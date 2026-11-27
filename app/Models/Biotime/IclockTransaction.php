<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class IclockTransaction extends Model
{
    protected $table = 'iclock_transaction';

    public $timestamps = false; // ZKBio يدير التواريخ

    protected $fillable = [
        'emp_code',
        'punch_time',
        'punch_state',
        'verify_type',
        'terminal_sn',
        'terminal_alias',
        'area_alias',
        'source',
        'purpose',
        'is_attendance',
        'temperature',
        'emp_id',
        'terminal_id',
        'company_code'
    ];

    /* =====================
       Relations utiles
       ===================== */

    public function employee()
    {
        return $this->belongsTo(
            \App\Models\Biotime\PersonnelEmployee::class,
            'emp_id'
        );
    }

    public function terminal()
    {
        return $this->belongsTo(
            \App\Models\Biotime\IclockTerminal::class,
            'terminal_id'
        );
    }

    /* =====================
       Helpers
       ===================== */

    public function getPunchTypeLabelAttribute()
    {
        return $this->punch_state == 0 ? 'دخول' : 'خروج';
    }
}
