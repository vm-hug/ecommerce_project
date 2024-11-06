<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()) {
            if(Auth::user()->utype === 'ADM'){
                return $next($request);
            }else {
                Session::flush();
                return redirect()->route('login');
            }
        }else {
            return redirect()->route('login');
        }
    }
}
