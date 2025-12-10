<?php

namespace OdooJson2\Examples\Models;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\HasMany;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\KeyName;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('res.partner')]
class Partner extends OdooModel
{
    #[Field]
    public string $name;

    #[Field('display_name')]
    public ?string $displayName;

    #[Field('email')]
    public ?string $email;

    #[Field('phone')]
    public ?string $phone;

    #[Field('street')]
    public ?string $street;

    #[Field('city')]
    public ?string $city;

    #[Field('zip')]
    public ?string $zip;

    #[Field('country_id'), Key]
    public ?int $countryId;

    #[Field('country_id'), KeyName]
    public ?string $countryName;

    #[Field('is_company')]
    public bool $isCompany = false;

    #[Field('active')]
    public bool $active = true;

    #[Field('parent_id'), Key]
    public ?int $parentId;

    #[Field('parent_id'), BelongsTo(name: 'parent_id', class: Partner::class)]
    public ?Partner $parent;

    #[Field('child_ids'), HasMany(class: Partner::class, name: 'child_ids')]
    public ?array $children;

    public function getParent(): ?Partner
    {
        if ($this->parentId) {
            return Partner::find($this->parentId);
        }
        return null;
    }

    public function getChildren(): array
    {
        if ($this->children) {
            return Partner::read($this->children);
        }
        return [];
    }
}

