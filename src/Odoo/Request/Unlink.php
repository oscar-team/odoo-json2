<?php

namespace OdooJson2\Odoo\Request;

class Unlink extends Request
{
    public function __construct(
        string $model,
        protected array $ids,
    ) {
        parent::__construct($model, 'unlink');
    }

    public function toArray(): array
    {
        return [
            'ids' => $this->ids,
        ];
    }
}

