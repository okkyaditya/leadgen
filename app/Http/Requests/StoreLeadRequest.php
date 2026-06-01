<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Lead;

class StoreLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:150',
            'telepon' => 'required|string|max:16|regex:/^[0-9]+$/',
            'nik' => 'required|string|max:16|regex:/^[0-9]+$/',
            'produk' => ['required', 'string', Rule::in(Lead::PRODUCTS)],
            'tipe_lead' => ['required', 'string', Rule::in(Lead::LEAD_TYPES)],
            'ntf' => 'nullable|numeric',
            'unit' => 'nullable|string|max:100',
            'no_unit' => 'nullable|string|max:50',
            'domisili' => 'required|string|max:150',
            'source_mitra_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
        ];
    }
}
