<?php

namespace OdooJson2\Attributes;

use Attribute;

#[Attribute]
class HasMany
{
    public function __construct(
        public string $class,
        public string $name
    ) {
    }
}

