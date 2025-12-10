<?php

namespace OdooJson2;

use Illuminate\Support\ServiceProvider;
use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\OdooModel;

class OdooServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        OdooModel::boot($this->app->make(Odoo::class));
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/odoo.php', 'odoo');

        $this->app->singleton(Odoo::class, function ($app) {
            $database = config('odoo.database');
            return new Odoo(new Config(
                database: $database ?: null,
                host: config('odoo.host', ''),
                apiKey: config('odoo.api_key', ''),
                sslVerify: config('odoo.ssl_verify', true)
            ), new Context(
                lang: config('odoo.context.lang'),
                timezone: config('odoo.context.timezone'),
                companyId: config('odoo.context.companyId')
            ));
        });
    }

    public function provides()
    {
        return ['odoo'];
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__ . '/../config/odoo.php' => config_path('odoo.php'),
        ], 'config');
    }
}

