<?php

namespace OdooJson2\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class KeyName implements OdooAttribute
{
    public function __construct()
    {
    }
}

