<?php

namespace OdooJson2\Odoo\Endpoint;

use OdooJson2\Json2\Client;
use OdooJson2\Odoo\Config;

class Endpoint
{
    protected string $service;

    private ?Client $client = null;

    public function __construct(private Config $config)
    {
    }

    public function getClient(bool $fresh = false): Client
    {
        // Always create a new client when running in CLI mode
        if ($fresh || null == $this->client || php_sapi_name() === 'cli') {
            // In CLI mode, don't cache the client to prevent connection pool exhaustion
            if (php_sapi_name() === 'cli') {
                return new Client(
                    $this->getConfig()->getHost(),
                    $this->getConfig()->getApiKey(),
                    $this->getConfig()->getDatabase(),
                    $this->getConfig()->getSslVerify()
                );
            }

            // In non-CLI mode, cache the client as before
            $this->client = new Client(
                $this->getConfig()->getHost(),
                $this->getConfig()->getApiKey(),
                $this->getConfig()->getDatabase(),
                $this->getConfig()->getSslVerify()
            );
        }
        return $this->client;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}

