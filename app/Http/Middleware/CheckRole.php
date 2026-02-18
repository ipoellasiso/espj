<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    // public function handle($request, Closure $next,...$roles)
    // {
    //     if(in_array($request->user()->role,$roles)){
    //         return $next($request);
    //     }
    //         return redirect('/login');
    // }

     public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403);
        }

        /* ✅ NORMALISASI ROLE USER */
        $userRole = strtolower(Auth::user()->role);

        foreach ($roles as $role) {

            /* ✅ BANDINGKAN LOWERCASE */
            if ($userRole == strtolower($role)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak');
    }
}
