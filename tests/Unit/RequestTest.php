<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Odoo\Request\Arguments\Options;
use OdooJson2\Odoo\Request\Create;
use OdooJson2\Odoo\Request\Read;
use OdooJson2\Odoo\Request\Search;
use OdooJson2\Odoo\Request\SearchRead;
use OdooJson2\Odoo\Request\Write;
use OdooJson2\Odoo\Request\Unlink;
use OdooJson2\Odoo\Request\FieldsGet;
use OdooJson2\Tests\TestCase;

class RequestTest extends TestCase
{
    public function testSearchRequest(): void
    {
        $domain = new Domain();
        $domain->where('active', '=', true);

        $request = new Search('res.partner', $domain, offset: 0, limit: 10);

        $array = $request->toArray();
        $this->assertArrayHasKey('domain', $array);
        $this->assertEquals(10, $array['limit']);
    }

    public function testReadRequest(): void
    {
        $request = new Read('res.partner', [1, 2, 3], ['name', 'email']);

        $array = $request->toArray();
        $this->assertEquals([1, 2, 3], $array['ids']);
        $this->assertEquals(['name', 'email'], $array['fields']);
    }

    public function testSearchReadRequest(): void
    {
        $domain = new Domain();
        $domain->where('active', '=', true);

        $request = new SearchRead('res.partner', $domain, ['name', 'email'], offset: 0, limit: 5);

        $array = $request->toArray();
        $this->assertArrayHasKey('domain', $array);
        $this->assertEquals(['name', 'email'], $array['fields']);
        $this->assertEquals(5, $array['limit']);
    }

    public function testCreateRequest(): void
    {
        $request = new Create('res.partner', [
            'name' => 'Test',
            'email' => 'test@example.com'
        ]);

        $array = $request->toArray();
        $this->assertArrayHasKey('vals_list', $array);
        $this->assertIsArray($array['vals_list']);
    }

    public function testCreateRequestWithMultipleRecords(): void
    {
        $request = new Create('res.partner', [
            ['name' => 'Test 1'],
            ['name' => 'Test 2']
        ]);

        $array = $request->toArray();
        $this->assertArrayHasKey('vals_list', $array);
        $this->assertCount(2, $array['vals_list']);
    }

    public function testWriteRequest(): void
    {
        $request = new Write('res.partner', [1, 2], [
            'email' => 'new@example.com'
        ]);

        $array = $request->toArray();
        $this->assertEquals([1, 2], $array['ids']);
        $this->assertArrayHasKey('vals', $array);
        $this->assertEquals('new@example.com', $array['vals']['email']);
    }

    public function testUnlinkRequest(): void
    {
        $request = new Unlink('res.partner', [1, 2, 3]);

        $array = $request->toArray();
        $this->assertEquals([1, 2, 3], $array['ids']);
    }

    public function testFieldsGetRequest(): void
    {
        $request = new FieldsGet('res.partner', null, ['type', 'required']);

        $array = $request->toArray();
        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals(['type', 'required'], $array['attributes']);
    }
}

