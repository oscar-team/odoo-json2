<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Odoo Host
    | Must start with http:// or https:// and include port
    |--------------------------------------------------------------------------
    */
    'host' => env('ODOO_HOST', ''),

    /*
    |--------------------------------------------------------------------------
    | Database name (optional)
    | If not provided, Odoo will use the default database from the API key
    |--------------------------------------------------------------------------
    */
    'database' => env('ODOO_DATABASE', null),

    /*
    |--------------------------------------------------------------------------
    | API Key
    | JSON-2 API uses API key authentication instead of username/password
    |--------------------------------------------------------------------------
    */
    'api_key' => env('ODOO_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Fixed User ID (optional)
    | If set, authentication will be skipped and this User ID will be used.
    |--------------------------------------------------------------------------
    */
    'fixed_user_id' => env('ODOO_FIXED_USER_ID', null),

    'context' => [
        'lang' => env('ODOO_LANG', null),
        'timezone' => env('ODOO_TIMEZONE', null),
        'companyId' => env('ODOO_COMPANY_ID', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Use SSL Verify
    |--------------------------------------------------------------------------
    */
    'ssl_verify' => (bool) env('ODOO_SSL_VERIFY', true)
];
