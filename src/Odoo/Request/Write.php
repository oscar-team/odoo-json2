<?php

namespace OdooJson2\Odoo\Request;

class Write extends Request
{
    public function __construct(
        string $model,
        protected array $ids,
        protected array $values,
    ) {
        parent::__construct($model, 'write');
    }

    public function toArray(): array
    {
        // JSON-2 API expects 'vals' instead of 'values'
        return [
            'ids' => $this->ids,
            'vals' => $this->values,
        ];
    }
}

