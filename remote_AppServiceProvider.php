<?php

namespace App\Providers;

use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\User;
use App\Policies\KelasPolicy;
use App\Policies\PembayaranSppPolicy;
use App\Policies\SiswaPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super-admin / admin implicitly gets all abilities
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Kelas::class, KelasPolicy::class);
        Gate::policy(Siswa::class, SiswaPolicy::class);
        Gate::policy(PembayaranSpp::class, PembayaranSppPolicy::class);

        // Auto-run migrasi untuk production jika tabel baru belum ada
        if (!app()->environment('testing')) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('alfabet_latins') || !\Illuminate\Support\Facades\Schema::hasColumn('siswas', 'jenis_kelamin')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                    if (\Illuminate\Support\Facades\Schema::hasTable('surats')) {
                        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'SuratSeeder', '--force' => true]);
                    }
                    if (\Illuminate\Support\Facades\Schema::hasTable('alfabet_latins')) {
                        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AlfabetLatinSeeder', '--force' => true]);
                    }
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
                }
                if (\Illuminate\Support\Facades\Schema::hasTable('pengaturans')) {
                    if (\App\Models\Pengaturan::where('kunci', 'nama_lembaga')->value('nilai') !== 'Binar Sunnah School') {
                        \App\Models\Pengaturan::set('nama_lembaga', 'Binar Sunnah School');
                    }
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan boot jika koneksi DB bermasalah
            }
        }
    }
}
