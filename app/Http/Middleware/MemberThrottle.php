<?php

namespace App\Http\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Closure;

class MemberThrottle extends ThrottleRequests
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
        return redirect('/login')->withError('Too Many Attempts.');
    }
}
