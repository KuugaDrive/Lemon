<?php

namespace App\Http\Requests;

use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Siswa::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:50', 'unique:siswas,nis'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nama_orang_tua' => ['required', 'string', 'max:255'],
            'no_hp_ortu' => ['nullable', 'string', 'max:30'],
            'kelas_id' => ['required', 'exists:kelas,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis' => 'NIS',
            'nama_siswa' => 'nama santri / siswa',
            'alamat' => 'alamat',
            'nama_orang_tua' => 'nama orang tua / wali',
            'no_hp_ortu' => 'nomor HP orang tua',
            'kelas_id' => 'kelas',
        ];
    }
}
