<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class AttAttShift extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'att_attshift';
    public $timestamps = false;

    protected $fillable = [
        'alias',
        'cycle_unit',
        'shift_cycle',
        'company_id'
    ];

    public function details()
    {
        return $this->hasMany(AttShiftDetail::class, 'shift_id');
    }
}
