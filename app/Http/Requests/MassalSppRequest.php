<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassalSppRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('nominal')) {
            $nominal = preg_replace('/[^\d]/', '', (string) $this->input('nominal'));
            $this->merge(['nominal' => $nominal !== '' ? (int) $nominal : null]);
        }
    }

    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasPermissionTo('kelola-spp') || $user->hasRole('admin'));
    }

    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'exists:kelas,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'between:2020,2100'],
            'nominal' => ['required', 'integer', 'min:1', 'max:100000000'],
            'tgl_bayar' => ['required', 'date', 'before_or_equal:today'],
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['exists:siswas,id'],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'bulan.required' => 'Bulan wajib dipilih.',
            'tahun.required' => 'Tahun wajib diisi.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal harus lebih dari 0.',
            'tgl_bayar.required' => 'Tanggal bayar wajib diisi.',
            'tgl_bayar.before_or_equal' => 'Tanggal bayar tidak boleh melebihi hari ini.',
            'siswa_ids.required' => 'Minimal pilih 1 santri untuk diproses.',
            'siswa_ids.min' => 'Minimal pilih 1 santri untuk diproses.',
        ];
    }
}
