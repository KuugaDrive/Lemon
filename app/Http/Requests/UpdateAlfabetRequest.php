<?php

namespace App\Http\Requests;

use App\Models\Alfabet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlfabetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $alfabet = $this->route('alfabet');
        $alfabetId = $alfabet instanceof Alfabet ? $alfabet->id : $alfabet;

        return [
            'nama_alfabet' => ['required', 'string', 'max:255', Rule::unique('alfabets', 'nama_alfabet')->ignore($alfabetId)],
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
