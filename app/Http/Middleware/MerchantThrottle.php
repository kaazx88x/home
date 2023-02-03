<?php

namespace App\Http\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Closure;

class MerchantThrottle extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function buildResponse($key, $maxAttempts)
    {
        return redirect('/merchant/login')->withError('Too Many Attempts.');
    }
}
