<?php

namespace Artim\Logger\Logger\File;

use Artim\Logger\Logger\Logger;

class FileLogSetter
{
    public function __invoke(array $config): Logger
    {
        $handler = new ($config['handler'])(
            ...$config['handler_with'] ?? [],
        );

        $formatter = new ($config['formatter'])(
            ...$config['formatter_with'] ?? [],
        );

        $handler->setFormatter($formatter);

        return new Logger(
            $config['name'] ?? app()->environment(),
            [$handler],
        );
    }
}
