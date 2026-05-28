<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasName;

class User extends Authenticatable implements HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama',
        'nik',
        'telepon',
        'role',
        'cabang',
        'hire_date',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted()
    {
        static::saved(function ($user) {
            if ($user->role) {
                $user->syncRoles([$user->role]);
            }
        });
    }

    public function getFilamentName(): string
    {
        return $this->nama;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'hire_date' => 'date',
        ];
    }

    public function mitra()
    {
        return $this->hasMany(Mitra::class, 'upline_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'input_by');
    }
}
