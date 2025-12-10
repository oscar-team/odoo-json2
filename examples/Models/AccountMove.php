<?php

namespace OdooJson2\Examples\Models;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\HasMany;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\KeyName;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('account.move')]
class AccountMove extends OdooModel
{
    #[Field('name')]
    public string $name;

    #[Field('move_type')]
    public ?string $moveType;

    #[Field('state')]
    public ?string $state;

    #[Field('date')]
    public ?string $date;

    #[Field('partner_id'), Key]
    public ?int $partnerId;

    #[Field('partner_id'), KeyName]
    public ?string $partnerName;

    #[Field('amount_total')]
    public ?float $amountTotal;

    #[Field('amount_residual')]
    public ?float $amountResidual;

    #[Field('partner_id'), BelongsTo(name: 'partner_id', class: Partner::class)]
    public ?Partner $partner;

    #[Field('invoice_line_ids'), HasMany(class: AccountMoveLine::class, name: 'invoice_line_ids')]
    public ?array $invoiceLines;
}

