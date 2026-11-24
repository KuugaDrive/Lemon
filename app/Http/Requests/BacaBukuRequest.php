<?php

namespace App\Http\Requests;

use App\Enums\Penilaian;
use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BacaBukuRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('jenis_huruf') === 'latin') {
            $this->merge(['alfabet_id' => null]);
        } elseif ($this->input('jenis_huruf') === 'hijaiyah') {
            $this->merge(['alfabet_latin_id' => null]);
        }
    }

    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $record = $this->route('baca_buku') ?? $this->route('baca-buku');
        if (is_numeric($record)) {
            $record = \App\Models\BacaBuku::find($record);
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
            'jenis_huruf' => ['nullable', 'in:hijaiyah,latin'],
            'alfabet_id' => ['nullable', 'exists:alfabets,id', 'required_without:alfabet_latin_id', 'prohibits:alfabet_latin_id'],
            'alfabet_latin_id' => ['nullable', 'exists:alfabet_latins,id', 'required_without:alfabet_id', 'prohibits:alfabet_id'],
            'jilid' => ['required', 'string', 'max:50'],
            'halaman' => ['required', 'integer', 'min:1', 'max:1000'],
            'tgl_hafalan' => ['nullable', 'date', 'before_or_equal:today'],
            'tgl_pengujian' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:tgl_hafalan'],
            'nilai' => ['required', Rule::enum(Penilaian::class)],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $hasHijaiyah = ! empty($this->input('alfabet_id'));
            $hasLatin = ! empty($this->input('alfabet_latin_id'));

            if (! $hasHijaiyah && ! $hasLatin) {
                $validator->errors()->add('jenis_huruf', 'Pilih salah satu jenis huruf (Huruf Hijaiyah atau Huruf Latin).');
            }

            if ($hasHijaiyah && $hasLatin) {
                $validator->errors()->add('jenis_huruf', 'Hanya boleh memilih salah satu antara Huruf Hijaiyah atau Huruf Latin.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Santri wajib dipilih.',
            'siswa_id.exists' => 'Santri yang dipilih tidak valid.',
            'alfabet_id.required_without' => 'Huruf Hijaiyah atau Huruf Latin wajib dipilih.',
            'alfabet_latin_id.required_without' => 'Huruf Hijaiyah atau Huruf Latin wajib dipilih.',
            'alfabet_id.prohibits' => 'Hanya boleh memilih salah satu jenis huruf.',
            'alfabet_latin_id.prohibits' => 'Hanya boleh memilih salah satu jenis huruf.',
            'alfabet_id.exists' => 'Huruf Hijaiyah yang dipilih tidak valid.',
            'alfabet_latin_id.exists' => 'Huruf Latin yang dipilih tidak valid.',
            'jilid.required' => 'Jilid wajib diisi.',
            'jilid.max' => 'Jilid maksimal 50 karakter.',
            'halaman.required' => 'Halaman wajib diisi.',
            'halaman.min' => 'Halaman minimal 1.',
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
