<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Context;
use OdooJson2\Tests\TestCase;

class ContextTest extends TestCase
{
    public function testContextCreation(): void
    {
        $context = new Context(
            lang: 'en_US',
            timezone: 'UTC',
            companyId: 1
        );

        $array = $context->toArray();
        $this->assertEquals('en_US', $array['lang']);
        $this->assertEquals('UTC', $array['tz']);
        $this->assertEquals(1, $array['company_id']);
    }

    public function testContextToArray(): void
    {
        $context = new Context(
            lang: 'en_US',
            timezone: 'UTC',
            companyId: 1
        );

        $array = $context->toArray();
        $this->assertEquals('en_US', $array['lang']);
        $this->assertEquals('UTC', $array['tz']);
        $this->assertEquals(1, $array['company_id']);
    }

    public function testContextWithCustomArgs(): void
    {
        $context = new Context();
        $context->setContextArg('custom_key', 'custom_value');

        $array = $context->toArray();
        $this->assertEquals('custom_value', $array['custom_key']);
    }

    public function testContextClone(): void
    {
        $context = new Context(lang: 'en_US');
        $cloned = $context->clone();

        $this->assertNotSame($context, $cloned);
        $clonedArray = $cloned->toArray();
        $this->assertEquals('en_US', $clonedArray['lang']);
    }

    public function testContextSetDefaults(): void
    {
        $baseContext = new Context(lang: 'en_US', timezone: 'UTC');
        $newContext = new Context();
        $newContext->setDefaults($baseContext);

        $array = $newContext->toArray();
        $this->assertEquals('en_US', $array['lang']);
        $this->assertEquals('UTC', $array['tz']);
    }
}

