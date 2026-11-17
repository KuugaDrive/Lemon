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
        return [
            'nis' => ['required', 'string', 'max:50', 'unique:siswas,nis'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah_kandung' => ['nullable', 'string', 'max:255'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'nama_orang_tua' => ['nullable', 'string', 'max:255'],
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
            'alamat' => 'alamat',
            'nama_ayah_kandung' => 'nama ayah kandung',
            'nama_ibu_kandung' => 'nama ibu kandung',
            'nama_orang_tua' => 'nama orang tua',
            'no_hp_ortu' => 'nomor HP orang tua',
            'kelas_id' => 'kelas',
        ];
    }
}
