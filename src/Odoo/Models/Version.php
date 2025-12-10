<?php

namespace OdooJson2\Odoo\Models;

class Version
{
    public function __construct(
        public string $serverVersion,
        public string $serverSerie,
    ) {
    }

    public static function hydrate(array $data): self
    {
        return new self(
            serverVersion: $data['server_version'] ?? $data['serverVersion'] ?? '19.0',
            serverSerie: $data['server_serie'] ?? $data['serverSerie'] ?? '19.0',
        );
    }
}

