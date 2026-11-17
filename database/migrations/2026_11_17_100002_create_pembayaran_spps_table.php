<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->restrictOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1 - 12
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('nominal'); // Rupiah tanpa desimal
            $table->date('tgl_bayar');
            $table->string('ket', 255)->nullable();
            $table->foreignId('dicatat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['siswa_id', 'bulan', 'tahun']);
            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_spps');
    }
};
