<?php

namespace OdooJson2\Odoo\Request;

class Create extends Request
{
    public function __construct(
        string $model,
        protected array $values,
    ) {
        parent::__construct($model, 'create');
    }

    public function toArray(): array
    {
        // JSON-2 API expects vals_list parameter
        // Check if values is a single record (associative array) or multiple records (array of arrays)
        $valsList = $this->values;
        
        // Check if this is a single record (associative array with string keys)
        // vs multiple records (array of arrays)
        $firstKey = array_key_first($valsList);
        // If first key is a string (associative array), it's a single record - wrap it
        if (is_string($firstKey)) {
            $valsList = [$valsList];
        }
        
        return [
            'vals_list' => $valsList,
        ];
    }
}

