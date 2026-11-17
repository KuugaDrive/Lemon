<?php

namespace App\Enums;

enum Penilaian: string
{
    case BB = 'BB';   // Belum Berkembang
    case MB = 'MB';   // Mulai Berkembang
    case BSH = 'BSH'; // Berkembang Sesuai Harapan
    case BSB = 'BSB'; // Berkembang Sangat Baik

    public function label(): string
    {
        return match ($this) {
            self::BB => 'BB (Belum Berkembang)',
            self::MB => 'MB (Mulai Berkembang)',
            self::BSH => 'BSH (Berkembang Sesuai Harapan)',
            self::BSB => 'BSB (Berkembang Sangat Baik)',
        };
    }

    public function shortLabel(): string
    {
        return $this->value;
    }

    public function deskripsi(): string
    {
        return match ($this) {
            self::BB => 'Belum Berkembang',
            self::MB => 'Mulai Berkembang',
            self::BSH => 'Berkembang Sesuai Harapan',
            self::BSB => 'Berkembang Sangat Baik',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BB => 'bg-rose-100 text-rose-800 border-rose-200',
            self::MB => 'bg-amber-100 text-amber-800 border-amber-200',
            self::BSH => 'bg-blue-100 text-blue-800 border-blue-200',
            self::BSB => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    public static function options(): array
    {
        return [
            self::BB->value => self::BB->label(),
            self::MB->value => self::MB->label(),
            self::BSH->value => self::BSH->label(),
            self::BSB->value => self::BSB->label(),
        ];
    }
}
