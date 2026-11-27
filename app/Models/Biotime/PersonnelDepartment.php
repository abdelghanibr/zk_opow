<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;
use App\Models\PersonnelEmployee;
use App\Models\PersonnelArea;

class PersonnelDepartment extends Model
{
    protected $table = 'personnel_department';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'alias',
        'parent_id',
        'company_id',
        'is_active',
    ];

    /* 🔗 Employees in this department */
    public function employees()
    {
        return $this->hasMany(
            PersonnelEmployee::class,
            'department_id',
            'id'
        );
    }

    /**
     * 🔗 Areas via employees
     * (Indirect relationship)
     */
    public function areas()
    {
        return $this->hasManyThrough(
            PersonnelArea::class,
            PersonnelEmployee::class,
            'department_id', // FK in personnel_employee
            'id',            // PK in personnel_area
            'id',            // PK in personnel_department
            'area_id'        // FK in pivot (if exists in employee)
        );
    }
}

