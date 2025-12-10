<?php

namespace OdooJson2\Attributes;

use Attribute;

#[Attribute]
class BelongsTo
{
    public function __construct(
        public string $name,
        public string $class
    ) {
    }
}

