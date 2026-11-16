<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHadistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_hadist' => ['required', 'string', 'max:255'],
            'baca_latin' => ['required', 'string'],
            'artinya' => ['required', 'string'],
            'ket' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_hadist' => 'nama hadits',
            'baca_latin' => 'bacaan latin',
            'artinya' => 'arti / terjemahan',
            'ket' => 'keterangan',
        ];
    }
}
