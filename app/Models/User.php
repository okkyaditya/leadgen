<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    const ROLES = ['admin', 'manager', 'supervisor', 'support'];
    const ALL_ROLES = ['admin', 'manager', 'supervisor', 'support', 'mitra'];

    protected $fillable = [
        'nama',
        'nik',
        'telepon',
        'email',
        'role',
        'cabang',
        'hire_date',
        'password',
        'is_active',
        'supervisor_id',
        'profesi',
        'tanggal_lahir',
        'domisili',
        'is_active_reason',
        'last_login_at',
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
        static::saving(function ($user) {
            // Automatically inherit upline's cabang for Mitra role
            if ($user->role === 'mitra' && $user->supervisor_id) {
                $upline = self::find($user->supervisor_id);
                if ($upline) {
                    $user->cabang = $upline->cabang;
                }
            }
        });

        static::saved(function ($user) {
            if ($user->role) {
                $user->syncRoles([$user->role]);
            }
        });
    }


    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'hire_date' => 'date',
            'tanggal_lahir' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id')->where('role', 'supervisor');
    }

    public function supports()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function upline()
    {
        return $this->belongsTo(User::class, 'supervisor_id')->whereIn('role', ['support', 'supervisor']);
    }

    public function downlines()
    {
        return $this->hasMany(User::class, 'supervisor_id')->where('role', 'mitra');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'input_by');
    }

    public function mitraLeads()
    {
        return $this->hasMany(Lead::class, 'source_mitra_id');
    }

    public function uplineRequests()
    {
        return $this->hasMany(UplineChangeRequest::class, 'mitra_id');
    }
}
