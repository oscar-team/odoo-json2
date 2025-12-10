<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\OdooModel;
use OdooJson2\Tests\TestCase;

class OdooModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock Odoo instance for testing
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $odoo = new Odoo($config);
        OdooModel::boot($odoo);
    }

    public function testModelBoot(): void
    {
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $odoo = new Odoo($config);
        
        OdooModel::boot($odoo);
        $this->assertTrue(true); // If no exception, boot worked
    }

    public function testModelExists(): void
    {
        $model = new class extends OdooModel {
            public int $id = 1;
        };

        $this->assertTrue($model->exists());
    }

    public function testModelNotExists(): void
    {
        $model = new class extends OdooModel {
            public int $id;
        };

        $this->assertFalse($model->exists());
    }

    public function testModelFill(): void
    {
        $model = new class extends OdooModel {
            public string $name;
            public ?string $email;
        };

        $model->fill([
            'name' => 'Test',
            'email' => 'test@example.com'
        ]);

        $this->assertEquals('Test', $model->name);
        $this->assertEquals('test@example.com', $model->email);
    }

    public function testModelFillThrowsExceptionForUndefinedProperty(): void
    {
        $this->expectException(\OdooJson2\Exceptions\UndefinedPropertyException::class);

        $model = new class extends OdooModel {
            public string $name;
        };

        $model->fill([
            'name' => 'Test',
            'undefined_property' => 'value'
        ]);
    }
}

