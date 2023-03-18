<?php

if (! function_exists('get_log_token')) {
    function get_log_token(): string
    {
        if (isset($_SERVER['LOG_TOKEN'])) {
            return $_SERVER['LOG_TOKEN'];
        }

        if (request()->hasHeader('LOG-TOKEN')) {
            return set_log_token(request()->header('LOG_TOKEN'));
        }

        return set_log_token(md5((string)microtime(true)));
    }
}

if (! function_exists('set_log_token')) {
    function set_log_token(string $logToken): string
    {
        return $_SERVER['LOG_TOKEN'] = $logToken;
    }
}

if (! function_exists('get_formatted_peak_memory_usage')) {
    function get_formatted_peak_memory_usage(): string
    {
        $bytes = memory_get_peak_usage();
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        if (0 === $bytes) {
            return '0 ' . $unit[0];
        }

        return round($bytes / pow(1000, ($i = floor(log($bytes, 1000)))), 2) . ' ' . ($unit[$i] ?? 'B');
    }
}
