<?php

namespace OdooJson2\Odoo\Endpoint;

use OdooJson2\Exceptions\AuthenticationException;
use OdooJson2\Odoo\Models\Version;

class CommonEndpoint extends Endpoint
{
    protected string $service = 'common';

    public function authenticate(): int
    {
        $fixedUid = $this->getConfig()->getFixedUserId();
        if ($fixedUid !== null && $fixedUid > 0) {
            return $fixedUid;
        }

        // JSON-2 API uses API key authentication, so we don't need to authenticate
        // The API key is already validated on each request
        // Return a dummy UID for compatibility
        return 1;
    }

    public function version(): Version
    {
        $client = $this->getClient();
        $result = $client->call('ir.config_parameter', 'version', []);

        // If version endpoint doesn't exist, return a default version
        if (is_array($result)) {
            return Version::hydrate($result);
        }

        return Version::hydrate([
            'server_version' => '19.0',
            'server_serie' => '19.0',
        ]);
    }
}

