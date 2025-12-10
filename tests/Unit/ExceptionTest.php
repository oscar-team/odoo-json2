<?php

namespace OdooJson2\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use OdooJson2\Exceptions\OdooException;
use OdooJson2\Exceptions\OdooModelException;
use OdooJson2\Exceptions\UndefinedPropertyException;
use OdooJson2\Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testOdooException(): void
    {
        $exception = new OdooException(null, 'Test error', 500);
        
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function testOdooExceptionWithResponse(): void
    {
        $response = new Response(400, [], '{"error": "Bad request"}');
        $exception = new OdooException($response, 'Test error', 400);
        
        // OdooException stores response but doesn't expose it via getter
        // We can test that it was constructed without error
        $this->assertInstanceOf(OdooException::class, $exception);
    }

    public function testOdooModelException(): void
    {
        $exception = new OdooModelException('Model error');
        
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Model error', $exception->getMessage());
    }

    public function testUndefinedPropertyException(): void
    {
        $exception = new UndefinedPropertyException('Property not found');
        
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Property not found', $exception->getMessage());
    }
}

