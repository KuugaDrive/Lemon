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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

        // Auto-sync skema database untuk production (aman untuk shared hosting tanpa CLI/proc_open)
        if (!app()->environment('testing')) {
            // 1. Field jenis_kelamin pada siswas
            try {
                if (Schema::hasTable('siswas') && !Schema::hasColumn('siswas', 'jenis_kelamin')) {
                    Schema::table('siswas', function (Blueprint $table) {
                        $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nama_siswa');
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (siswas.jenis_kelamin): ' . $e->getMessage());
            }

            // 2. Field nama_wali pada siswas
            try {
                if (Schema::hasTable('siswas') && !Schema::hasColumn('siswas', 'nama_wali')) {
                    Schema::table('siswas', function (Blueprint $table) {
                        $table->string('nama_wali', 255)->nullable()->after('nama_orang_tua');
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (siswas.nama_wali): ' . $e->getMessage());
            }

            // 3. Field jenis_kelamin pada users
            try {
                if (Schema::hasTable('users') && !Schema::hasColumn('users', 'jenis_kelamin')) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('name');
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (users.jenis_kelamin): ' . $e->getMessage());
            }

            // 4. Field nama_arab pada surats
            try {
                if (Schema::hasTable('surats') && !Schema::hasColumn('surats', 'nama_arab')) {
                    Schema::table('surats', function (Blueprint $table) {
                        $table->string('nama_arab', 100)->nullable()->after('nama_latin');
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (surats.nama_arab): ' . $e->getMessage());
            }

            // 5. Field ayat_mulai & ayat_selesai pada hafalan_surats
            try {
                if (Schema::hasTable('hafalan_surats') && !Schema::hasColumn('hafalan_surats', 'ayat_mulai')) {
                    Schema::table('hafalan_surats', function (Blueprint $table) {
                        $table->unsignedSmallInteger('ayat_mulai')->nullable()->after('surat_id');
                        $table->unsignedSmallInteger('ayat_selesai')->nullable()->after('ayat_mulai');
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (hafalan_surats.ayat): ' . $e->getMessage());
            }

            // 6. Tabel alfabet_latins
            try {
                if (!Schema::hasTable('alfabet_latins')) {
                    Schema::create('alfabet_latins', function (Blueprint $table) {
                        $table->id();
                        $table->unsignedTinyInteger('urutan')->unique();
                        $table->string('huruf', 10)->unique();
                        $table->timestamps();
                    });

                    // Seed A-Z
                    $hurufList = range('A', 'Z');
                    $now = now();
                    $records = [];
                    foreach ($hurufList as $idx => $huruf) {
                        $records[] = [
                            'urutan' => $idx + 1,
                            'huruf' => $huruf,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    \App\Models\AlfabetLatin::insert($records);
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (alfabet_latins): ' . $e->getMessage());
            }

            // 7. Field kategori & alfabet_latin_id pada baca_bukus
            try {
                if (Schema::hasTable('baca_bukus')) {
                    if (!Schema::hasColumn('baca_bukus', 'kategori')) {
                        Schema::table('baca_bukus', function (Blueprint $table) {
                            $table->enum('kategori', ['hijaiyah', 'latin'])->default('hijaiyah')->after('siswa_id');
                        });
                    }
                    if (!Schema::hasColumn('baca_bukus', 'alfabet_latin_id')) {
                        Schema::table('baca_bukus', function (Blueprint $table) {
                            $table->unsignedBigInteger('alfabet_latin_id')->nullable();
                        });
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (baca_bukus): ' . $e->getMessage());
            }

            // 8. Nama lembaga dan alamat real pada pengaturans
            try {
                if (Schema::hasTable('pengaturans')) {
                    if (\App\Models\Pengaturan::where('kunci', 'nama_lembaga')->value('nilai') !== 'Binar Sunnah School') {
                        \App\Models\Pengaturan::set('nama_lembaga', 'Binar Sunnah School');
                    }
                    $realAddress = 'Jl. H. Nirin No. 63, RT.3/RW.5, Petukangan Selatan, Kec. Pesanggrahan, Kota Jakarta Selatan, DKI Jakarta 12270';
                    if (\App\Models\Pengaturan::where('kunci', 'alamat_lembaga')->value('nilai') !== $realAddress) {
                        \App\Models\Pengaturan::set('alamat_lembaga', $realAddress);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (pengaturans): ' . $e->getMessage());
            }

            // 9. Role Kepsek
            try {
                if (Schema::hasTable('roles')) {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'kepsek', 'guard_name' => 'web']);
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (roles.kepsek): ' . $e->getMessage());
            }

            // 10. Jika ada surat yang belum punya nama arab, jalankan seeder
            try {
                if (Schema::hasTable('surats') && Schema::hasColumn('surats', 'nama_arab')) {
                    if (\App\Models\Surat::whereNull('nama_arab')->count() > 0) {
                        (new \Database\Seeders\SuratSeeder())->run();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Schema auto-sync (surats.seeder): ' . $e->getMessage());
            }

            // 11. Dump ringkasan status database ke storage/logs/database_audit.json
            try {
                $auditFile = storage_path('logs/database_audit.json');
                $auditData = [
                    'timestamp' => now()->toIso8601String(),
                    'schema_columns' => [
                        'siswas.jenis_kelamin' => Schema::hasTable('siswas') && Schema::hasColumn('siswas', 'jenis_kelamin'),
                        'siswas.nama_wali' => Schema::hasTable('siswas') && Schema::hasColumn('siswas', 'nama_wali'),
                        'users.jenis_kelamin' => Schema::hasTable('users') && Schema::hasColumn('users', 'jenis_kelamin'),
                        'baca_bukus.kategori' => Schema::hasTable('baca_bukus') && Schema::hasColumn('baca_bukus', 'kategori'),
                        'baca_bukus.alfabet_latin_id' => Schema::hasTable('baca_bukus') && Schema::hasColumn('baca_bukus', 'alfabet_latin_id'),
                        'hafalan_surats.ayat_mulai' => Schema::hasTable('hafalan_surats') && Schema::hasColumn('hafalan_surats', 'ayat_mulai'),
                        'surats.nama_arab' => Schema::hasTable('surats') && Schema::hasColumn('surats', 'nama_arab'),
                        'roles.kepsek' => Schema::hasTable('roles') && \Spatie\Permission\Models\Role::where('name', 'kepsek')->exists(),
                    ],
                    'counts' => [
                        'users' => Schema::hasTable('users') ? \App\Models\User::count() : 0,
                        'siswas' => Schema::hasTable('siswas') ? \App\Models\Siswa::count() : 0,
                        'kelas' => Schema::hasTable('kelas') ? \App\Models\Kelas::count() : 0,
                        'presensis' => Schema::hasTable('presensis') ? \App\Models\Presensi::count() : 0,
                        'setoran_iqros' => Schema::hasTable('setoran_iqros') ? \App\Models\SetoranIqro::count() : 0,
                        'setoran_qurans' => Schema::hasTable('setoran_qurans') ? \App\Models\SetoranQuran::count() : 0,
                        'hafalan_surats' => Schema::hasTable('hafalan_surats') ? \App\Models\HafalanSurat::count() : 0,
                        'hafalan_doas' => Schema::hasTable('hafalan_doas') ? \App\Models\HafalanDoa::count() : 0,
                        'hafalan_hadists' => Schema::hasTable('hafalan_hadists') ? \App\Models\HafalanHadist::count() : 0,
                        'baca_bukus' => Schema::hasTable('baca_bukus') ? \App\Models\BacaBuku::count() : 0,
                        'pembayaran_spps' => Schema::hasTable('pembayaran_spps') ? \App\Models\PembayaranSpp::count() : 0,
                    ],
                    'pengaturans' => Schema::hasTable('pengaturans') ? \App\Models\Pengaturan::all()->pluck('nilai', 'kunci')->toArray() : [],
                    'users' => Schema::hasTable('users') ? \App\Models\User::with('roles')->get()->map(function ($u) {
                        return [
                            'id' => $u->id,
                            'nip' => $u->nip,
                            'name' => $u->name,
                            'jenis_kelamin' => $u->jenis_kelamin,
                            'roles' => $u->roles->pluck('name')->toArray(),
                        ];
                    })->toArray() : [],
                    'kelas' => Schema::hasTable('kelas') ? \App\Models\Kelas::all()->map(function ($k) {
                        return [
                            'id' => $k->id,
                            'nama_kelas' => $k->nama_kelas,
                        ];
                    })->toArray() : [],
                    'siswas' => Schema::hasTable('siswas') ? \App\Models\Siswa::with('kelas')->get()->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'nis' => $s->nis,
                            'nama' => $s->nama_siswa,
                            'jk' => $s->jenis_kelamin,
                            'kelas' => $s->kelas?->nama_kelas,
                            'ortu' => $s->nama_orang_tua,
                            'wali' => $s->nama_wali,
                        ];
                    })->toArray() : [],
                ];
                file_put_contents($auditFile, json_encode($auditData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } catch (\Throwable $e) {
                Log::warning('Database audit dump: ' . $e->getMessage());
            }
        }
    }
}
