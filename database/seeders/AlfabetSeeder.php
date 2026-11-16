<?php

namespace Database\Seeders;

use App\Models\Alfabet;
use Illuminate\Database\Seeder;

class AlfabetSeeder extends Seeder
{
    public function run(): void
    {
        $hurufList = [
            ['nama_alfabet' => 'Alif', 'ket' => 'ا'],
            ['nama_alfabet' => 'Ba', 'ket' => 'ب'],
            ['nama_alfabet' => 'Ta', 'ket' => 'ت'],
            ['nama_alfabet' => 'Tsa', 'ket' => 'ث'],
            ['nama_alfabet' => 'Jim', 'ket' => 'ج'],
            ['nama_alfabet' => 'Ha', 'ket' => 'ح'],
            ['nama_alfabet' => 'Kho', 'ket' => 'خ'],
            ['nama_alfabet' => 'Dal', 'ket' => 'د'],
            ['nama_alfabet' => 'Dzal', 'ket' => 'ذ'],
            ['nama_alfabet' => 'Ro', 'ket' => 'ر'],
            ['nama_alfabet' => 'Zai', 'ket' => 'ز'],
            ['nama_alfabet' => 'Sin', 'ket' => 'س'],
            ['nama_alfabet' => 'Syin', 'ket' => 'ش'],
            ['nama_alfabet' => 'Shod', 'ket' => 'ص'],
            ['nama_alfabet' => 'Dhod', 'ket' => 'ض'],
            ['nama_alfabet' => 'Tho', 'ket' => 'ط'],
            ['nama_alfabet' => 'Zho', 'ket' => 'ظ'],
            ['nama_alfabet' => "'Ain", 'ket' => 'ع'],
            ['nama_alfabet' => 'Ghain', 'ket' => 'غ'],
            ['nama_alfabet' => 'Fa', 'ket' => 'ف'],
            ['nama_alfabet' => 'Qof', 'ket' => 'ق'],
            ['nama_alfabet' => 'Kaf', 'ket' => 'ك'],
            ['nama_alfabet' => 'Lam', 'ket' => 'ل'],
            ['nama_alfabet' => 'Mim', 'ket' => 'م'],
            ['nama_alfabet' => 'Nun', 'ket' => 'ن'],
            ['nama_alfabet' => 'Wawu', 'ket' => 'و'],
            ['nama_alfabet' => 'Ha (Besar)', 'ket' => 'هـ'],
            ['nama_alfabet' => 'Ya', 'ket' => 'ي'],
        ];

        foreach ($hurufList as $item) {
            Alfabet::updateOrCreate(
                ['nama_alfabet' => $item['nama_alfabet']],
                ['ket' => $item['ket']]
            );
        }
    }
}
