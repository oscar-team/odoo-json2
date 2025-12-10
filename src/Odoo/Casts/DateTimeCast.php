<?php

namespace OdooJson2\Odoo\Casts;

use DateTime;

class DateTimeCast extends Cast
{
    public function getType(): string
    {
        return DateTime::class;
    }

    public function cast($raw)
    {
        if (empty($raw)) {
            return null;
        }
        return new DateTime($raw);
    }

    public function uncast($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}

