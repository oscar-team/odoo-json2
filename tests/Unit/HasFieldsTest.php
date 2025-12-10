<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\HasMany;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\OdooModel;
use OdooJson2\Tests\TestCase;

class HasFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $config = new Config('test_db', 'https://test.odoo.com', 'test-api-key');
        $context = new Context();
        $odoo = new Odoo($config, $context);
        OdooModel::boot($odoo);
    }

    public function testHydrateSimpleModel(): void
    {
        $model = new class extends OdooModel {
            #[Field('name')]
            public string $name;
        };

        $response = ['id' => 1, 'name' => 'Test'];
        $instance = $model::hydrate($response);

        $this->assertEquals(1, $instance->id);
        $this->assertEquals('Test', $instance->name);
    }

    public function testHydrateWithKey(): void
    {
        $model = new class extends OdooModel {
            #[Field('partner_id')]
            #[Key]
            public ?int $partnerId;
        };

        $response = ['id' => 1, 'partner_id' => [5, 'Partner Name']];
        $instance = $model::hydrate($response);

        $this->assertEquals(5, $instance->partnerId);
    }

    public function testDehydrateSimpleModel(): void
    {
        $model = new class extends OdooModel {
            #[Field('name')]
            public string $name;
        };

        $instance = new $model();
        $instance->id = 1;
        $instance->name = 'Test';

        $dehydrated = $model::dehydrate($instance);
        $array = (array) $dehydrated;

        // Dehydrate may not include id if it's not a field
        $this->assertEquals('Test', $array['name']);
    }

    public function testDehydrateWithKey(): void
    {
        $model = new class extends OdooModel {
            #[Field('partner_id')]
            #[Key]
            public ?int $partnerId;
        };

        $instance = new $model();
        $instance->id = 1;
        $instance->partnerId = 5;

        $dehydrated = $model::dehydrate($instance);
        $array = (array) $dehydrated;

        $this->assertEquals(5, $array['partner_id']);
    }
}

