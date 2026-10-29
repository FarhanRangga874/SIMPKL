<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
