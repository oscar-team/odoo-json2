<?php

namespace OdooJson2\Attributes;

use Attribute;

#[Attribute]
class Model
{
    public function __construct(
        public string $name
    ) {
    }
}

