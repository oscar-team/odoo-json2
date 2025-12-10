<?php

namespace OdooJson2\Examples\Models;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('product.category')]
class ProductCategory extends OdooModel
{
    #[Field]
    public string $name;

    #[Field('parent_id'), Key]
    public ?int $parentId;

    #[Field('parent_id'), BelongsTo(name: 'parent_id', class: ProductCategory::class)]
    public ?ProductCategory $parent;
}

