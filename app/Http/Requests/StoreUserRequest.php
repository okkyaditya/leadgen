<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', User::class);
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|digits:16|regex:/^[0-9]+$/|unique:users,nik',
            'telepon' => 'required|string|max:16|regex:/^[0-9]+$/|unique:users,telepon',
            'email' => 'required|string|email|max:191|unique:users,email',
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'cabang' => 'required|string|exists:cabangs,nama',
            'hire_date' => 'required|date',
            'password' => 'required|string|min:8',
            'is_active' => 'required|boolean',
            'supervisor_id' => [
                'nullable',
                Rule::requiredIf($this->role === 'support'),
                function ($attribute, $value, $fail) {
                    if ($this->role === 'support' && $value) {
                        $supervisor = User::find($value);
                        if (!$supervisor || $supervisor->role !== 'supervisor') {
                            $fail('Supervisor tidak valid.');
                            return;
                        }
                        if (!$supervisor->is_active) {
                            $fail('Supervisor yang dipilih harus aktif.');
                            return;
                        }
                        if ($supervisor->cabang !== $this->cabang) {
                            $fail('Supervisor harus berasal dari cabang yang sama.');
                        }
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ];
    }
}
