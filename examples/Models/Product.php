<?php

namespace OdooJson2\Examples\Models;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\KeyName;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('product.product')]
class Product extends OdooModel
{
    #[Field]
    public string $name;

    #[Field('default_code')]
    public ?string $defaultCode;

    #[Field('list_price')]
    public ?float $listPrice;

    #[Field('standard_price')]
    public ?float $standardPrice;

    #[Field('categ_id'), Key]
    public ?int $categoryId;

    #[Field('categ_id'), KeyName]
    public ?string $categoryName;

    #[Field('sale_ok')]
    public bool $saleOk = true;

    #[Field('purchase_ok')]
    public bool $purchaseOk = true;

    #[Field('active')]
    public bool $active = true;

    #[Field('type')]
    public ?string $type;

    #[Field('categ_id'), BelongsTo(name: 'categ_id', class: ProductCategory::class)]
    public ?ProductCategory $category;
}

