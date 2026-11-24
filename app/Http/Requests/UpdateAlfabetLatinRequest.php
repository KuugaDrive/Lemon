<?php

namespace App\Http\Requests;

use App\Models\AlfabetLatin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlfabetLatinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $alfabetLatin = $this->route('alfabet_latin');
        $id = $alfabetLatin instanceof AlfabetLatin ? $alfabetLatin->id : $alfabetLatin;

        return [
            'huruf' => ['required', 'string', 'max:10', Rule::unique('alfabet_latins', 'huruf')->ignore($id)],
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
