<?php

namespace Artim\Logger\Http;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;

class HttpFactory extends Factory
{
    /**
     * @comment Set LOG-TOKEN header to request
     *
     * @return PendingRequest
     */
    public function withLogToken(): PendingRequest
    {
        return $this->withHeaders(['LOG-TOKEN' => get_log_token()]);
    }
}
