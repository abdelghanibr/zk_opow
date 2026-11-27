<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZkUser extends Model
{
    use HasFactory;

    protected $table = 'zk_users';

    /**
     * الحقول القابلة للإدخال
     */
    protected $fillable = [
        'device_id',
        'zk_uid',
        'user_id',
        'name',
        'privilege',
        'card_number',
        'is_active',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
}