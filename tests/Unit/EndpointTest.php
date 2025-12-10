<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\Endpoint\Endpoint;
use OdooJson2\Odoo\Endpoint\ObjectEndpoint;
use OdooJson2\Tests\TestCase;

class EndpointTest extends TestCase
{
    public function testEndpointCreation(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $endpoint = new class($config) extends Endpoint {
            protected string $service = 'test';
        };

        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals($config, $endpoint->getConfig());
    }

    public function testObjectEndpointCreation(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $endpoint = new ObjectEndpoint($config, $context, 1);

        $this->assertInstanceOf(ObjectEndpoint::class, $endpoint);
    }
}

