<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use App\Models\Biotime\IclockTransaction;
use App\Models\Biotime\PersonnelEmployee;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class TvPunchController extends Controller
{
    /*public function last(Request $request)
    {
        // Auto refresh (seconds)
        $refresh = (int) ($request->get('refresh', 3));
        if ($refresh < 2) $refresh = 2;
        if ($refresh > 20) $refresh = 20;

        // 🔥 آخر pointage في النظام كامل
        $lastLog = IclockTransaction::with(['employee','terminal'])
            ->where('is_attendance', 1)
            ->orderByDesc('punch_time')
            ->first();

        $employee = $lastLog?->employee;

        // الحالة
        $stateLabel = '—';
        $stateBadge = 'secondary';

        if ($lastLog) {
            if ((int)$lastLog->punch_state === 0) {
                $stateLabel = 'دخول';
                $stateBadge = 'success';
            } else {
                $stateLabel = 'خروج';
                $stateBadge = 'danger';
            }
        }

        // الصورة (اختياري)
        $photoUrl = null;
        if ($employee && !empty($employee->photo)) {
            $photoUrl = asset('storage/'.$employee->photo);
        }

        return view('biotime.tv.last_punch', compact(
            'lastLog',
            'employee',
            'stateLabel',
            'stateBadge',
            'photoUrl',
            'refresh'
        ));
    }*/



public function last(Request $request)
{
    // ⏱ refresh TV
    $refresh = max(2, min(20, (int) $request->get('refresh', 3)));

    // 🔥 آخر pointage
    $lastLog = IclockTransaction::with(['employee', 'terminal'])
        ->where('is_attendance', 1)
        ->orderByDesc('punch_time')
        ->first();

    // لا يوجد أي pointage
    if (! $lastLog || ! $lastLog->employee) {
        return view('biotime.tv.last_punch', [
            'mode'    => 'welcome',
            'refresh' => $refresh,
        ]);
    }

    // 🔐 دخول / خروج
    $stateLabel = ((int)$lastLog->punch_state === 0) ? 'دخول' : 'خروج';

    // =========================
    // 🌐 API DISTANTE
    // =========================
    $complexId = (int) env('BIOTIME_COMPLEX_ID');
    $timestamp = now()->timestamp;

    if (! $complexId) {
        abort(500, 'BIOTIME_COMPLEX_ID manquant');
    }

    // ⚠️ الربط فقط بالـ ID
    $personId = (int) $lastLog->emp_code;

    $signature = hash_hmac(
        'sha256',
        $complexId . '|' . $timestamp,
        env('REMOTE_API_SECRET')
    );

    $url = rtrim(env('REMOTE_API_URL'), '/') . "/api/complex/{$complexId}/persons";

    $response = Http::withHeaders([
        'Accept'      => 'application/json',
        'X-TIMESTAMP' => $timestamp,
        'X-SIGNATURE' => $signature,
    ])->get($url);

    if (! $response->successful()) {
        return view('biotime.tv.last_punch', [
            'mode'    => 'welcome',
            'refresh' => $refresh,
        ]);
    }

    $persons = collect($response->json('data') ?? []);

    // 🔎 البحث بالـ id فقط
    $person = $persons->firstWhere('id', $personId);

    if (! $person) {
        return view('biotime.tv.last_punch', [
            'mode'    => 'welcome',
            'refresh' => $refresh,
        ]);
    }

    return view('biotime.tv.last_punch', [
        'mode'      => 'person',
        'person'    => $person,
        'lastLog'   => $lastLog,
        'state'     => $stateLabel,
        'refresh'   => $refresh,
    ]);
}


public function lastJson(Request $request)
{
    // 🔥 dernier pointage
    $lastLog = IclockTransaction::with(['employee', 'terminal'])
        ->where('is_attendance', 1)
        ->orderByDesc('punch_time')
        ->first();

    if (! $lastLog || ! $lastLog->employee) {
        return response()->json([
            'mode' => 'welcome'
        ]);
    }

    $stateLabel = ((int)$lastLog->punch_state === 0) ? 'دخول' : 'خروج';

    // =========================
    // 🌐 API DISTANTE (IDENTIQUE)
    // =========================
    $complexId = (int) env('BIOTIME_COMPLEX_ID');
    $timestamp = now()->timestamp;

    if (! $complexId) {
        return response()->json(['mode' => 'welcome']);
    }

    $personId = (int) $lastLog->emp_code;

    $signature = hash_hmac(
        'sha256',
        $complexId . '|' . $timestamp,
        env('REMOTE_API_SECRET')
    );

    $url = rtrim(env('REMOTE_API_URL'), '/') . "/api/complex/{$complexId}/persons";

    $response = Http::withHeaders([
        'Accept'      => 'application/json',
        'X-TIMESTAMP' => $timestamp,
        'X-SIGNATURE' => $signature,
    ])->get($url);

    if (! $response->successful()) {
        return response()->json(['mode' => 'welcome']);
    }

    $persons = collect($response->json('data') ?? []);
    $person  = $persons->firstWhere('id', $personId);
 // $personRows = $persons->where('id', $personId)->values();

    if (! $person) {
        return response()->json(['mode' => 'welcome']);
    }
 //dd($person);
    return response()->json([
        'mode'  => 'person',
          'complex' => [
        'id'   => $person['complex_id'] ?? null,
        'name' => $person['complex_name'] ?? 'المركب الرياضي',
    ],
        'person' => [
            'firstname'  => $person['firstname'] ?? '',
            'lastname'   => $person['lastname'] ?? '',
            'photo'      => $person['person_photo'] ?? asset('images/avatar.png'),
            'start_date' => $person['start_date'] ?? null,
            'end_date'   => $person['end_date'] ?? null,
            'time_slots' => $person['time_slots'] ?? [],
            'access'     => $person['access_status'] ?? '',
        ],

        'punch' => [
            'time'     => $lastLog->punch_time,
            'state'    => $stateLabel,
            'terminal' => $lastLog->terminal->name ?? '—',
        ],
    ]);
}

/*

public function last(Request $request)
{
    // ⏱️ Auto refresh
    $refresh = (int) ($request->get('refresh', 3));
    if ($refresh < 2) $refresh = 2;
    if ($refresh > 20) $refresh = 20;

    // 🔥 آخر pointage
    $lastLog = IclockTransaction::with(['employee','terminal'])
        ->where('is_attendance', 1)
        ->orderByDesc('punch_time')
        ->first();

    if (! $lastLog || ! $lastLog->employee) {
        return view('biotime.tv.last_punch', [
            'employee' => null,
            'lastLog'  => null,
            'refresh'  => $refresh,
        ]);
    }

    $employee = $lastLog->employee;

    // 🔐 état entrée / sortie
    $stateLabel = ((int)$lastLog->punch_state === 0) ? 'دخول' : 'خروج';
    $stateBadge = ((int)$lastLog->punch_state === 0) ? 'success' : 'danger';

    // ===============================
    // 🌐 APPEL API DISTANTE
    // ===============================

    $timestamp = now()->timestamp;
   $complexId = (int) env('BIOTIME_COMPLEX_ID', 0);

if ($complexId <= 0) {
    abort(500, 'BIOTIME_COMPLEX_ID non défini');
}
// مهم
    $userId    = $lastLog->emp_code;         // أو user_id حسب ton mapping
//dd($userId );
    $signature = hash_hmac(
        'sha256',
        $complexId . '|' . $timestamp,
        env('REMOTE_API_SECRET')
    );

    $url = rtrim(env('REMOTE_API_URL'), '/')
         . "/api/complex/{$complexId}/persons";

    $response = Http::withHeaders([
        'Accept'      => 'application/json',
        'X-TIMESTAMP' => $timestamp,
        'X-SIGNATURE' => $signature,
    ])->get($url);
//dd($response);
    if (! $response->successful()) {
        abort(500, 'Erreur API distante');
    }

    $persons = collect($response->json('data') ?? []);
   
    // 🔎 récupérer UNIQUEMENT la personne qui a pointé
    $remotePerson = $persons->firstWhere('id', $userId);
 
    return view('biotime.tv.last_punch', compact(
        'lastLog',
        'employee',
        'remotePerson',
        'stateLabel',
        'stateBadge',
        'refresh'
    ));
}*/

}
