<?php

namespace OdooJson2\Odoo\Request;

use OdooJson2\Odoo\Request\Arguments\Domain;

class ReadGroup extends Request
{
    public function __construct(
        string $model,
        protected array $groupBy,
        protected ?Domain $domain = null,
        protected ?array $fields = null,
        protected int $offset = 0,
        protected ?int $limit = null,
        protected ?string $order = null,
    ) {
        parent::__construct($model, 'read_group');
    }

    public function toArray(): array
    {
        $params = [
            'groupby' => $this->groupBy,
        ];

        if ($this->domain !== null) {
            $params['domain'] = $this->domain->toArray();
        }

        if ($this->fields !== null) {
            $params['fields'] = $this->fields;
        }

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

