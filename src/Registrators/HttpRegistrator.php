<?php

namespace Artim\Logger\Registrators;

use Artim\Logger\Http\HttpFactory;

class HttpRegistrator extends AbstractRegistrator
{
    public function set(): void
    {
        $this->app->singleton('http', HttpFactory::class);
    }
}
