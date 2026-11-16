<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kelas = $this->route('kelas') ?? $this->route('kela');
        return $this->user()?->can('update', $kelas) ?? false;
    }

    public function rules(): array
    {
        $kelas = $this->route('kelas') ?? $this->route('kela');
        $kelasId = $kelas instanceof Kelas ? $kelas->id : $kelas;

        return [
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas', 'nama_kelas')->ignore($kelasId)],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_kelas' => 'nama kelas',
            'keterangan' => 'keterangan',
        ];
    }
}
