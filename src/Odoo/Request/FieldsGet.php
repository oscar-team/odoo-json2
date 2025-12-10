<?php

namespace OdooJson2\Odoo\Request;

class FieldsGet extends Request
{
    public function __construct(
        string $model,
        protected ?array $fields = null,
        protected ?array $attributes = null,
    ) {
        parent::__construct($model, 'fields_get');
    }

    public function toArray(): array
    {
        $params = [];

        // JSON-2 API fields_get may not accept 'fields' parameter
        // Only include attributes if provided
        if ($this->attributes !== null) {
            $params['attributes'] = $this->attributes;
        }

        // Note: 'fields' parameter is not supported in JSON-2 API fields_get
        // If you need to filter fields, you'll need to filter the response after receiving it

        return $params;
    }
}

