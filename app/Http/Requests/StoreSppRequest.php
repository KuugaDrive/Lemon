<?php

namespace App\Http\Requests;

use App\Models\PembayaranSpp;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSppRequest extends FormRequest
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
            'siswa_id' => ['required', 'exists:siswas,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'between:2020,2100'],
            'nominal' => ['required', 'integer', 'min:1', 'max:100000000'],
            'tgl_bayar' => ['required', 'date', 'before_or_equal:today'],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('siswa_id') && $this->filled('bulan') && $this->filled('tahun')) {
                $defaultNominal = (int) \App\Models\Pengaturan::get('spp_nominal_default', 150000);
                $totalBayar = (int) PembayaranSpp::where('siswa_id', $this->input('siswa_id'))
                    ->where('bulan', $this->input('bulan'))
                    ->where('tahun', $this->input('tahun'))
                    ->sum('nominal');

                if ($totalBayar >= $defaultNominal) {
                    $namaBulan = Carbon::createFromDate(null, (int) $this->input('bulan'), 1)->translatedFormat('F');
                    $terbayarRp = number_format($totalBayar, 0, ',', '.');
                    $validator->errors()->add(
                        'bulan',
                        "SPP santri ini untuk periode {$namaBulan} {$this->input('tahun')} sudah Lunas (Total terbayar: Rp {$terbayarRp})."
                    );
                    return;
                }

                $sisaTagihan = $defaultNominal - $totalBayar;
                $inputNominal = (int) $this->input('nominal');
                if ($inputNominal > $sisaTagihan) {
                    $sisaRp = number_format($sisaTagihan, 0, ',', '.');
                    $inputRp = number_format($inputNominal, 0, ',', '.');
                    $validator->errors()->add(
                        'nominal',
                        "Nominal pembayaran (Rp {$inputRp}) tidak boleh melebihi sisa tagihan santri (Maksimal: Rp {$sisaRp})."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Santri wajib dipilih.',
            'siswa_id.exists' => 'Santri tidak valid.',
            'bulan.required' => 'Bulan wajib dipilih.',
            'bulan.between' => 'Bulan harus antara 1 sampai 12.',
            'tahun.required' => 'Tahun wajib diisi.',
            'nominal.required' => 'Nominal pembayaran wajib diisi.',
            'nominal.integer' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal harus lebih dari 0.',
            'tgl_bayar.required' => 'Tanggal bayar wajib diisi.',
            'tgl_bayar.before_or_equal' => 'Tanggal bayar tidak boleh melebihi hari ini.',
            'ket.max' => 'Keterangan maksimal 255 karakter.',
        ];
    }
}
