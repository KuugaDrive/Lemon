<?php

namespace App\Http\Requests;

use App\Enums\Penilaian;
use App\Models\Iqro;
use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SetoranIqroRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $record = $this->route('setoran_iqro');
        if ($record && ! $user->canAccessSiswa($record->siswa)) {
            return false;
        }

        if ($this->filled('siswa_id')) {
            $siswa = Siswa::find($this->input('siswa_id'));
            if ($siswa && ! $user->canAccessSiswa($siswa)) {
                return false;
            }
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'exists:siswas,id'],
            'iqro_id' => ['required', 'exists:iqros,id'],
            'halaman' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'nilai' => ['required', Rule::enum(Penilaian::class)],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('iqro_id') && $this->filled('halaman')) {
                $iqro = Iqro::find($this->input('iqro_id'));
                if ($iqro && $this->input('halaman') > $iqro->jumlah_halaman) {
                    $validator->errors()->add(
                        'halaman',
                        "Nomor halaman ({$this->input('halaman')}) melebihi jumlah halaman maksimal pada {$iqro->nama_iqro} ({$iqro->jumlah_halaman} halaman)."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Santri wajib dipilih.',
            'siswa_id.exists' => 'Santri yang dipilih tidak valid.',
            'iqro_id.required' => 'Materi Iqro wajib dipilih.',
            'iqro_id.exists' => 'Materi Iqro tidak valid.',
            'halaman.required' => 'Halaman wajib diisi.',
            'halaman.integer' => 'Halaman harus berupa angka.',
            'halaman.min' => 'Halaman minimal 1.',
            'tanggal.required' => 'Tanggal setoran wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal setoran tidak boleh melebihi hari ini.',
            'nilai.required' => 'Penilaian wajib dipilih.',
            'nilai.enum' => 'Pilihan nilai tidak valid (harus BB, MB, BSH, atau BSB).',
            'ket.max' => 'Keterangan maksimal 255 karakter.',
        ];
    }
}
