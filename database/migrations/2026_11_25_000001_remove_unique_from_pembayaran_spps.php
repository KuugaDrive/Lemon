<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_spps', function (Blueprint $table) {
            $table->index('siswa_id', 'pembayaran_spps_siswa_id_index');
            $table->index(['siswa_id', 'bulan', 'tahun'], 'pembayaran_spps_siswa_id_bulan_tahun_index');
            $table->dropUnique('pembayaran_spps_siswa_id_bulan_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_spps', function (Blueprint $table) {
            $table->dropIndex('pembayaran_spps_siswa_id_index');
            $table->dropIndex('pembayaran_spps_siswa_id_bulan_tahun_index');
            $table->unique(['siswa_id', 'bulan', 'tahun']);
        });
    }
};
