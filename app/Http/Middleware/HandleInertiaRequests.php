<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Template root yang digunakan Inertia.
     */
    protected $rootView = 'app';

    /**
     * Tentukan versi aset untuk cache busting.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Data yang dibagikan ke semua halaman Vue (shared props).
     * Bisa diakses via usePage().props di Vue.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [

            // Data user yang sedang login
            'auth' => [
                'user' => $request->user() ? [
                    'id'        => $request->user()->id,
                    'name'      => $request->user()->name,
                    'email'     => $request->user()->email,
                    'role'      => $request->user()->role,
                    'is_admin'  => $request->user()->isAdmin(),
                    'is_bidan'  => $request->user()->isBidan(),
                    'is_kader'  => $request->user()->isKader(),
                    'is_ortu'   => $request->user()->isOrtu(),
                ] : null,
            ],

            // Flash messages
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info'    => fn () => $request->session()->get('info'),
            ],
        ]);
    }
}