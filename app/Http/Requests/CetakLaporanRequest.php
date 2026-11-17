<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CetakLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        // Khusus SPP: hanya admin atau user dengan permission kelola-spp
        if ($this->input('jenis') === 'spp') {
            if (! ($user->hasPermissionTo('kelola-spp') || $user->hasRole('admin'))) {
                return false;
            }
        }

        // Pengecekan hak akses siswa
        if ($this->input('mode') === 'siswa' && $this->filled('siswa_id')) {
            $siswa = Siswa::find($this->input('siswa_id'));
            if ($siswa && ! $user->canAccessSiswa($siswa)) {
                return false;
            }
        }

        // Pengecekan hak akses kelas
        if ($this->input('mode') === 'kelas' && $this->filled('kelas_id')) {
            $kelas = Kelas::find($this->input('kelas_id'));
            if ($kelas && ! $user->canAccessKelas($kelas)) {
                return false;
            }
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'jenis' => ['required', Rule::in(['presensi', 'iqro', 'quran', 'doa', 'hadist', 'surat', 'baca_buku', 'spp'])],
            'mode' => ['required', Rule::in(['siswa', 'kelas'])],
            'siswa_id' => ['required_if:mode,siswa', 'nullable', 'exists:siswas,id'],
            'kelas_id' => ['required_if:mode,kelas', 'nullable', 'exists:kelas,id'],
            'dari_tanggal' => ['required', 'date'],
            'sampai_tanggal' => ['required', 'date', 'after_or_equal:dari_tanggal'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('dari_tanggal') && $this->filled('sampai_tanggal')) {
                $start = Carbon::parse($this->input('dari_tanggal'));
                $end = Carbon::parse($this->input('sampai_tanggal'));

                // Maksimal rentang 366 hari untuk mencegah timeout
                if ($start->diffInDays($end) > 366) {
                    $validator->errors()->add(
                        'sampai_tanggal',
                        'Rentang tanggal laporan maksimal 1 tahun (366 hari).'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis laporan wajib dipilih.',
            'jenis.in' => 'Jenis laporan tidak valid.',
            'mode.required' => 'Mode laporan wajib dipilih.',
            'mode.in' => 'Mode laporan harus per siswa atau per kelas.',
            'siswa_id.required_if' => 'Santri wajib dipilih untuk laporan per santri.',
            'siswa_id.exists' => 'Santri tidak valid.',
            'kelas_id.required_if' => 'Kelas wajib dipilih untuk rekapitulasi kelas.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'dari_tanggal.required' => 'Tanggal awal wajib diisi.',
            'sampai_tanggal.required' => 'Tanggal akhir wajib diisi.',
            'sampai_tanggal.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
        ];
    }
}
