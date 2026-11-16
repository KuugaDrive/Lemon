<?php

namespace App\Http\Requests;

use App\Models\Surat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $surat = $this->route('surat');
        $suratId = $surat instanceof Surat ? $surat->id : $surat;

        return [
            'nomor' => ['required', 'integer', 'min:1', 'max:114', Rule::unique('surats', 'nomor')->ignore($suratId)],
            'nama_surat' => ['required', 'string', 'max:255'],
            'juz' => ['required', 'integer', 'min:1', 'max:30'],
            'jumlah_ayat' => ['required', 'integer', 'min:1', 'max:1000'],
            'ket' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor' => 'nomor surat',
            'nama_surat' => 'nama surat',
            'juz' => 'juz utama',
            'jumlah_ayat' => 'jumlah ayat',
            'ket' => 'keterangan',
        ];
    }
}
