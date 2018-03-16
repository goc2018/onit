<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	if ($request->has('token'))
	    {
	    	$userId = decrypt($request->token);

	    	Auth::loginUsingId($userId);
	    }

        return $next($request);
    }
}
