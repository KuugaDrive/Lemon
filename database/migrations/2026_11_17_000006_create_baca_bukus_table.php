<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baca_bukus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->restrictOnDelete();
            $table->foreignId('guru_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('alfabet_id')->constrained('alfabets')->restrictOnDelete();
            $table->string('jilid', 50);
            $table->unsignedSmallInteger('halaman');
            $table->date('tgl_hafalan')->nullable();
            $table->date('tgl_pengujian')->index();
            $table->enum('nilai', ['BB', 'MB', 'BSH', 'BSB']);
            $table->string('ket', 255)->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'tgl_pengujian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baca_bukus');
    }
};
