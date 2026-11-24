<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nomor' => ['required', 'integer', 'min:1', 'max:114', 'unique:surats,nomor'],
            'nama_surat' => ['required', 'string', 'max:255'],
            'nama_arab' => ['nullable', 'string', 'max:100'],
            'juz' => ['required', 'integer', 'min:1', 'max:30'],
            'jumlah_ayat' => ['required', 'integer', 'min:1', 'max:1000'],
            'ket' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor' => 'nomor surat',
            'nama_surat' => 'nama surat',
            'nama_arab' => 'nama Arab',
            'juz' => 'juz utama',
            'jumlah_ayat' => 'jumlah ayat',
            'ket' => 'keterangan',
        ];
    }
}
