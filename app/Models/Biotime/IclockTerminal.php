<?php

namespace App\Models\Biotime;

use Illuminate\Database\Eloquent\Model;

class IclockTerminal extends Model
{
    /* ===============================
       Connexion & table
       =============================== */
    protected $connection = 'pgsql';     // biotime
    protected $table = 'iclock_terminal';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /* ===============================
       Champs autorisés
       =============================== */
    protected $fillable = [
        'create_time',
        'create_user',
        'change_time',
        'change_user',
        'status',
        'sn',
        'alias',
        'ip_address',
        'real_ip',
        'state',
        'terminal_tz',
        'heartbeat',
        'transfer_mode',
        'transfer_interval',
        'transfer_time',
        'product_type',
        'is_attendance',
        'is_registration',
        'purpose',
        'controller_type',
        'authentication',
        'style',
        'upload_flag',
        'fw_ver',
        'push_protocol',
        'push_ver',
        'language',
        'is_tft',
        'terminal_name',
        'platform',
        'oem_vendor',
        'log_stamp',
        'op_log_stamp',
        'capture_stamp',
        'user_count',
        'transaction_count',
        'fp_count',
        'fp_alg_ver',
        'face_count',
        'face_alg_ver',
        'fv_count',
        'fv_alg_ver',
        'palm_count',
        'palm_alg_ver',
        'lock_func',
        'last_activity',
        'upload_time',
        'push_time',
        'is_access',
        'area_id',
    ];

    /* ===============================
       Casts corrects
       =============================== */
    protected $casts = [
        'create_time'   => 'datetime',
        'change_time'   => 'datetime',
        'last_activity' => 'datetime',
        'upload_time'   => 'datetime',
        'push_time'     => 'datetime',
        'is_tft'        => 'boolean',
        'ip_address'    => 'string',   // inet
        'real_ip'       => 'string',   // inet
    ];

    /* ===============================
       Relations
       =============================== */

    // Zone / Area
    public function area()
    {
        return $this->belongsTo(
            \App\Models\Biotime\PersonnelArea::class,
            'area_id'
        );
    }

    /* ===============================
       Helpers utiles
       =============================== */

    public function isOnline(): bool
    {
        return $this->state === 1;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->alias ?: $this->sn;
    }
}
