<?php

namespace OdooJson2\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Key implements OdooAttribute
{
    public function __construct()
    {
    }
}

