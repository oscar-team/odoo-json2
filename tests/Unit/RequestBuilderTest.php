<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\Endpoint\ObjectEndpoint;
use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Odoo\Request\RequestBuilder;
use OdooJson2\Tests\TestCase;

class RequestBuilderTest extends TestCase
{
    private ObjectEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $this->endpoint = new ObjectEndpoint($config, $context, 1);
    }

    public function testRequestBuilderCreation(): void
    {
        $builder = new RequestBuilder($this->endpoint, 'res.partner', new Domain());
        $this->assertInstanceOf(RequestBuilder::class, $builder);
    }

    public function testRequestBuilderWhere(): void
    {
        $builder = new RequestBuilder($this->endpoint, 'res.partner', new Domain());
        $builder->where('active', '=', true);
        
        $this->assertInstanceOf(RequestBuilder::class, $builder);
    }

    public function testRequestBuilderLimit(): void
    {
        $builder = new RequestBuilder($this->endpoint, 'res.partner', new Domain());
        $builder->limit(10);
        
        $this->assertInstanceOf(RequestBuilder::class, $builder);
    }

    public function testRequestBuilderOffset(): void
    {
        $builder = new RequestBuilder($this->endpoint, 'res.partner', new Domain());
        $builder->offset(5);
        
        $this->assertInstanceOf(RequestBuilder::class, $builder);
    }

    public function testRequestBuilderOrderBy(): void
    {
        $builder = new RequestBuilder($this->endpoint, 'res.partner', new Domain());
        $builder->orderBy('name');
        
        $this->assertInstanceOf(RequestBuilder::class, $builder);
    }
}

