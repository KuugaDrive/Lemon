<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_iqros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->restrictOnDelete();
            $table->foreignId('guru_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('iqro_id')->constrained('iqros')->restrictOnDelete();
            $table->unsignedSmallInteger('halaman');
            $table->date('tanggal')->index();
            $table->enum('nilai', ['BB', 'MB', 'BSH', 'BSB']);
            $table->string('ket', 255)->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_iqros');
    }
};
