<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // Katalog Materi: Khusus Admin
    Route::prefix('katalog')->name('katalog.')->middleware(['role:admin'])->group(function () {
        Route::resource('iqro', \App\Http\Controllers\IqroController::class)->except(['show']);
        Route::resource('surat', \App\Http\Controllers\SuratController::class)->except(['show']);
        Route::resource('doa', \App\Http\Controllers\DoaController::class)->except(['show']);
        Route::resource('hadist', \App\Http\Controllers\HadistController::class)->except(['show']);
        Route::resource('alfabet', \App\Http\Controllers\AlfabetController::class)->except(['show']);
    });

    // Presensi Harian: Admin & Guru
    Route::middleware(['role:admin|guru'])->group(function () {
        Route::get('/presensi', [\App\Http\Controllers\PresensiController::class, 'index'])->name('presensi.index');
        Route::post('/presensi', [\App\Http\Controllers\PresensiController::class, 'store'])->name('presensi.store');
        Route::get('/presensi/rekap', [\App\Http\Controllers\PresensiController::class, 'rekap'])->name('presensi.rekap');
        Route::get('/presensi/siswa/{siswa}', [\App\Http\Controllers\PresensiController::class, 'showSiswa'])->name('presensi.siswa');
    });

    // Setoran & Hafalan (6 Modul): Admin & Guru
    Route::middleware(['role:admin|guru'])->group(function () {
        Route::prefix('setoran')->name('setoran.')->group(function () {
            Route::resource('iqro', \App\Http\Controllers\SetoranIqroController::class)->except(['show']);
            Route::resource('quran', \App\Http\Controllers\SetoranQuranController::class)->except(['show']);
        });

        Route::prefix('hafalan')->name('hafalan.')->group(function () {
            Route::resource('doa', \App\Http\Controllers\HafalanDoaController::class)->except(['show']);
            Route::resource('hadist', \App\Http\Controllers\HafalanHadistController::class)->except(['show']);
            Route::resource('surat', \App\Http\Controllers\HafalanSuratController::class)->except(['show']);
        });

        Route::resource('baca-buku', \App\Http\Controllers\BacaBukuController::class)->except(['show']);
    });

    // Pembayaran SPP: Khusus Admin / Pengguna dengan hak kelola-spp
    Route::middleware(['can:kelola-spp'])->prefix('spp')->name('spp.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SppController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\SppController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\SppController::class, 'store'])->name('store');
        Route::get('/massal', [\App\Http\Controllers\SppController::class, 'massal'])->name('massal');
        Route::post('/massal', [\App\Http\Controllers\SppController::class, 'storeMassal'])->name('massal.store');
        Route::get('/tunggakan', [\App\Http\Controllers\SppController::class, 'tunggakan'])->name('tunggakan');
        Route::get('/pengaturan', [\App\Http\Controllers\SppController::class, 'pengaturan'])->name('pengaturan');
        Route::post('/pengaturan', [\App\Http\Controllers\SppController::class, 'simpanPengaturan'])->name('pengaturan.simpan');
        Route::get('/siswa/{siswa}', [\App\Http\Controllers\SppController::class, 'riwayat'])->name('riwayat');
        Route::get('/{spp}/edit', [\App\Http\Controllers\SppController::class, 'edit'])->name('edit');
        Route::put('/{spp}', [\App\Http\Controllers\SppController::class, 'update'])->name('update');
        Route::delete('/{spp}', [\App\Http\Controllers\SppController::class, 'destroy'])->name('destroy');
    });

    // Laporan PDF: Admin & Guru
    Route::middleware(['role:admin|guru'])->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\LaporanController::class, 'index'])->name('index');
        Route::post('/cetak', [\App\Http\Controllers\LaporanController::class, 'cetak'])->name('cetak');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
