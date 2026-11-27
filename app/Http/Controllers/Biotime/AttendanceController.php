<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use App\Models\Biotime\IclockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = IclockTransaction::with(['employee','terminal'])
            ->where('is_attendance', 1)
            ->orderByDesc('punch_time');

        // 🔎 Filtre par employé
        if ($request->filled('emp_code')) {
            $query->where('emp_code', $request->emp_code);
        }

        // 📅 Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('punch_time', $request->date);
        }

        $logs = $query->paginate(20);

        // 🌐 Infos des personnes via l'API distante (indexées par id = emp_code)
        $persons = $this->remotePersons();

        return view('biotime.attendance.index', compact('logs', 'persons'));
    }

    /**
     * Liste des personnes du complexe depuis l'API distante, indexée par id.
     * Mise en cache 30 s pour ne pas appeler l'API à chaque affichage.
     */
    private function remotePersons()
    {
        $complexId = (int) env('BIOTIME_COMPLEX_ID');

        if (! $complexId) {
            return collect();
        }

        return Cache::remember("remote_persons_{$complexId}", 30, function () use ($complexId) {
            try {
                $timestamp = now()->timestamp;
                $signature = hash_hmac(
                    'sha256',
                    $complexId . '|' . $timestamp,
                    env('REMOTE_API_SECRET')
                );

                $response = Http::timeout(10)->withHeaders([
                    'Accept'      => 'application/json',
                    'X-TIMESTAMP' => $timestamp,
                    'X-SIGNATURE' => $signature,
                ])->get(rtrim(env('REMOTE_API_URL'), '/') . "/api/complex/{$complexId}/persons");

                if (! $response->successful()) {
                    return collect();
                }

                return collect($response->json('data') ?? [])->keyBy('id');
            } catch (\Throwable $e) {
                return collect();
            }
        });
    }
}
