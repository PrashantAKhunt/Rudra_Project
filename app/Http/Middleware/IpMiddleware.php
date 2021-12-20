<?php

namespace App\Http\Middleware;

use Closure;

class IpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {return $next($request);
		if ($request->ip() != "103.238.14.163" && $request->ip() != "103.106.21.53" && $request->ip() != "192.240.98.163" && $request->ip()!="23.237.128.50" && $request->ip()!="23.237.16.27" && $request->ip()!="23.237.128.50") {
        // here instead of checking a single ip address we can do collection of ips
        //address in constant file and check with in_array function
             //return redirect()->route('error_404');
			 return abort(404);
        }
        return $next($request);
    }
}
