<?php

namespace OdooJson2\Odoo;

use OdooJson2\Attributes\Model;
use OdooJson2\Exceptions\ConfigurationException;
use OdooJson2\Exceptions\OdooModelException;
use OdooJson2\Exceptions\UndefinedPropertyException;
use OdooJson2\Odoo\Mapping\HasFields;
use OdooJson2\Odoo;

class OdooModel
{
    use HasFields;

    private static Odoo $odoo;
    private static ?string $model = null;

    public static function boot(Odoo $odoo)
    {
        self::$odoo = $odoo;
    }

    public static function listFields(?array $fields = null): object
    {
        return self::$odoo->fieldsGet(static::model(), $fields);
    }

    public static function find(int $id): ?static
    {
        $odooInstance = self::$odoo->find(static::model(), $id, static::fieldNames());
        if (null === $odooInstance) {
            return null;
        }
        // Convert array to object-like structure for hydration
        return static::hydrate($odooInstance);
    }

    public static function read(array $ids): array
    {
        $results = self::$odoo->read(static::model(), $ids, static::fieldNames());
        return array_map(fn($item) => static::hydrate($item), $results);
    }

    protected static function model(): string
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $model = $reflectionClass->getAttributes(Model::class)[0] ?? throw new ConfigurationException("Missing Model Attribute");

        return $model->newInstance()->name;
    }

    public static function query()
    {
        return new Odoo\Models\ModelQuery(static::newInstance(), self::$odoo->model(static::model())->fields(static::fieldNames()));
    }

    public static function all()
    {
        return static::query()->get();
    }

    public int $id;

    public function exists(): bool
    {
        return isset($this->id);
    }

    public function save(): static
    {
        $dehydrated = static::dehydrate($this);
        $data = (array) $dehydrated;

        if ($this->exists()) {
            $updateResponse = self::$odoo->write(static::model(), [$this->id], $data);
            if (false === $updateResponse) {
                throw new OdooModelException("Failed to update model");
            }
        } else {
            $createResponse = self::$odoo->create(static::model(), $data);
            if (false === $createResponse) {
                throw new OdooModelException("Failed to create model");
            }
            $this->id = is_array($createResponse) ? $createResponse[0] : $createResponse;
        }

        return $this;
    }

    public function fill(iterable $properties)
    {
        $reflectionClass = new \ReflectionClass(static::class);

        foreach ($properties as $name => $value) {
            if ($reflectionClass->hasProperty($name)) {
                $this->{$name} = $value;
            } else {
                throw new UndefinedPropertyException("Property $name not defined");
            }
        }

        return $this;
    }

    public function equals(OdooModel $model): bool
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if ($property->isInitialized($this)) {
                if (!$property->isInitialized($model)) {
                    return false;
                }
                if ($this->{$property->name} !== $model->{$property->name}) {
                    return false;
                }
            } else {
                if ($property->isInitialized($model)) {
                    return false;
                }
            }
        }
        return true;
    }
}

