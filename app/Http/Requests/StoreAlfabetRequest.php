<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlfabetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_alfabet' => ['required', 'string', 'max:255', 'unique:alfabets,nama_alfabet'],
            'ket' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_alfabet' => 'nama huruf / alfabet',
            'ket' => 'keterangan / huruf hijaiyah',
        ];
    }
}
