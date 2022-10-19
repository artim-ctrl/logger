<?php

namespace Artim\Logger\Registrators;

use Illuminate\Contracts\Foundation\Application;

class AbstractRegistrator implements RegistratorInterface
{
    public function __construct(protected Application $app)
    {
    }

    public function set(): void
    {
    }
}
