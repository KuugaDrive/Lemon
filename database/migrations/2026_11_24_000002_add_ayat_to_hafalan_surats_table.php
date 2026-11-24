<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hafalan_surats', function (Blueprint $table) {
            $table->unsignedSmallInteger('ayat_mulai')->nullable()->after('surat_id');
            $table->unsignedSmallInteger('ayat_selesai')->nullable()->after('ayat_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('hafalan_surats', function (Blueprint $table) {
            $table->dropColumn(['ayat_mulai', 'ayat_selesai']);
        });
    }
};
