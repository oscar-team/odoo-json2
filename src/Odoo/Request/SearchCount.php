<?php

namespace OdooJson2\Odoo\Request;

use OdooJson2\Odoo\Request\Arguments\Domain;

class SearchCount extends Request
{
    public function __construct(
        string $model,
        protected Domain $domain,
    ) {
        parent::__construct($model, 'search_count');
    }

    public function toArray(): array
    {
        return [
            'domain' => $this->domain->toArray(),
        ];
    }
}

