<?php

namespace App\Enums;

enum StatusPresensi: string
{
    case Hadir = 'hadir';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alfa = 'alfa';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Izin => 'Izin',
            self::Sakit => 'Sakit',
            self::Alfa => 'Alfa',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Hadir => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Izin => 'bg-blue-100 text-blue-800 border-blue-200',
            self::Sakit => 'bg-amber-100 text-amber-800 border-amber-200',
            self::Alfa => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public static function options(): array
    {
        return [
            self::Hadir->value => self::Hadir->label(),
            self::Izin->value => self::Izin->label(),
            self::Sakit->value => self::Sakit->label(),
            self::Alfa->value => self::Alfa->label(),
        ];
    }
}
