<?php

namespace App\Http\Requests;

use App\Models\Iqro;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIqroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $iqro = $this->route('iqro');
        $iqroId = $iqro instanceof Iqro ? $iqro->id : $iqro;

        return [
            'nama_iqro' => ['required', 'string', 'max:255', Rule::unique('iqros', 'nama_iqro')->ignore($iqroId)],
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
