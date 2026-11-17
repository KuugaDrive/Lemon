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
    }
}
