<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIqroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_iqro' => ['required', 'string', 'max:255', 'unique:iqros,nama_iqro'],
            'jumlah_halaman' => ['required', 'integer', 'min:1', 'max:1000'],
            'ket' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_iqro' => 'nama iqro',
            'jumlah_halaman' => 'jumlah halaman',
            'ket' => 'keterangan',
        ];
    }
}
