<?php

namespace App\Http\Requests;

use App\Enums\Penilaian;
use App\Models\Siswa;
use App\Models\Surat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class HafalanSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $record = $this->route('hafalan_surat') ?? $this->route('surat');
        if (is_numeric($record)) {
            $record = \App\Models\HafalanSurat::find($record);
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
            'ayat_mulai' => ['nullable', 'integer', 'min:1'],
            'ayat_selesai' => ['nullable', 'integer', 'gte:ayat_mulai'],
            'tgl_hafalan' => ['nullable', 'date', 'before_or_equal:today'],
            'tgl_pengujian' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:tgl_hafalan'],
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
            'ayat_mulai.min' => 'Ayat mulai minimal 1.',
            'ayat_selesai.gte' => 'Ayat selesai harus lebih besar atau sama dengan ayat mulai.',
            'tgl_hafalan.before_or_equal' => 'Tanggal mulai hafalan tidak boleh melebihi hari ini.',
            'tgl_pengujian.required' => 'Tanggal pengujian wajib diisi.',
            'tgl_pengujian.before_or_equal' => 'Tanggal pengujian tidak boleh melebihi hari ini.',
            'tgl_pengujian.after_or_equal' => 'Tanggal pengujian harus setelah atau sama dengan tanggal mulai hafalan.',
            'nilai.required' => 'Penilaian wajib dipilih.',
            'nilai.enum' => 'Pilihan nilai tidak valid (harus BB, MB, BSH, atau BSB).',
            'ket.max' => 'Keterangan maksimal 255 karakter.',
        ];
    }
}
