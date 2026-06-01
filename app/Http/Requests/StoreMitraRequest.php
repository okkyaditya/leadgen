<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreMitraRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('createMitra', User::class);
    }

    public function rules()
    {
        $currentUser = auth()->user();

        $rules = [
            'nama' => 'required|string|max:150',
            'nik' => 'required|string|digits:16|regex:/^[0-9]+$/|unique:users,nik',
            'telepon' => 'required|string|max:16|regex:/^[0-9]+$/|unique:users,telepon',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'required|string|min:8',
            'profesi' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'domisili' => 'required|string|max:150',
            'is_active' => 'required|boolean',
        ];

        if (!$currentUser->hasAnyRole(['supervisor', 'support'])) {
            $rules['supervisor_id'] = [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($currentUser) {
                    $upline = User::find($value);
                    if (!$upline || !in_array($upline->role, ['supervisor', 'support'])) {
                        $fail('Upline harus memiliki role Supervisor atau Support.');
                        return;
                    }
                    if (!$upline->is_active) {
                        $fail('Upline yang dipilih tidak aktif.');
                        return;
                    }
                    if ($currentUser->hasRole('manager') && $currentUser->cabang && $upline->cabang !== $currentUser->cabang) {
                        $fail('Upline harus berada di cabang yang sama dengan Manager.');
                    }
                }
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ];
    }
}
