<?php

namespace OdooJson2\Odoo\Request;

use OdooJson2\Exceptions\ConfigurationException;
use OdooJson2\Odoo\Endpoint\ObjectEndpoint;
use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Odoo\Request\Arguments\HasDomain;
use OdooJson2\Odoo\Request\Arguments\HasFields;
use OdooJson2\Odoo\Request\Arguments\HasGroupBy;
use OdooJson2\Odoo\Request\Arguments\HasLimit;
use OdooJson2\Odoo\Request\Arguments\HasOffset;
use OdooJson2\Odoo\Request\Arguments\HasOptions;
use OdooJson2\Odoo\Request\Arguments\HasOrder;
use OdooJson2\Odoo\Request\Arguments\Options;

class RequestBuilder
{
    use HasDomain, HasOrder, HasOffset, HasLimit, HasFields, HasOptions, HasGroupBy;

    public function __construct(
        private ObjectEndpoint $endpoint,
        protected string $model,
        Domain $domain,
        ?Options $options = null
    ) {
        $this->domain = $domain;
        $this->options = $options ?? new Options();
    }

    public function can(string $permission): bool
    {
        return $this->endpoint->checkAccessRights($this->model, $permission, $this->options);
    }

    public function get(): array
    {
        if ($this->hasGroupBy()) {
            return $this->endpoint->readGroup(
                $this->model,
                groupBy: $this->groupBy,
                domain: $this->domain,
                fields: $this->fields,
                offset: $this->offset,
                limit: $this->limit,
                order: $this->getOrderString(),
                options: $this->options
            );
        }
        return $this->endpoint->searchRead(
            $this->model,
            domain: $this->domain,
            fields: $this->fields,
            offset: $this->offset,
            limit: $this->limit,
            order: $this->getOrderString(),
            options: $this->options
        );
    }

    public function collect(): iterable
    {
        if (!function_exists('collect')) {
            throw new ConfigurationException("collect is not defined. Are you missing Laravel framework?");
        }
        return collect($this->get());
    }

    public function first(): ?array
    {
        $this->limit = 1;
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function ids(): array
    {
        return $this->endpoint->search(
            $this->model,
            domain: $this->domain,
            offset: $this->offset,
            limit: $this->limit,
            order: $this->getOrderString(),
            options: $this->options
        );
    }

    public function count(): int
    {
        return $this->endpoint->count(
            $this->model,
            domain: $this->domain,
            offset: $this->offset,
            limit: $this->limit,
            order: $this->getOrderString(),
            options: $this->options
        );
    }

    public function delete(): bool
    {
        $ids = $this->ids();

        return $this->endpoint->unlink($this->model, $ids, $this->options);
    }

    public function create(array $values): bool|int|array
    {
        return $this->endpoint->create($this->model, $values, $this->options);
    }

    public function write(array $values): bool
    {
        $ids = $this->ids();

        return $this->endpoint->write($this->model, $ids, $values, $this->options);
    }

    public function update(array $values): bool
    {
        return $this->write($values);
    }
}

