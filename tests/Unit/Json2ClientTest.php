<?php

namespace OdooJson2\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use OdooJson2\Json2\Client as Json2Client;
use OdooJson2\Tests\TestCase;

class Json2ClientTest extends TestCase
{
    public function testClientInitialization(): void
    {
        $client = new Json2Client('https://test.odoo.com', 'test-api-key');
        $this->assertInstanceOf(Json2Client::class, $client);
    }

    public function testClientWithDatabase(): void
    {
        $client = new Json2Client('https://test.odoo.com', 'test-api-key', 'test-db');
        $this->assertInstanceOf(Json2Client::class, $client);
    }

    public function testBuildUrl(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['result' => [1, 2, 3]]))
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler, 'base_uri' => 'https://test.odoo.com']);

        // We can't easily test the private buildUrl, but we can test the call method
        $client = new Json2Client('https://test.odoo.com', 'test-api-key');
        $reflection = new \ReflectionClass($client);
        $httpClientProperty = $reflection->getProperty('client');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($client, $guzzleClient);

        $result = $client->call('res.partner', 'search', ['domain' => []]);
        $this->assertIsArray($result);
    }

    public function testClientCallWithSuccessResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([1, 2, 3]))
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler, 'base_uri' => 'https://test.odoo.com']);

        $client = new Json2Client('https://test.odoo.com', 'test-api-key');
        $reflection = new \ReflectionClass($client);
        $httpClientProperty = $reflection->getProperty('client');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($client, $guzzleClient);

        $result = $client->call('res.partner', 'search', ['domain' => []]);
        $this->assertEquals([1, 2, 3], $result);
    }

    public function testClientCallWithErrorResponse(): void
    {
        $this->expectException(\OdooJson2\Exceptions\OdooException::class);

        $mock = new MockHandler([
            new Response(400, [], json_encode(['error' => ['message' => 'Test error']]))
        ]);

        $handler = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handler, 'base_uri' => 'https://test.odoo.com']);

        $client = new Json2Client('https://test.odoo.com', 'test-api-key');
        $reflection = new \ReflectionClass($client);
        $httpClientProperty = $reflection->getProperty('client');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($client, $guzzleClient);

        $client->call('res.partner', 'search', ['domain' => []]);
    }
}

