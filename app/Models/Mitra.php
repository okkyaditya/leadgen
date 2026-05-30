<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasName;

class Mitra extends Authenticatable implements HasName
{
    use HasFactory, Notifiable;

    protected $table = 'mitra';

    protected $fillable = [
        'nik', 'password', 'nama', 'telepon', 'email', 'profesi', 
        'tanggal_lahir', 'domisili', 'upline_id', 'is_active', 
        'is_active_reason', 'last_login_at'
    ];

    protected $hidden = [
        'password',
    ];

    public function getFilamentName(): string
    {
        return $this->nama;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'tanggal_lahir' => 'date',
        ];
    }

    public function upline()
    {
        return $this->belongsTo(User::class, 'upline_id');
    }

    public function leads()
    {
        return $this->morphMany(Lead::class, 'owner');
    }
}
