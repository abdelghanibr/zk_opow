<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class AttAttSchedule extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'att_attschedule';
    public $timestamps = false;

    protected $fillable = [
        'start_date',
        'end_date',
        'employee_id',
        'shift_id'
    ];

    public function employee()
    {
        return $this->belongsTo(PersonnelEmployee::class, 'employee_id');
    }

    public function shift()
    {
        return $this->belongsTo(AttAttShift::class, 'shift_id');
    }
}
