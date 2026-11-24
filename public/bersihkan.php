<?php
/**
 * Lemon - Pembersihan Data Transaksi & Sampel untuk Hosting
 * Keamanan: Dilindungi dengan token rahasia
 */

$token = 'lemon-clean-2026';

if (($_GET['key'] ?? '') !== $token) {
    http_response_code(403);
    die('Akses Ditolak: Kunci otentikasi tidak valid. Harap sertakan parameter ?key=' . $token);
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

echo "====================================================\n";
echo "   SISTEM INFORMASI LEMON - PEMBERSIHAN DATA        \n";
echo "====================================================\n\n";

$keepMaster = isset($_GET['keep_master']) && $_GET['keep_master'] == '1';

$params = ['--force' => true];
if ($keepMaster) {
    $params['--keep-master'] = true;
    echo "Mode: Hanya membersihkan transaksi (Master Santri, Guru & Kelas DIPERTAHANKAN).\n\n";
} else {
    echo "Mode: Bersihkan SEMUA transaksi, santri, kelas, dan user sampel (Menyisakan Akun Admin & Katalog).\n\n";
}

try {
    \Illuminate\Support\Facades\Artisan::call('lemon:bersihkan-data', $params);
    echo \Illuminate\Support\Facades\Artisan::output();

    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');

    echo "\n====================================================\n";
    echo "STATUS: PEMBERSIHAN DATA BERHASIL DIJALANKAN!\n";
    echo "Akun Administrator yang tersisa:\n";
    echo "  NIP     : admin\n";
    echo "  Password: password\n";
    echo "====================================================\n";
} catch (\Throwable $e) {
    echo "\n[ERROR]: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
