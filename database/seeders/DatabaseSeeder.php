<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            KelasSeeder::class,
            UserSeeder::class,
            SiswaSeeder::class,
            IqroSeeder::class,
            SuratSeeder::class,
            AlfabetSeeder::class,
            AlfabetLatinSeeder::class,
            DoaSeeder::class,
            HadistSeeder::class,
            SetoranHafalanSeeder::class,
        ]);
    }
}
