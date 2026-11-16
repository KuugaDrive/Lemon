<?php

namespace App\Http\Requests;

use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $siswa = $this->route('siswa');
        return $this->user()?->can('update', $siswa) ?? false;
    }

    public function rules(): array
    {
        $siswa = $this->route('siswa');
        $siswaId = $siswa instanceof Siswa ? $siswa->id : $siswa;

        return [
            'nis' => ['required', 'string', 'max:50', Rule::unique('siswas', 'nis')->ignore($siswaId)],
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
