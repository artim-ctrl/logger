<?php

namespace Artim\Logger\Registrators;

use Illuminate\Contracts\Foundation\Application;

abstract class AbstractRegistrator implements RegistratorInterface
{
    public function __construct(protected Application $app)
    {
    }
}
