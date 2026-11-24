<?php

namespace Database\Seeders;

use App\Models\AlfabetLatin;
use Illuminate\Database\Seeder;

class AlfabetLatinSeeder extends Seeder
{
    public function run(): void
    {
        $hurufs = range('A', 'Z');

        foreach ($hurufs as $huruf) {
            AlfabetLatin::updateOrCreate(
                ['huruf' => $huruf],
                ['ket' => "Huruf latin kapital {$huruf}"]
            );
        }
    }
}
