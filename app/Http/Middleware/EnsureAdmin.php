<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Hanya user dengan role admin yang boleh lanjut ke controller.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Versi modul: cek email admin@example.com.
        // Versi latihan: cek kolom role di tabel users.
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
