<?php

namespace Artim\Logger\Registrators;

use Illuminate\Database\Events\QueryExecuted;

class DBLogRegistrator implements RegistratorInterface
{
    public function set(): void
    {
        \DB::listen(
            fn (QueryExecuted $query) => \Log::info($query->sql, [
                'type' => 'query',
                'bindings' => $query->bindings,
                'time' => $query->time,
            ])
        );
    }
}
