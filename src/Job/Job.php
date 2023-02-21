<?php

declare(strict_types = 1);

namespace Artim\Logger\Job;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Job implements ShouldQueue
{
    use LogToken;

    protected string $token;
}
