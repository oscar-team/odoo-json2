<?php

namespace OdooJson2\Odoo\Casts;

use DateTimeZone;
use DateTime;

class DateTimeTimezoneCast extends Cast
{
    public function __construct(private ?DateTimeZone $timezone = null)
    {
    }

    public function getType(): string
    {
        return DateTime::class;
    }

    public function cast($raw)
    {
        if (empty($raw)) {
            return null;
        }
        $dateTime = new DateTime($raw);
        if ($this->timezone) {
            $dateTime->setTimezone($this->timezone);
        }
        return $dateTime;
    }

    public function uncast($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}

