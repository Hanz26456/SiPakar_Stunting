<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GejalaController;
use App\Http\Controllers\Admin\RuleCfController;
use Illuminate\Support\Facades\Route;

// ===== REDIRECT BERDASARKAN ROLE SETELAH LOGIN =====
Route::middleware('auth')->get('/', function () {
    return match(auth()->user()->role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'bidan'  => redirect()->route('bidan.dashboard'),
        'kader'  => redirect()->route('kader.dashboard'),
        'ortu'   => redirect()->route('ortu.dashboard'),
        default  => redirect()->route('login'),
    };
});

// ===== KADER =====
Route::middleware(['auth', 'role:kader,admin'])
    ->prefix('kader')
    ->name('kader.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kader'])
             ->name('dashboard');

        // Balita
        Route::resource('balita', BalitaController::class);

        // Kunjungan (pencatatan posyandu)
        Route::resource('kunjungan', KunjunganController::class);
        Route::get('balita/{balita}/kunjungan/create',
            [KunjunganController::class, 'createForBalita'])
            ->name('kunjungan.create-for-balita');

        // Diagnosis
        Route::get('kunjungan/{kunjungan}/diagnosis',
            [DiagnosisController::class, 'create'])
            ->name('diagnosis.create');
        Route::post('kunjungan/{kunjungan}/diagnosis',
            [DiagnosisController::class, 'store'])
            ->name('diagnosis.store');
        Route::get('diagnosis/{diagnosis}',
            [DiagnosisController::class, 'show'])
            ->name('diagnosis.show');
        Route::post('kunjungan/{kunjungan}/diagnosis/preview',
            [DiagnosisController::class, 'preview'])
            ->name('diagnosis.preview');
    });

// ===== BIDAN =====
Route::middleware(['auth', 'role:bidan,admin'])
    ->prefix('bidan')
    ->name('bidan.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'bidan'])
             ->name('dashboard');

        // Monitoring
        Route::get('/monitoring', [VerifikasiController::class, 'index'])
             ->name('monitoring');

        // Verifikasi diagnosis
        Route::get('/verifikasi/{diagnosis}',
            [VerifikasiController::class, 'show'])
            ->name('verifikasi.show');
        Route::post('/verifikasi/{diagnosis}',
            [VerifikasiController::class, 'update'])
            ->name('verifikasi.update');

        // Detail pasien
        Route::get('/pasien/{balita}',
            [VerifikasiController::class, 'pasien'])
            ->name('pasien.show');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'bidan'])
             ->name('laporan');
        Route::get('/laporan/export',
            [LaporanController::class, 'export'])
            ->name('laporan.export');
    });

// ===== ORANG TUA =====
Route::middleware(['auth', 'role:ortu'])
    ->prefix('ortu')
    ->name('ortu.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'ortu'])
             ->name('dashboard');
        Route::get('/anak/{balita}',
            [DashboardController::class, 'detailAnak'])
            ->name('anak.show');
        Route::get('/panduan', [DashboardController::class, 'panduan'])
             ->name('panduan');
    });

// ===== ADMIN =====
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])
             ->name('dashboard');

        // Manajemen pengguna
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-active',
            [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        // Basis pengetahuan
        Route::resource('gejala', GejalaController::class);
        Route::resource('rule-cf', RuleCfController::class);
        Route::patch('rule-cf/{ruleCf}/toggle',
            [RuleCfController::class, 'toggle'])
            ->name('rule-cf.toggle');

        // Log & pengaturan
        Route::get('/log', [DashboardController::class, 'log'])
             ->name('log');
        Route::get('/pengaturan', [DashboardController::class, 'pengaturan'])
             ->name('pengaturan');
    });

require __DIR__.'/auth.php';