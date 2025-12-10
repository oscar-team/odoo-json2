<?php

namespace OdooJson2\Tests\Unit;

use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Tests\TestCase;

class DomainTest extends TestCase
{
    public function testDomainCreation(): void
    {
        $domain = new Domain();
        $this->assertTrue($domain->isEmpty());
    }

    public function testDomainWhere(): void
    {
        $domain = new Domain();
        $domain->where('name', '=', 'Test');

        $this->assertFalse($domain->isEmpty());
        $array = $domain->toArray();
        $this->assertEquals([['name', '=', 'Test']], $array);
    }

    public function testDomainMultipleWhere(): void
    {
        $domain = new Domain();
        $domain->where('name', '=', 'Test');
        $domain->where('active', '=', true);

        $array = $domain->toArray();
        $this->assertCount(2, $array);
        $this->assertEquals([['name', '=', 'Test'], ['active', '=', true]], $array);
    }

    public function testDomainOrWhere(): void
    {
        $domain = new Domain();
        $domain->where('name', '=', 'Test');
        $domain->orWhere('email', '=', 'test@example.com');

        $array = $domain->toArray();
        $this->assertContains('|', $array);
    }

    public function testDomainOrWhereThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(\RuntimeException::class);
        $domain = new Domain();
        $domain->orWhere('name', '=', 'Test');
    }
}

