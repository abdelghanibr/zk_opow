<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class PersonnelEmployee extends Model
{
    protected $table = 'personnel_employee';
    public $timestamps = false;

    protected $fillable = [
        'emp_code',
        'first_name',
        'last_name',
        'status',
        'is_active',
        'enable_payroll',
        'department_id',
        'company_id',
        'create_time',
    ];
}
