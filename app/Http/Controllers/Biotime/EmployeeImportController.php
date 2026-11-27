<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use App\Services\RemoteApiService;
use App\Services\ZKBioApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeImportController extends Controller
{
    public function importFromRemote(int $complexId, RemoteApiService $api, ZKBioApiService $zkbio)
    {
        $response = $api->getPersonsByComplex($complexId);

        if (! $response->successful()) {
            return back()->withErrors([
                'import' => 'Erreur API distante'
            ]);
        }

        $persons = $response->json('data', []);

        $count  = 0;
        $errors = [];

        foreach ($persons as $p) {
            try {
                $zkbio->upsertEmployee((string) $p['user_id'], $this->payload(
                    $p['first_name'] ?? null,
                    $p['last_name'] ?? null,
                    $p['mobile'] ?? null,
                    $p['email'] ?? null,
                    $complexId,
                    true
                ));
                $count++;
            } catch (\Throwable $e) {
                $errors[] = $p['user_id'] . ' : ' . $e->getMessage();
            }
        }

        if ($errors) {
            Log::error('Import distant: erreurs ZKBio', ['errors' => $errors]);
            return back()->withErrors(['import' => implode("\n", $errors)]);
        }

        return back()->with('success', "✅ Import terminé ($count employés)");
    }

    /** Import d'un seul employé (appelé en boucle par la barre de progression). */
    public function importOne(Request $request, $complexId, ZKBioApiService $zkbio)
    {
        $emp = json_decode((string) $request->input('employee'), true);

        if (! is_array($emp) || empty($emp['id'])) {
            return response()->json(['ok' => false, 'error' => 'Données invalides'], 422);
        }

        $name = trim(($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? ''));

        try {
            [$kind, ] = $zkbio->upsertEmployee((string) $emp['id'], $this->payload(
                $emp['firstname'] ?? null,
                $emp['lastname'] ?? null,
                $emp['person_phone'] ?? null,
                $emp['email'] ?? null,
                $complexId,
                ($emp['access_status'] ?? 'non_autorisé') === 'autorisé'
            ));

            return response()->json(['ok' => true, 'kind' => $kind, 'id' => $emp['id'], 'name' => $name]);

        } catch (\Throwable $e) {
            Log::error('Import employé échoué', ['id' => $emp['id'], 'message' => $e->getMessage()]);

            return response()->json([
                'ok' => false, 'id' => $emp['id'], 'name' => $name, 'error' => $e->getMessage(),
            ]);
        }
    }

    /** Payload ZKBio : l'API applique elle-même les contrôles de licence. */
    private function payload(?string $first, ?string $last, ?string $mobile, ?string $email, $areaId, bool $active): array
    {
        $ids = app(ZKBioApiService::class)->defaults();

        return array_filter([
            'first_name' => $first,
            'last_name'  => $last,
            'mobile'     => $mobile,
            'email'      => $email,
            'department' => $ids['department'],
            'area'       => [$ids['area']],
            'company'    => $ids['company'],
            'app_status' => 0, // 0 = application mobile désactivée : ne consomme pas le quota APP (50) de la licence
            'is_active'  => $active,
        ], fn ($v) => $v !== null);
    }
public function testRemoteApi($complexId)
{
    $response = Http::withHeaders([
        'X-API-SECRET' => env('REMOTE_API_SECRET'),
        'Accept' => 'application/json',
    ])->get(env('REMOTE_API_URL') . "/api/complex/{$complexId}/employees");

    dd([
        'status' => $response->status(),
        'body'   => $response->body(),
    ]);
}

/*
public function preview($complexId)
{
    $timestamp = now()->timestamp;

    $signature = hash_hmac(
        'sha256',
        $complexId . '|' . $timestamp,
        env('REMOTE_API_SECRET')
    );

    $url = rtrim(env('REMOTE_API_URL'), '/')
         . "/api/complex/{$complexId}/persons";

    try {
        $response = Http::timeout(5) // ⏱️ 5 secondes max
            ->withHeaders([
                'Accept'      => 'application/json',
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
            ])
            ->get($url);

        if (! $response->successful()) {

            Log::warning('API distante - réponse invalide', [
                'complex_id' => $complexId,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);

            return back()->withErrors([
                'api' => '⚠️ فشل الاتصال بالخادم البعيد (Réponse invalide). حاول لاحقًا.'
            ]);
        }

        $employees = $response->json('data') ?? [];

        return view(
            'biotime.employees.preview',
            compact('employees', 'complexId')
        );

    } catch (ConnectionException $e) {

        // ❌ فقدان الاتصال نهائيًا
        Log::error('Connexion API distante perdue', [
            'complex_id' => $complexId,
            'url'        => $url,
            'message'    => $e->getMessage(),
        ]);

        return back()->withErrors([
            'api' => '❌ تعذر الاتصال بالخادم البعيد. تأكد من الشبكة أو أن الخادم يعمل.'
        ]);

    } catch (\Exception $e) {

        // ❌ أي خطأ غير متوقع
        Log::critical('Erreur API inattendue', [
            'complex_id' => $complexId,
            'message'    => $e->getMessage(),
        ]);

        return back()->withErrors([
            'api' => '❌فشل الاتصال بالخادم حدث خطأ غير متوقع. يرجى التواصل مع الدعم الفني.'
        ]);
    }
}

*/

public function preview($complexId)
{
    $timestamp = now()->timestamp;

    $signature = hash_hmac(
        'sha256',
        $complexId . '|' . $timestamp,
        env('REMOTE_API_SECRET')
    );

    $url = rtrim(env('REMOTE_API_URL'), '/')
         . "/api/complex/{$complexId}/persons";

    try {
        $response = Http::timeout(5)
            ->withHeaders([
                'Accept'      => 'application/json',
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
            ])
            ->get($url);

        if (! $response->successful()) {
            return back()->withErrors([
                'api' => '⚠️ فشل الاتصال بالخادم البعيد'
            ]);
        }

        $data = $response->json('data') ?? [];

        /* =====================================
         * 🔽 FILTRAGE : 1 ligne / employé
         * priorité à "autorisé"
         * ===================================== */
        $employees = collect($data)
            ->groupBy('id') // أو emp_code حسب الـ API
            ->map(function ($group) {

                // chercher ligne active
                $active = $group->firstWhere('access_status', 'autorisé');

                // إذا موجود active نأخذه، وإلا نأخذ أول واحد
                return $active ?? $group->first();
            })
            ->values()
            ->toArray();

        return view(
            'biotime.employees.preview',
            compact('employees', 'complexId')
        );

    } catch (\Throwable $e) {

        Log::error('Erreur API preview', [
            'complex_id' => $complexId,
            'message'    => $e->getMessage(),
        ]);

        return back()->withErrors([
            'api' => '❌ حدث خطأ أثناء جلب البيانات'
        ]);
    }
}


public function execute(Request $request, $complexId, ZKBioApiService $zkbio)
{
    $request->validate([
        'employees' => 'required|array|min:1',
    ]);

    /* ============================
     * 1️⃣ Decode + filtrage
     * ============================ */
    $employees = [];

    foreach ($request->employees as $json) {

        $emp = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            continue;
        }

        if (
            empty($emp['firstname']) &&
            empty($emp['lastname']) &&
            empty($emp['person_phone']) &&
            empty($emp['email'])
        ) {
            continue;
        }

        $employees[] = $emp;
    }

    if (empty($employees)) {
        return back()->withErrors([
            'import' => '❌ لا توجد بيانات صالحة للاستيراد'
        ]);
    }

    /* ============================
     * 2️⃣ Préparer le rapport
     * ============================ */
    $report = [
        'inserted' => [],
        'updated'  => [],
    ];

    $errors = [];

    foreach ($employees as $emp) {
        try {
            [$kind, ] = $zkbio->upsertEmployee((string) $emp['id'], $this->payload(
                $emp['firstname'] ?? null,
                $emp['lastname'] ?? null,
                $emp['person_phone'] ?? null,
                $emp['email'] ?? null,
                $complexId,
                ($emp['access_status'] ?? 'non_autorisé') === 'autorisé'
            ));
            $report[$kind][] = $emp;
        } catch (\Throwable $e) {
            $errors[] = $emp['id'] . ' : ' . $e->getMessage();
        }
    }

    if ($errors) {
        Log::error('Erreur import employés', [
            'complex_id' => $complexId,
            'errors'     => $errors,
        ]);
    }

    // Lecture seule de la liste (aucune écriture SQL)
    $employeesList = DB::table('personnel_employee')
        ->orderByDesc('id')
        ->get();

    return view('biotime.employees.index', [
        'complexId' => $complexId,
        'employees' => $employeesList,
        'report'    => $report,
    ])->with(
        $errors ? 'error' : 'success',
        $errors
            ? '⚠️ ' . count($errors) . ' erreur(s) : ' . implode(' | ', $errors)
            : '✅ Import ZKBio terminé : ' . count($employees) . ' employés'
    );
}
}