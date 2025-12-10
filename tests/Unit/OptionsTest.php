<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\Request\Arguments\Options;
use OdooJson2\Tests\TestCase;

class OptionsTest extends TestCase
{
    public function testOptionsCreation(): void
    {
        $options = new Options();
        $array = $options->toArray();
        $this->assertIsArray($array);
    }

    public function testOptionsWithContextInConstructor(): void
    {
        $context = new Context(lang: 'en_US');
        $options = new Options([], $context);

        $array = $options->toArray();
        $this->assertArrayHasKey('context', $array);
        $this->assertEquals('en_US', $array['context']['lang']);
    }

    public function testOptionsSetRaw(): void
    {
        $options = new Options();
        $options->setRaw('custom_key', 'custom_value');

        $array = $options->toArray();
        $this->assertEquals('custom_value', $array['custom_key']);
    }

    public function testOptionsLimit(): void
    {
        $options = new Options();
        $options->limit(10);

        $array = $options->toArray();
        $this->assertEquals(10, $array['limit']);
    }

    public function testOptionsOffset(): void
    {
        $options = new Options();
        $options->offset(5);

        $array = $options->toArray();
        $this->assertEquals(5, $array['offset']);
    }

    public function testOptionsWithContextMethod(): void
    {
        $context = new Context(lang: 'en_US');
        $options = new Options();
        $options->withContext($context);

        $array = $options->toArray();
        $this->assertArrayHasKey('context', $array);
    }
}

