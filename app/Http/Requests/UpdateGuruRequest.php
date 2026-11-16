<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        $guru = $this->route('guru');
        return $this->user()?->can('update', $guru) ?? false;
    }

    public function rules(): array
    {
        $guru = $this->route('guru');
        $guruId = $guru instanceof User ? $guru->id : $guru;

        return [
            'nip' => ['required', 'string', 'max:50', Rule::unique('users', 'nip')->ignore($guruId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guruId)],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'in:admin,guru'],
            'kelas_ids' => ['nullable', 'array'],
            'kelas_ids.*' => ['exists:kelas,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $guru = $this->route('guru');
            $targetUser = $guru instanceof User ? $guru : User::find($guru);
            $currentUser = $this->user();
            $roles = $this->input('roles', []);

            if ($targetUser && $currentUser && $targetUser->id === $currentUser->id) {
                if (!in_array('admin', $roles)) {
                    $validator->errors()->add('roles', 'Anda tidak boleh mencabut role admin dari diri Anda sendiri.');
                }
            }

            if ($targetUser && $targetUser->hasRole('admin') && !in_array('admin', $roles)) {
                $totalAdmin = User::role('admin')->count();
                if ($totalAdmin <= 1) {
                    $validator->errors()->add('roles', 'Sistem tidak boleh sampai tanpa admin sama sekali.');
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'nip' => 'NIP',
            'name' => 'nama lengkap',
            'email' => 'alamat email',
            'alamat' => 'alamat',
            'no_hp' => 'nomor HP',
            'password' => 'kata sandi',
            'roles' => 'peran (role)',
            'kelas_ids' => 'kelas yang diampu',
        ];
    }
}
