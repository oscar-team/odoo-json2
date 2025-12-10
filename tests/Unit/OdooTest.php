<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Tests\TestCase;

class OdooTest extends TestCase
{
    public function testOdooCreation(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $odoo = new Odoo($config, $context);

        $this->assertInstanceOf(Odoo::class, $odoo);
    }

    public function testOdooConnect(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $odoo = new Odoo($config, $context);

        // connect() should not throw an exception
        $odoo->connect();
        $this->assertTrue(true);
    }

    public function testOdooModel(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $odoo = new Odoo($config, $context);
        $odoo->connect();

        $builder = $odoo->model('res.partner');
        $this->assertInstanceOf(\OdooJson2\Odoo\Request\RequestBuilder::class, $builder);
    }

    public function testOdooSearch(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $odoo = new Odoo($config, $context);
        $odoo->connect();

        $domain = new Domain();
        $domain->where('active', '=', true);

        // This will fail without a real Odoo instance, but we can test the method exists
        $this->expectException(\OdooJson2\Exceptions\OdooException::class);
        $odoo->search('res.partner', $domain);
    }

    public function testOdooCount(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $odoo = new Odoo($config, $context);
        $odoo->connect();

        $domain = new Domain();
        $domain->where('active', '=', true);

        // This will fail without a real Odoo instance, but we can test the method exists
        $this->expectException(\OdooJson2\Exceptions\OdooException::class);
        $odoo->count('res.partner', $domain);
    }
}

