<?php

namespace Artim\Logger\Job;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Job implements ShouldQueue
{
    protected string $logToken;

    public function __construct()
    {
        $this->logToken = get_log_token();
    }

    public function handle(): void
    {
        set_log_token($this->logToken);
    }
}
