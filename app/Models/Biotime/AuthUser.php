<?php
// app/Models/Biotime/AuthUser.php


namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class AuthUser extends Model
{
    protected $table = 'auth_user';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'email',
        'emp_pin',
        'is_active',
        'is_staff',
        'is_superuser',
        'is_public',
        'can_manage_all_dept',
        'date_joined',
    ];
}
