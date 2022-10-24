<?php

declare(strict_types = 1);

namespace Artim\Logger\Registrators;

class AppLogRegistrator extends AbstractRegistrator
{
    public function set(): void
    {
        $this->app->terminating(function () {
            $data = [
                'type' => 'application',
                'startedAt' => LARAVEL_START,
                'endedAt' => microtime(true),
            ];

            if (config('artim-logger.log_request') ?? true) {
                $data['request'] = request();
            }

            \Log::info('App terminating', $data);
        });
    }
}
