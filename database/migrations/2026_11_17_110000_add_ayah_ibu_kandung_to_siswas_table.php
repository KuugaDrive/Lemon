<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nama_ayah_kandung')->nullable()->after('alamat');
            $table->string('nama_ibu_kandung')->nullable()->after('nama_ayah_kandung');
        });

        DB::table('siswas')->update([
            'nama_ayah_kandung' => DB::raw('nama_orang_tua'),
        ]);
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nama_ayah_kandung', 'nama_ibu_kandung']);
        });
    }
};
