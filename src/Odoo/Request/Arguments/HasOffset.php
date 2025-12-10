<?php

namespace OdooJson2\Odoo\Request\Arguments;

trait HasOffset
{
    protected int $offset = 0;

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }
}

