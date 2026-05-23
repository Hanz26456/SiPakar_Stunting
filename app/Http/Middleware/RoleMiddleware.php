<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Cek apakah user punya role yang diizinkan.
     *
     * Contoh pemakaian di routes/web.php:
     *   Route::middleware(['auth', 'role:admin,bidan'])->group(...)
     *   Route::middleware(['auth', 'role:kader'])->group(...)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->hasRole($roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
