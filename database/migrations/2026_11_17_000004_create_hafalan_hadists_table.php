<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hafalan_hadists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->restrictOnDelete();
            $table->foreignId('guru_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('hadist_id')->constrained('hadists')->restrictOnDelete();
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
        Schema::dropIfExists('hafalan_hadists');
    }
};
