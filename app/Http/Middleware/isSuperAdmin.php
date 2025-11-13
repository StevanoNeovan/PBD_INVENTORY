<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class isSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // pastikan user login dan punya role Super_Admin
        if (!auth()->check() || auth()->user()->role->nama_role !== 'Super_Admin') {
            return redirect('/login')->with('error', 'Akses ditolak!');
        }

        return $next($request);
    }
}
