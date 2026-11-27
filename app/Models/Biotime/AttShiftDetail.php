<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class AttShiftDetail extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'att_shiftdetail';
    public $timestamps = false;

    protected $fillable = [
        'in_time',
        'out_time',
        'day_index',
        'shift_id',
        'time_interval_id'
    ];

    public function shift()
    {
        return $this->belongsTo(AttAttShift::class, 'shift_id');
    }

    public function timeInterval()
    {
        return $this->belongsTo(AttTimeInterval::class, 'time_interval_id');
    }
}
