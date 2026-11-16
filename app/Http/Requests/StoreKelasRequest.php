<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Kelas::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => ['required', 'string', 'max:255', 'unique:kelas,nama_kelas'],
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
