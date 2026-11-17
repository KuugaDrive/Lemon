<?php

namespace App\Http\Requests;

use App\Enums\StatusPresensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class SimpanPresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        // Cek apakah kelas_id boleh diakses oleh user ini
        $kelasId = $this->input('kelas_id');
        if ($kelasId && ! Kelas::visibleTo($user)->where('id', $kelasId)->exists()) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'presensi' => ['required', 'array', 'min:1'],
            'presensi.*.siswa_id' => ['required', 'integer'],
            'presensi.*.status' => ['required', new Enum(StatusPresensi::class)],
            'presensi.*.ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $kelasId = $this->input('kelas_id');
            $presensiData = $this->input('presensi', []);

            if (!$kelasId || empty($presensiData)) {
                return;
            }

            // Validasi bahwa semua siswa_id benar-benar merupakan anggota kelas tersebut dan lolos visibleTo
            $siswaIds = collect($presensiData)->pluck('siswa_id')->all();
            $validSiswaCount = Siswa::visibleTo($user)
                ->where('kelas_id', $kelasId)
                ->whereIn('id', $siswaIds)
                ->count();

            if ($validSiswaCount !== count(array_unique($siswaIds))) {
                $validator->errors()->add(
                    'presensi',
                    'Terdapat data santri yang bukan anggota kelas ini atau Anda tidak memiliki hak akses terhadapnya.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'kelas_id' => 'kelas',
            'tanggal' => 'tanggal presensi',
            'presensi' => 'data presensi',
            'presensi.*.siswa_id' => 'santri',
            'presensi.*.status' => 'status presensi',
            'presensi.*.ket' => 'keterangan',
        ];
    }
}
