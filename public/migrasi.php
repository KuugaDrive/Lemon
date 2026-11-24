<?php
/**
 * Lemon - Database Migration & Seeder Runner
 * Keamanan: Dilindungi dengan token rahasia
 */

$token = 'lemon-migration-2026';

if (($_GET['key'] ?? '') !== $token) {
    http_response_code(403);
    die('Akses Ditolak: Kunci otentikasi tidak valid.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "   SISTEM INFORMASI LEMON - MIGRASI DB  \n";
echo "========================================\n\n";

try {
    echo "[1/4] Menjalankan migrasi database...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output() . "\n";

    echo "[2/4] Menjalankan SuratSeeder (nama arab & ayat)...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'SuratSeeder', '--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output() . "\n";

    echo "[3/4] Menjalankan AlfabetLatinSeeder (A-Z)...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AlfabetLatinSeeder', '--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output() . "\n";

    echo "[4/5] Memperbarui nama lembaga...\n";
    \App\Models\Pengaturan::set('nama_lembaga', 'Binar Sunnah School');
    echo "Nama lembaga: " . \App\Models\Pengaturan::get('nama_lembaga') . "\n\n";

    echo "[5/5] Membersihkan cache tampilan & konfigurasi...\n";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo \Illuminate\Support\Facades\Artisan::output();
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo \Illuminate\Support\Facades\Artisan::output();

    echo "\n========================================\n";
    echo "STATUS: SEMUA MIGRASI BERHASIL DIJALANKAN!\n";
    echo "========================================\n";
} catch (\Throwable $e) {
    echo "\n[ERROR]: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
