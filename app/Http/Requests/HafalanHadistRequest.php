<?php

namespace App\Http\Requests;

use App\Enums\Penilaian;
use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HafalanHadistRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $record = $this->route('hafalan_hadist');
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
            'hadist_id' => ['required', 'exists:hadists,id'],
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
            'hadist_id.required' => 'Hadits wajib dipilih.',
            'hadist_id.exists' => 'Hadits yang dipilih tidak valid.',
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
