<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ FIX: Check if user is authenticated AND is user
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (Auth::user()->status == 'blocked') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('error', 'Sesi berakhir. Akun Anda telah diblokir.');
        }

        if (Auth::user()->role !== 'user') {
            return redirect('/admin/dashboard')->with('error', 'Akses ditolak. Hanya untuk mahasiswa.');
        }

        return $next($request);
    }
}
