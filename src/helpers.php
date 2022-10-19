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

        return set_log_token(md5(microtime(true)));
    }
}

if (! function_exists('set_log_token')) {
    function set_log_token(string $logToken): string
    {
        return $_SERVER['LOG_TOKEN'] = $logToken;
    }
}
