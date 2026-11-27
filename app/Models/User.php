<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /* ===============================
       Connexion & table (ZKBio)
       =============================== */
    protected $connection = 'pgsql';     // biotime
    protected $table = 'auth_user';
    protected $primaryKey = 'id';
    public $timestamps = false;           // pas de created_at / updated_at

    /* ===============================
       Champs autorisés (fillable)
       =============================== */
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'emp_pin',
        'email',
        'tele_phone',
        'auth_time_dept',
        'login_id',
        'login_type',
        'login_count',
        'is_staff',
        'is_active',
        'is_superuser',
        'is_public',
        'can_manage_all_dept',
        'del_flag',
        'date_joined',
        'last_login',
        'session_key',
        'login_ip',
        'photo',
        'assign_company',
    ];

    /* ===============================
       Champs cachés
       =============================== */
    protected $hidden = [
        'password',
        'session_key',
    ];

    /* ===============================
       Casts (types corrects)
       =============================== */
    protected $casts = [
        'is_staff' => 'boolean',
        'is_active' => 'boolean',
        'is_superuser' => 'boolean',
        'is_public' => 'boolean',
        'can_manage_all_dept' => 'boolean',
        'date_joined' => 'datetime',
        'last_login' => 'datetime',
    ];

    /* ===============================
       Désactiver remember_token
       =============================== */
    public function getRememberTokenName()
    {
        return null;
    }

    /* ===============================
       Helpers utiles
       =============================== */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_superuser;
    }

    public function isStaff(): bool
    {
        return (bool) $this->is_staff;
    }
}
