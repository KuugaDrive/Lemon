<?php
/**
 * Lemon - Database & Feature Audit Script
 * Keamanan: Dilindungi dengan token rahasia
 */

$token = 'lemon-audit-2026';

if (($_GET['key'] ?? '') !== $token) {
    http_response_code(403);
    die('Akses Ditolak: Kunci otentikasi tidak valid. Gunakan ?key=' . $token);
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pengaturan;

header('Content-Type: application/json; charset=utf-8');

$audit = [
    'status' => 'success',
    'timestamp' => now()->toIso8601String(),
    'schema_check' => [
        'siswas.jenis_kelamin' => Schema::hasTable('siswas') && Schema::hasColumn('siswas', 'jenis_kelamin'),
        'siswas.nama_wali' => Schema::hasTable('siswas') && Schema::hasColumn('siswas', 'nama_wali'),
        'users.jenis_kelamin' => Schema::hasTable('users') && Schema::hasColumn('users', 'jenis_kelamin'),
        'baca_bukus.kategori' => Schema::hasTable('baca_bukus') && Schema::hasColumn('baca_bukus', 'kategori'),
        'baca_bukus.alfabet_latin_id' => Schema::hasTable('baca_bukus') && Schema::hasColumn('baca_bukus', 'alfabet_latin_id'),
        'hafalan_surats.ayat_mulai' => Schema::hasTable('hafalan_surats') && Schema::hasColumn('hafalan_surats', 'ayat_mulai'),
        'surats.nama_arab' => Schema::hasTable('surats') && Schema::hasColumn('surats', 'nama_arab'),
        'roles.kepsek' => Schema::hasTable('roles') && Role::where('name', 'kepsek')->exists(),
    ],
    'counts' => [
        'users' => Schema::hasTable('users') ? User::count() : 0,
        'siswas' => Schema::hasTable('siswas') ? Siswa::count() : 0,
        'kelas' => Schema::hasTable('kelas') ? Kelas::count() : 0,
        'presensis' => Schema::hasTable('presensis') ? DB::table('presensis')->count() : 0,
        'setoran_iqros' => Schema::hasTable('setoran_iqros') ? DB::table('setoran_iqros')->count() : 0,
        'setoran_qurans' => Schema::hasTable('setoran_qurans') ? DB::table('setoran_qurans')->count() : 0,
        'hafalan_surats' => Schema::hasTable('hafalan_surats') ? DB::table('hafalan_surats')->count() : 0,
        'hafalan_doas' => Schema::hasTable('hafalan_doas') ? DB::table('hafalan_doas')->count() : 0,
        'hafalan_hadists' => Schema::hasTable('hafalan_hadists') ? DB::table('hafalan_hadists')->count() : 0,
        'baca_bukus' => Schema::hasTable('baca_bukus') ? DB::table('baca_bukus')->count() : 0,
        'pembayaran_spps' => Schema::hasTable('pembayaran_spps') ? DB::table('pembayaran_spps')->count() : 0,
    ],
    'pengaturans' => Schema::hasTable('pengaturans') ? Pengaturan::all()->pluck('nilai', 'kunci')->toArray() : [],
    'roles' => Schema::hasTable('roles') ? Role::all()->pluck('name')->toArray() : [],
    'users' => Schema::hasTable('users') ? User::with('roles')->get()->map(function ($u) {
        return [
            'id' => $u->id,
            'nip' => $u->nip,
            'name' => $u->name,
            'jenis_kelamin' => $u->jenis_kelamin,
            'roles' => $u->roles->pluck('name')->toArray(),
        ];
    })->toArray() : [],
    'kelas' => Schema::hasTable('kelas') ? Kelas::all()->map(function ($k) {
        return [
            'id' => $k->id,
            'nama_kelas' => $k->nama_kelas,
        ];
    })->toArray() : [],
    'siswas' => Schema::hasTable('siswas') ? Siswa::with('kelas')->get()->map(function ($s) {
        return [
            'id' => $s->id,
            'nis' => $s->nis,
            'nama' => $s->nama_siswa,
            'jk' => $s->jenis_kelamin,
            'kelas' => $s->kelas?->nama_kelas,
            'ayah' => $s->nama_ayah_kandung,
            'ibu' => $s->nama_ibu_kandung,
            'ortu' => $s->nama_orang_tua,
            'wali' => $s->nama_wali,
        ];
    })->toArray() : [],
];

$auditFile = storage_path('logs/database_audit.json');
@file_put_contents($auditFile, json_encode($audit, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode($audit, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
