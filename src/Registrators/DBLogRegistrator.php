<?php

declare(strict_types = 1);

namespace Artim\Logger\Registrators;

use Illuminate\Database\Events\QueryExecuted;

class DBLogRegistrator implements RegistratorInterface
{
    public function set(): void
    {
        \DB::listen(
            fn (QueryExecuted $query) => \Log::info('DB query', [
                'type' => 'query',
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ])
        );
    }
}
