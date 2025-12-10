<?php

namespace OdooJson2\Odoo\Request;

use OdooJson2\Json2\Client;
use OdooJson2\Odoo\Request\Arguments\Options;

abstract class Request
{
    public function __construct(
        protected string $model,
        protected string $method
    ) {
    }

    public abstract function toArray(): array;

    public function execute(
        Client $client,
        ?string $database,
        Options $options
    ) {
        $params = array_merge(
            $this->toArray(),
            $options->toArray()
        );
        
        return $client->call($this->model, $this->method, $params);
    }
}

