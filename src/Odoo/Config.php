<?php

namespace OdooJson2\Odoo;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class Config
{
    public function __construct(
        protected ?string $database,
        protected string $host,
        protected string $apiKey,
        protected bool $sslVerify = true,
        protected ?int $fixedUserId = null
    ) {
        $this->fixedUserId = $fixedUserId;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getSslVerify(): bool
    {
        return $this->sslVerify;
    }

    public function getFixedUserId(): ?int
    {
        return $this->fixedUserId;
    }
}

