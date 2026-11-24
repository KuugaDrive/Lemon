<?php

namespace App\Http\Requests;

use App\Enums\Penilaian;
use App\Models\Siswa;
use App\Models\Surat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SetoranQuranRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $record = $this->route('setoran_quran') ?? $this->route('quran');
        if (is_numeric($record)) {
            $record = \App\Models\SetoranQuran::find($record);
        }
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
            'surat_id' => ['required', 'exists:surats,id'],
            'juz' => ['required', 'integer', 'min:1', 'max:30'],
            'ayat_mulai' => ['required', 'integer', 'min:1'],
            'ayat_selesai' => ['required', 'integer', 'gte:ayat_mulai'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'nilai' => ['required', Rule::enum(Penilaian::class)],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('surat_id') && $this->filled('ayat_selesai')) {
                $surat = Surat::find($this->input('surat_id'));
                if ($surat && $this->input('ayat_selesai') > $surat->jumlah_ayat) {
                    $validator->errors()->add(
                        'ayat_selesai',
                        "Ayat selesai ({$this->input('ayat_selesai')}) melebihi total ayat pada Surat {$surat->nama_surat} ({$surat->jumlah_ayat} ayat)."
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
            'surat_id.required' => 'Surat Al-Qur\'an wajib dipilih.',
            'surat_id.exists' => 'Surat yang dipilih tidak valid.',
            'juz.required' => 'Juz wajib diisi.',
            'juz.min' => 'Juz minimal 1.',
            'juz.max' => 'Juz maksimal 30.',
            'ayat_mulai.required' => 'Ayat mulai wajib diisi.',
            'ayat_mulai.min' => 'Ayat mulai minimal 1.',
            'ayat_selesai.required' => 'Ayat selesai wajib diisi.',
            'ayat_selesai.gte' => 'Ayat selesai harus lebih besar atau sama dengan ayat mulai.',
            'tanggal.required' => 'Tanggal setoran wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal setoran tidak boleh melebihi hari ini.',
            'nilai.required' => 'Penilaian wajib dipilih.',
            'nilai.enum' => 'Pilihan nilai tidak valid (harus BB, MB, BSH, atau BSB).',
            'ket.max' => 'Keterangan maksimal 255 karakter.',
        ];
    }
}
