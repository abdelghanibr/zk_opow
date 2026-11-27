<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class PersonnelArea extends Model
{
    protected $table = 'personnel_area';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'alias',
        'parent_id',
        'company_id',
        'is_active',
    ];
}
