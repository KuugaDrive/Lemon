<?php

namespace App\Http\Requests;

use App\Enums\Penilaian;
use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BacaBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $record = $this->route('baca_buku');
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
            'alfabet_id' => ['required', 'exists:alfabets,id'],
            'jilid' => ['required', 'string', 'max:50'],
            'halaman' => ['required', 'integer', 'min:1', 'max:1000'],
            'tgl_hafalan' => ['nullable', 'date', 'before_or_equal:today'],
            'tgl_pengujian' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:tgl_hafalan'],
            'nilai' => ['required', Rule::enum(Penilaian::class)],
            'ket' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Santri wajib dipilih.',
            'siswa_id.exists' => 'Santri yang dipilih tidak valid.',
            'alfabet_id.required' => 'Huruf Hijaiyah wajib dipilih.',
            'alfabet_id.exists' => 'Huruf Hijaiyah yang dipilih tidak valid.',
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
