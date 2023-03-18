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
                'peakMemoryUsage' => get_formatted_peak_memory_usage(),
            ];

            if (config('artim-logger.logs.request')) {
                $data['request'] = request();
            }

            \Log::info('App terminating', $data);
        });
    }
}
