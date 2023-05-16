<?php

declare(strict_types = 1);

namespace Artim\Logger\Registrators;

class AppLogRegistrator extends AbstractRegistrator
{
    public function set(): void
    {
        /**
         * Because the schedule:run command must run every minute, it will generate a log about the destruction of the Application object every minute.
         * But this log is almost never used.
         *
         * During php artisan test run LARAVEL_START constant does not exist.
         */
        if (
            $this->app->runningInConsole()
            && (
                (in_array('schedule:run', request()->server('argv'), true)
                && ! config('artim-logger.logs.schedule_terminating'))
                || in_array('test', request()->server('argv'), true)
            )
        ) {
            return;
        }

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
