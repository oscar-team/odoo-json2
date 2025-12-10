<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Config;
use OdooJson2\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function testConfigCreation(): void
    {
        $config = new Config(
            database: 'test_db',
            host: 'https://test.odoo.com',
            apiKey: 'test-api-key'
        );

        $this->assertEquals('test_db', $config->getDatabase());
        $this->assertEquals('https://test.odoo.com', $config->getHost());
        $this->assertEquals('test-api-key', $config->getApiKey());
        $this->assertTrue($config->getSslVerify());
    }

    public function testConfigWithOptionalParameters(): void
    {
        $config = new Config(
            database: 'test_db',
            host: 'https://test.odoo.com',
            apiKey: 'test-api-key',
            sslVerify: false,
            fixedUserId: 123
        );

        $this->assertFalse($config->getSslVerify());
        $this->assertEquals(123, $config->getFixedUserId());
    }

    public function testConfigWithNullDatabase(): void
    {
        $config = new Config(
            database: null,
            host: 'https://test.odoo.com',
            apiKey: 'test-api-key'
        );

        $this->assertNull($config->getDatabase());
    }
}

