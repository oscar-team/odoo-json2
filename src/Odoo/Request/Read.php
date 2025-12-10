<?php

namespace OdooJson2\Odoo\Request;

class Read extends Request
{
    public function __construct(
        string $model,
        protected array $ids,
        protected array $fields = [],
    ) {
        parent::__construct($model, 'read');
    }

    public function toArray(): array
    {
        $params = [
            'ids' => $this->ids,
        ];

        if (!empty($this->fields)) {
            $params['fields'] = $this->fields;
        }

        return $params;
    }
}

