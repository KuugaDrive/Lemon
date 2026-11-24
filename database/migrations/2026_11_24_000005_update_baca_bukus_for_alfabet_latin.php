<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baca_bukus', function (Blueprint $table) {
            $table->unsignedBigInteger('alfabet_id')->nullable()->change();
            $table->foreignId('alfabet_latin_id')
                ->nullable()
                ->after('alfabet_id')
                ->constrained('alfabet_latins')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('baca_bukus', function (Blueprint $table) {
            $table->dropForeign(['alfabet_latin_id']);
            $table->dropColumn('alfabet_latin_id');
            $table->unsignedBigInteger('alfabet_id')->nullable(false)->change();
        });
    }
};
