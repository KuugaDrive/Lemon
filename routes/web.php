<?php

use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('master')->name('master.')->group(function () {
        // Master Guru & Master Kelas: Khusus Admin
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('guru', GuruController::class);
            Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
        });

        // Master Siswa: create, store, edit, update, destroy khusus Admin
        Route::middleware(['role:admin'])->group(function () {
            Route::get('siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
            Route::post('siswa', [SiswaController::class, 'store'])->name('siswa.store');
            Route::get('siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
            Route::match(['put', 'patch'], 'siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
            Route::delete('siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
        });

        // Master Siswa: index & show untuk Admin dan Guru (dibatasi scope kelas pada controller & policy)
        Route::middleware(['role:admin|guru'])->group(function () {
            Route::get('siswa', [SiswaController::class, 'index'])->name('siswa.index');
            Route::get('siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
