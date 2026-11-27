<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    /**
     * الحقول القابلة للإدخال
     */
    protected $fillable = [
        'device_id',
        'zk_uid',
        'check_time',
        'state',
        'verify_type',
        'work_code',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'check_time' => 'datetime',
        'state'      => 'integer',
        'verify_type'=> 'integer',
    ];

    /**
     * علاقة: السجل تابع لجهاز واحد
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * علاقة: السجل تابع لمستخدم ZK
     * الربط عبر zk_uid
     */
    public function zkUser()
    {
        return $this->belongsTo(
            ZkUser::class,
            'zk_uid',   // foreign key في attendances
            'zk_uid'    // local key في zk_users
        );
    }
}
