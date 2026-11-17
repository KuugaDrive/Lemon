<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanSppRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasPermissionTo('kelola-spp') || $user->hasRole('admin'));
    }

    public function rules(): array
    {
        return [
            'spp_nominal_default' => ['required', 'integer', 'min:1000'],
            'spp_jatuh_tempo_hari' => ['required', 'integer', 'between:1,31'],
        ];
    }

    public function messages(): array
    {
        return [
            'spp_nominal_default.required' => 'Nominal SPP default wajib diisi.',
            'spp_nominal_default.integer' => 'Nominal SPP default harus berupa angka.',
            'spp_nominal_default.min' => 'Nominal SPP default minimal Rp 1.000.',
            'spp_jatuh_tempo_hari.required' => 'Tanggal jatuh tempo wajib diisi.',
            'spp_jatuh_tempo_hari.between' => 'Tanggal jatuh tempo harus antara tanggal 1 sampai 31.',
        ];
    }
}
