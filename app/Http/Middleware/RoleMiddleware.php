<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            // Jika belum, alihkan ke halaman login
            return redirect('login');
        }

        // 2. Dapatkan data pengguna yang sedang login
        $user = Auth::user();

        // 3. Loop melalui setiap peran yang diizinkan yang dilewatkan ke middleware
        //    Contoh: ->middleware('role:admin,petugas') akan memberikan $roles = ['admin', 'petugas']
        foreach ($roles as $role) {
            // 4. Periksa apakah peran pengguna saat ini cocok dengan salah satu peran yang diizinkan
            if ($user->role == $role) {
                // Jika cocok, izinkan permintaan untuk melanjutkan ke tujuan berikutnya (controller/view)
                return $next($request);
            }
        }

        // 5. Jika loop selesai dan tidak ada peran yang cocok, blokir akses.
        //    Tampilkan halaman error 403 (Forbidden) dengan pesan.
        abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK HALAMAN INI.');
    }
}
