<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlfabetLatinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'huruf' => ['required', 'string', 'max:10', 'unique:alfabet_latins,huruf'],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'huruf' => 'huruf latin',
            'ket' => 'keterangan',
        ];
    }
}
