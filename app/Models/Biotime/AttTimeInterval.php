<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class AttTimeInterval extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'att_timeinterval';
    public $timestamps = false;

    protected $fillable = [
        'alias',
        'in_time',
        'duration',
        'work_day',
        'company_id'
    ];
}
