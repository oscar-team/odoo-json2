<?php

namespace OdooJson2\Odoo\Mapping;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\HasMany;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\KeyName;
use OdooJson2\Odoo\Casts\CastHandler;
use OdooJson2\Odoo\OdooModel;
use stdClass;

trait HasFields
{
    protected static function fieldNames(): array
    {
        $fieldNames = [];

        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Field::class);
            $attributes += $property->getAttributes(HasMany::class);
            $attributes += $property->getAttributes(BelongsTo::class);

            foreach ($attributes as $attribute) {
                $fieldNames[] = $attribute->newInstance()->name ?? $property->name;
            }
        }
        return $fieldNames;
    }

    public static function hydrate(array $response): static
    {
        $castsExists = CastHandler::hasCasts();

        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();

        $instance = static::newInstance();
        $instance->id = $response['id'] ?? null;

        foreach ($properties as $property) {
            // Skip properties that are relations (BelongsTo, HasMany) - they'll be handled separately
            $hasBelongsTo = !empty($property->getAttributes(BelongsTo::class));
            $hasHasMany = !empty($property->getAttributes(HasMany::class));
            
            if ($hasBelongsTo || $hasHasMany) {
                continue;
            }
            
            $isKey = !empty($property->getAttributes(Key::class));
            $isKeyName = !empty($property->getAttributes(KeyName::class));
            $attributes = $property->getAttributes(Field::class);

            foreach ($attributes as $attribute) {
                $field = $attribute->newInstance()->name ?? $property->name;
                if (isset($response[$field])) {
                    if ($isKey) {
                        $value = is_array($response[$field]) ? ($response[$field][0] ?? null) : $response[$field];
                    } elseif ($isKeyName) {
                        $value = is_array($response[$field]) ? ($response[$field][1] ?? null) : null;
                    } else {
                        $value = $response[$field];
                    }
                    $instance->{$property->name} = $castsExists ? CastHandler::cast($property, $value) : $value;
                }
            }
        }

        // Handle relations
        foreach ($properties as $property) {
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if ($attributeInstance instanceof HasMany) {
                    $foreignKey = $attributeInstance->name;
                    if (isset($response[$foreignKey]) && is_array($response[$foreignKey])) {
                        $ids = $response[$foreignKey];
                        if (!empty($ids)) {
                            /** @var OdooModel $relatedModelClass */
                            $relatedModelClass = $attributeInstance->class;
                            $instance->{$property->name} = new LazyHasMany($relatedModelClass, 'read', [$ids]);
                        } else {
                            $instance->{$property->name} = [];
                        }
                    } elseif ($property->getType()?->allowsNull() ?? true) {
                        $instance->{$property->name} = null;
                    } else {
                        if ($property->getType() && $property->getType()->getName() === 'array') {
                            $instance->{$property->name} = [];
                        }
                    }
                } elseif ($attributeInstance instanceof BelongsTo) {
                    $foreignKey = $attributeInstance->name;
                    if (isset($response[$foreignKey])) {
                        $foreignValue = $response[$foreignKey];
                        $id = null;
                        if (is_array($foreignValue) && count($foreignValue) > 0) {
                            $id = $foreignValue[0];
                        } elseif (is_int($foreignValue)) {
                            $id = $foreignValue;
                        }

                        if ($id !== null) {
                            /** @var OdooModel $relatedModelClass */
                            $relatedModelClass = $attributeInstance->class;
                            $instance->{$property->name} = $relatedModelClass::find($id);
                        } elseif ($property->getType()?->allowsNull() ?? true) {
                            $instance->{$property->name} = null;
                        }
                    } elseif ($property->getType()?->allowsNull() ?? true) {
                        $instance->{$property->name} = null;
                    }
                }
            }
        }

        return $instance;
    }

    public static function dehydrate(OdooModel $model): object
    {
        $castsExists = CastHandler::hasCasts();
        $item = new stdClass();

        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Field::class);

            foreach ($attributes as $attribute) {
                $field = $attribute->newInstance()->name ?? $property->name;
                if ($property->isInitialized($model)) {
                    $value = $model->{$property->name};

                    $isKey = !empty($property->getAttributes(Key::class)) || !empty($property->getAttributes(BelongsTo::class));
                    if ($isKey && is_array($value) && count($value) >= 1) {
                        $value = $value[0];
                    }

                    $item->{$field} = $castsExists ? CastHandler::uncast($property, $value) : $value;
                }
            }

            $hasManyRelations = $property->getAttributes(HasMany::class);
            foreach ($hasManyRelations as $attribute) {
                $field = $attribute->newInstance()->name ?? $property->name;
                if ($property->isInitialized($model)) {
                    $values = $model->{$property->name};
                    if (null === $values) {
                        continue;
                    }

                    if ($values instanceof LazyHasMany && !$values->isLoaded()) {
                        continue;
                    }

                    if (self::isIdArray($values)) {
                        $item->{$field} = [[6, 0, $values]];
                    } else {
                        $commands = [];
                        foreach ($values as $value) {
                            if ($value instanceof OdooModel) {
                                if ($value->exists()) {
                                    $dehydratedValue = self::dehydrateRelatedModel($value);
                                    $commands[] = [1, $value->id, $dehydratedValue];
                                } else {
                                    $dehydratedValue = self::dehydrateRelatedModel($value);
                                    $commands[] = [0, 0, $dehydratedValue];
                                }
                            }
                        }
                        if (!empty($commands)) {
                            $item->{$field} = $commands;
                        }
                    }
                }
            }
        }

        return $item;
    }

    protected static function newInstance()
    {
        return new static();
    }

    private static function dehydrateRelatedModel(OdooModel $relatedModel): object
    {
        $reflectionClass = new \ReflectionClass($relatedModel);
        // Call static dehydrate method on the related model's class
        $dehydratedData = call_user_func([get_class($relatedModel), 'dehydrate'], $relatedModel);

        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $isKey = !empty($property->getAttributes(Key::class)) || !empty($property->getAttributes(BelongsTo::class));
            $fieldName = null;

            $fieldAttributes = $property->getAttributes(Field::class);
            foreach ($fieldAttributes as $attribute) {
                $fieldName = $attribute->newInstance()->name ?? $property->name;
                break;
            }

            if ($isKey && $fieldName && property_exists($dehydratedData, $fieldName)) {
                $value = $dehydratedData->{$fieldName};
                if (is_array($value) && count($value) >= 1 && is_int($value[0])) {
                    $dehydratedData->{$fieldName} = $value[0];
                }
            }
        }

        return $dehydratedData;
    }

    private static function isIdArray(array|\ArrayAccess $arr): bool
    {
        foreach ($arr as $item) {
            if (!is_int($item)) {
                return false;
            }
        }
        return true;
    }
}

