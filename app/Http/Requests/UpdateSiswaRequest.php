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

    public function prepareForValidation(): void
    {
        if (!$this->filled('nama_orang_tua')) {
            $parts = array_filter([$this->input('nama_ayah_kandung'), $this->input('nama_ibu_kandung')]);
            if (!empty($parts)) {
                $this->merge(['nama_orang_tua' => implode(' / ', $parts)]);
            }
        }
        if (!$this->filled('nama_ayah_kandung') && $this->filled('nama_orang_tua')) {
            $this->merge(['nama_ayah_kandung' => $this->input('nama_orang_tua')]);
        }
    }

    public function rules(): array
    {
        $siswa = $this->route('siswa');
        $siswaId = $siswa instanceof Siswa ? $siswa->id : $siswa;

        return [
            'nis' => ['required', 'string', 'max:50', Rule::unique('siswas', 'nis')->ignore($siswaId)],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah_kandung' => ['nullable', 'string', 'max:255'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'nama_orang_tua' => ['nullable', 'string', 'max:255'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'no_hp_ortu' => ['nullable', 'string', 'max:30'],
            'kelas_id' => ['required', 'exists:kelas,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (!$this->filled('nama_ayah_kandung') && !$this->filled('nama_ibu_kandung') && !$this->filled('nama_orang_tua')) {
                $v->errors()->add('nama_ayah_kandung', 'Nama ayah atau nama ibu kandung wajib diisi.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'nis' => 'NIS',
            'nama_siswa' => 'nama santri',
            'jenis_kelamin' => 'jenis kelamin',
            'alamat' => 'alamat',
            'nama_ayah_kandung' => 'nama ayah kandung',
            'nama_ibu_kandung' => 'nama ibu kandung',
            'nama_orang_tua' => 'nama orang tua',
            'nama_wali' => 'nama wali',
            'no_hp_ortu' => 'nomor HP orang tua',
            'kelas_id' => 'kelas',
        ];
    }
}
