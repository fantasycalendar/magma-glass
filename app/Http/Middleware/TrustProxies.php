<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     * Set the TRUSTED_PROXIES env var to a comma-separated list of IPs,
     * or '*' if you intentionally want to trust all proxies (e.g. behind a load balancer).
     *
     * @var array|string|null
     */
    protected $proxies = null;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    public function __construct()
    {
        $trustedProxies = env('TRUSTED_PROXIES');

        if ($trustedProxies) {
            $this->proxies = $trustedProxies === '*'
                ? '*'
                : explode(',', $trustedProxies);
        }
    }
}
