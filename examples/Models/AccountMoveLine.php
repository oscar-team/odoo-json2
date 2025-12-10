<?php

namespace OdooJson2\Examples\Models;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('account.move.line')]
class AccountMoveLine extends OdooModel
{
    #[Field('name')]
    public string $name;

    #[Field('move_id'), Key]
    public ?int $moveId;

    #[Field('product_id'), Key]
    public ?int $productId;

    #[Field('quantity')]
    public ?float $quantity;

    #[Field('price_unit')]
    public ?float $priceUnit;

    #[Field('price_subtotal')]
    public ?float $priceSubtotal;

    #[Field('move_id'), BelongsTo(name: 'move_id', class: AccountMove::class)]
    public ?AccountMove $move;

    #[Field('product_id'), BelongsTo(name: 'product_id', class: Product::class)]
    public ?Product $product;
}

