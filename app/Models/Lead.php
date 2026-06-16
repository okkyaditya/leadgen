<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    const PRODUCTS = ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor'];
    const LEAD_TYPES = ['Tanya-tanya', 'Thinking', 'Negotiation', 'Cancel', 'Lose deal', 'Survey', 'Reject', 'Funding'];

    protected $fillable = [
        'nama', 'telepon', 'nik', 'produk', 'ntf', 
        'unit', 'no_unit', 'owner_type', 'owner_id', 
        'input_by', 'source_mitra_id', 'cabang', 'domisili', 'tipe_lead'
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function sourceMitra()
    {
        return $this->belongsTo(User::class, 'source_mitra_id');
    }
}
