<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Kaprodi\PklPeriodController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kaprodi\CplController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        if ($user->hasRole('kaprodi')) {
            return redirect()->route('kaprodi.dashboard');
        }

        if ($user->hasRole('mahasiswa')) {
            return redirect()->route('mahasiswa.dashboard');
        }

        abort(403, 'Akun belum memiliki role yang sesuai.');
    })->name('dashboard');

    Route::get('/kaprodi/dashboard', function () {
        return view('kaprodi.index');
    })
        ->middleware('role:kaprodi')
        ->name('kaprodi.dashboard');

    Route::get('/mahasiswa/dashboard', function () {
        return view('mahasiswa.index');
    })
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.dashboard');

    // Periode Pendaftaran PKL — khusus Kaprodi
    Route::middleware('role:kaprodi')
        ->prefix('kaprodi')
        ->name('kaprodi.')
        ->group(function () {
            // Periode Pendaftaran PKL
            Route::get(
                '/pkl-periods',
                [PklPeriodController::class, 'index']
            )->name('pkl-periods.index');

            Route::get(
                '/pkl-periods/create',
                [PklPeriodController::class, 'create']
            )->name('pkl-periods.create');

            Route::post(
                '/pkl-periods',
                [PklPeriodController::class, 'store']
            )->name('pkl-periods.store');

            // Edit periode PKL
            Route::get('/pkl-periods/{pklPeriod}/edit', [PklPeriodController::class, 'edit'])
                ->name('pkl-periods.edit');

            // Simpan perubahan periode PKL
            Route::put('/pkl-periods/{pklPeriod}', [PklPeriodController::class, 'update'])
                ->name('pkl-periods.update');

            // Hapus periode PKL
            Route::delete('/pkl-periods/{pklPeriod}', [PklPeriodController::class, 'destroy'])
                ->name('pkl-periods.destroy');

            // Pengaturan CPL
            Route::get('/cpls', [CplController::class, 'index'])
                ->name('cpls.index');

            Route::get('/cpls/create', [CplController::class, 'create'])
                ->name('cpls.create');

            Route::post('/cpls', [CplController::class, 'store'])
                ->name('cpls.store');

            Route::get('/cpls/{cpl}/edit', [CplController::class, 'edit'])
                ->name('cpls.edit');

            Route::put('/cpls/{cpl}', [CplController::class, 'update'])
                ->name('cpls.update');

            Route::delete('/cpls/{cpl}', [CplController::class, 'destroy'])
                ->name('cpls.destroy');

            Route::post(
                '/pkl-periods/{pklPeriod}/activate',
                [PklPeriodController::class, 'activate']
            )->name('pkl-periods.activate');

            Route::post(
                '/pkl-periods/{pklPeriod}/finish',
                [PklPeriodController::class, 'finish']
            )->name('pkl-periods.finish');

        });
});

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');

            Route::patch('/profile', [ProfileController::class, 'update'])
                ->name('profile.update');

            Route::delete('/profile', [ProfileController::class, 'destroy'])
                ->name('profile.destroy');
        });

require __DIR__.'/auth.php';