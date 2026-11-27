<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Biotime\IclockTerminal;
use App\Models\Biotime\PersonnelEmployee;
use App\Models\Biotime\AttendanceLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $devicesCount = IclockTerminal::count();

    // ✅ PostgreSQL BOOLEAN correct
    $usersCount = PersonnelEmployee::whereRaw('is_active = TRUE')->count();

    $logsToday = AttendanceLog::whereDate(
        'punch_time',
        Carbon::today()
    )->count();

    $latestLogs = AttendanceLog::with([
            'employee:id,emp_code,first_name,last_name',
            'terminal:id,alias'
        ])
        ->orderByDesc('punch_time')
        ->limit(10)
        ->get();

    return view('dashboard', compact(
        'devicesCount',
        'usersCount',
        'logsToday',
        'latestLogs'
    ));
}

}
