<?php

declare(strict_types = 1);

namespace Artim\Logger\Registrators;

use Artim\Logger\Logger\LogManager;

class LogRegistrator extends AbstractRegistrator
{
    public function set(): void
    {
        $this->app->extend('log', fn ($command, $app) => new LogManager($app));
        \Log::clearResolvedInstance('log');
    }
}
