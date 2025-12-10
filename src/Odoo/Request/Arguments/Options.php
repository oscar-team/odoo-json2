<?php

namespace OdooJson2\Odoo\Request\Arguments;

use OdooJson2\Odoo\Context;

class Options
{
    public function __construct(private array $options = [], private ?Context $context = null)
    {
    }

    public function toArray(): array
    {
        $context = $this->context?->toArray() ?? [];
        if (empty($context)) {
            return $this->options;
        }
        return ['context' => $context] + $this->options;
    }

    public function withContext(Context $context): static
    {
        $this->context = $context->setDefaults($this->context);
        return $this;
    }

    public function setRaw(string $key, $value): static
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function limit(int $value): static
    {
        return $this->setRaw('limit', $value);
    }

    public function offset(int $value): static
    {
        return $this->setRaw('offset', $value);
    }
}

