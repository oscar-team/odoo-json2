<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Endpoint\CommonEndpoint;
use OdooJson2\Tests\TestCase;

class CommonEndpointTest extends TestCase
{
    public function testCommonEndpointCreation(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $endpoint = new CommonEndpoint($config);

        $this->assertInstanceOf(CommonEndpoint::class, $endpoint);
    }

    public function testAuthenticate(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $endpoint = new CommonEndpoint($config);

        $uid = $endpoint->authenticate();
        $this->assertIsInt($uid);
        $this->assertEquals(1, $uid);
    }

    public function testAuthenticateWithFixedUserId(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key', sslVerify: true, fixedUserId: 123);
        $endpoint = new CommonEndpoint($config);

        $uid = $endpoint->authenticate();
        $this->assertEquals(123, $uid);
    }
}

