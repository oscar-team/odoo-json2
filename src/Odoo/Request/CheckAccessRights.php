<?php

namespace OdooJson2\Odoo\Request;

class CheckAccessRights extends Request
{
    public function __construct(
        string $model,
        protected string $permission,
    ) {
        parent::__construct($model, 'check_access_rights');
    }

    public function toArray(): array
    {
        return [
            'operation' => $this->permission,
            'raise_exception' => false,
        ];
    }
}

