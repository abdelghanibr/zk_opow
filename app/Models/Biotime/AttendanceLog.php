<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    /* ===============================
       Connexion & table
       =============================== */
    protected $connection = 'pgsql';     // biotime
    protected $table = 'iclock_transaction';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /* ===============================
       Champs autorisés
       =============================== */
    protected $fillable = [
        'emp_code',
        'punch_time',
        'punch_state',
        'verify_type',
        'work_code',
        'terminal_sn',
        'terminal_alias',
        'area_alias',
        'longitude',
        'latitude',
        'gps_location',
        'mobile',
        'source',
        'purpose',
        'crc',
        'is_attendance',
        'reserved',
        'upload_time',
        'sync_status',
        'sync_time',
        'is_mask',
        'temperature',
        'emp_id',
        'terminal_id',
        'company_code',
    ];

    /* ===============================
       Casts
       =============================== */
    protected $casts = [
        'punch_time' => 'datetime',
        'upload_time' => 'datetime',
        'sync_time' => 'datetime',
        'temperature' => 'decimal:1',
        'longitude' => 'float',
        'latitude' => 'float',
    ];

    /* ===============================
       Relations
       =============================== */

    // 🔗 Employé
    public function employee()
    {
        return $this->belongsTo(
            PersonnelEmployee::class,
            'emp_id'
        );
    }

    // 🔗 Terminal
    public function terminal()
    {
        return $this->belongsTo(
            IclockTerminal::class,
            'terminal_id'
        );
    }

    /* ===============================
       Helpers utiles
       =============================== */

    public function isCheckIn(): bool
    {
        // حسب ZKTeco: غالبًا 0 = IN ، 1 = OUT
        return in_array($this->punch_state, ['0', 'IN']);
    }

    public function isCheckOut(): bool
    {
        return in_array($this->punch_state, ['1', 'OUT']);
    }
}
