<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama', 'telepon', 'nik', 'produk', 'ntf', 
        'unit', 'no_unit', 'owner_type', 'owner_id', 
        'input_by', 'source_mitra_id', 'cabang', 'domisili'
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
        return $this->belongsTo(Mitra::class, 'source_mitra_id');
    }
}
