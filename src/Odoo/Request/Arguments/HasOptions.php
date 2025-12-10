<?php

namespace OdooJson2\Odoo\Request\Arguments;

trait HasOptions
{
    protected Options $options;

    public function option(string $key, $value): static
    {
        $this->options->setRaw($key, $value);
        return $this;
    }
}

