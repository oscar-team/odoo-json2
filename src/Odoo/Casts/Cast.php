<?php

namespace OdooJson2\Odoo\Casts;

abstract class Cast
{
    const WILDCARD = '*';

    public function applies($value): bool
    {
        return true;
    }

    public function handlesNullValues(): bool
    {
        return true;
    }

    public abstract function getType(): string;

    public abstract function cast($raw);

    public abstract function uncast($value);
}

