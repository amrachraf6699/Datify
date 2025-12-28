<?php

namespace Datify;

use Illuminate\Support\ServiceProvider;

class DatifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/datify.php' => config_path('datify.php'),
        ], 'datify-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datify.php', 'datify');
    }
}
