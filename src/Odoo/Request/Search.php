<?php

namespace OdooJson2\Odoo\Request;

use OdooJson2\Odoo\Request\Arguments\Domain;

class Search extends Request
{
    public function __construct(
        string $model,
        protected Domain $domain,
        protected int $offset = 0,
        protected ?int $limit = null,
        protected ?string $order = null,
        protected bool $count = false,
    ) {
        parent::__construct($model, $count ? 'search_count' : 'search');
    }

    public function toArray(): array
    {
        if ($this->count) {
            return [
                'domain' => $this->domain->toArray(),
            ];
        }

        $params = [
            'domain' => $this->domain->toArray(),
        ];

        if ($this->offset > 0) {
            $params['offset'] = $this->offset;
        }

        if ($this->limit !== null) {
            $params['limit'] = $this->limit;
        }

        if ($this->order !== null) {
            $params['order'] = $this->order;
        }

        return $params;
    }
}

