<?php

declare(strict_types = 1);

namespace Artim\Logger;

use Artim\Logger\Registrators\AppLogRegistrator;
use Artim\Logger\Registrators\DBLogRegistrator;
use Artim\Logger\Registrators\HttpRegistrator;
use Artim\Logger\Registrators\LogRegistrator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class ArtimLoggerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/artim-logger.php' => config_path('artim-logger.php'),
        ], 'config');
    }

    /**
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->app->make(LogRegistrator::class)->set();
        $this->app->make(HttpRegistrator::class)->set();

        if (config('artim-logger.logs.application')) {
            $this->app->make(AppLogRegistrator::class)->set();
        }

        if (config('artim-logger.logs.db')) {
            $this->app->make(DBLogRegistrator::class)->set();
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/artim-logger.php', 'artim-logger');
    }
}
