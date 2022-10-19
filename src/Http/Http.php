<?php

namespace Artim\Logger\Http;

use Illuminate\Support\Facades\Http as BaseHttp;

/**
 * @method static \Illuminate\Http\Client\PendingRequest withLogToken() Set LOG-TOKEN header to request
 */
class Http extends BaseHttp
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'http';
    }
}
