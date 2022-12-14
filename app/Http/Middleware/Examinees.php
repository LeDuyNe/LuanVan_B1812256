<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Examinees
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if (Auth::check() && (Auth::user()->role == 0 || Auth::user()->role == 1 || Auth::user()->role == 2)){   
        if (Auth::check() && (Auth::user()->role == 1 || Auth::user()->role == 2)){ 
            return $next($request);
        }
        return redirect()->route('permission-error');
    }
}
