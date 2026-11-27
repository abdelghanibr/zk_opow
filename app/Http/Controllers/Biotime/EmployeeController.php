<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biotime\PersonnelEmployee;
use Illuminate\Support\Facades\DB;
use App\Services\ZKBioApiService;
use App\Models\Biotime\PersonnelDepartment;
use App\Models\Biotime\PersonnelArea;


class EmployeeController extends Controller
{



public function create()
{
    $departments = PersonnelDepartment::orderBy('id')->get();
    $areas       = PersonnelArea::orderBy('id')->get();

    return view('biotime.employees.create', compact(
        'departments',
        'areas'
    ));
}

    /* =========================
       INDEX : liste des employés
       ========================= */
   /**
 * Création employé via API ZKBio
 */
public function storeViaApi(Request $request, ZKBioApiService $zkbio)
{
    // 1️⃣ Validation minimale (ZKBio est strict)
    $validated = $request->validate([
        'emp_code'   => 'required|string|max:20',
        'first_name' => 'nullable|string|max:25',
        'last_name'  => 'nullable|string|max:25',
        'gender'     => 'nullable|in:M,F,S',
        'mobile'     => 'nullable|string|max:20',
        'email'      => 'nullable|email|max:50',
        'address'    => 'nullable|string|max:200',
    ]);

    // 2️⃣ Payload FINAL conforme ZKBio
    // ⚠️ department, area, company, app_status sont CRITIQUES
    $payload = [
        'emp_code'   => $validated['emp_code'],
        'first_name' => $validated['first_name'] ?? 'EMP',
        'last_name'  => $validated['last_name'] ?? 'NEW',

        'department' => 1,        // doit exister dans ZKBio
        'area'       => [1],      // une seule area, liée au department
        'company'    => 1,        // obligatoire dans beaucoup de configs

        'app_status' => 1,        // 1 = Enable

        // optionnels
        'gender'  => $validated['gender']  ?? null,
        'mobile'  => $validated['mobile']  ?? null,
        'email'   => $validated['email']   ?? null,
        'address' => $validated['address'] ?? null,
    ];

    // 3️⃣ Appel API ZKBio
    $response = $zkbio->post('/personnel/api/employees/', $payload);

    // 4️⃣ ZKBio ne respecte PAS HTTP status → on lit le body
    $body = $response->json();

    $isSuccess =
        isset($body['id']) ||
        isset($body['emp_id']) ||
        (isset($body['success']) && $body['success'] === true) ||
        (isset($body['message']) &&
            str_contains(strtolower($body['message']), 'success'));

    // 5️⃣ Échec réel
    if (! $isSuccess) {

        // log technique (important)
        \Log::error('ZKBio API INSERT FAILED', [
            'status'  => $response->status(),
            'payload' => $payload,
            'body'    => $body,
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'api' => '❌ ZKBio a refusé l’ajout : ' . json_encode($body)
            ]);
    }

    // 6️⃣ Vérification finale (emp_code existe bien)
    $check = $zkbio->get('/personnel/api/employees/', [
        'emp_code' => $payload['emp_code']
    ])->json();

    $exists =
        ! empty($check['results']) ||
        ! empty($check['data']);

    if (! $exists) {
        return back()
            ->withInput()
            ->withErrors([
                'api' => '⚠️ API OK mais employé non trouvé après insertion'
            ]);
    }

    // 7️⃣ Succès confirmé
    return redirect()
        ->route('biotime.employees.index')
        ->with('success', '✅ Employé ajouté avec succès via ZKBio API');
}



   

public function index(Request $request)
{
    $employees = DB::table('personnel_employee')
        ->select(
            'id',
            'emp_code',
            'first_name',
            'last_name',
            'mobile',
            'is_active',
            'status',
            'create_time',
            'department_id',
            'company_id',
            'email',
        )
        ->orderByDesc('id')
        ->paginate(20);

    return view('biotime.employees.index', compact('employees'));
}

 
/*public function index(Request $request, ZKBioApiService $zkbio)
{
    $params = [
        'page'      => $request->get('page', 1),
        'page_size' => 20,
    ];

    $response = $zkbio->get('/personnel/api/employees/', $params);

    if (! $response->successful()) {
        abort(500, 'Erreur API ZKBio');
    }

    $data = $response->json();

    $employees = $data['results']
              ?? $data['data']
              ?? [];

    $total = $data['count'] ?? count($employees);

    return view('biotime.employees.index', compact(
        'employees',
        'total'
    ));
}*/


    /* =========================
       CREATE : formulaire
       ========================= */
   

    /* =========================
       STORE : insertion
       ========================= */


public function store(Request $request, ZKBioApiService $zkbio)
{
    $data = $request->validate([
        'emp_code'   => 'required|string|max:20',
        'first_name' => 'nullable|string|max:100',
        'last_name'  => 'nullable|string|max:100',
        'department' => 'required|integer',
        'area'       => 'required|array|min:1',
        'area.*'     => 'integer',
        'company'    => 'required|integer',
        'app_status' => 'nullable|in:0,1',
    ]);

    try {
        // Création via l'API ZKBio (contrôle de licence appliqué par ZKBio)
        $zkbio->createEmployee([
            'emp_code'   => $data['emp_code'],
            'first_name' => $data['first_name'] ?? 'EMP',
            'last_name'  => $data['last_name'] ?? 'NEW',
            'department' => (int) $data['department'],
            'area'       => array_map('intval', $data['area']),
            'company'    => (int) $data['company'],
            'app_status' => (int) ($data['app_status'] ?? 1),
        ]);

        return redirect()
            ->route('biotime.employees.index')
            ->with('success', '✅ Employé créé avec succès (API ZKBio)');

    } catch (\Throwable $e) {
        return back()->withInput()->withErrors(['api' => $e->getMessage()]);
    }
}


    /* =========================
       EDIT : formulaire édition
       ========================= */
    public function edit($id)
{
    $employee = PersonnelEmployee::findOrFail($id);

    $departments = PersonnelDepartment::orderBy('id')->get();
    $areas       = PersonnelArea::orderBy('id')->get();

    $employeeAreaIds = DB::table('personnel_employee_area')
        ->where('employee_id', $employee->id)
        ->pluck('area_id')
        ->toArray();

    return view('biotime.employees.edit', compact(
        'employee',
        'departments',
        'areas',
        'employeeAreaIds'
    ));
}


    /* =========================
       UPDATE : mise à jour
       ========================= */
public function update(Request $request, $id, ZKBioApiService $zkbio)
{
    $employee = PersonnelEmployee::findOrFail($id);

    $data = $request->validate([
        'first_name' => 'nullable|string|max:100',
        'last_name'  => 'nullable|string|max:100',
        'department' => 'required|integer',
        'area'       => 'required|array|min:1',
        'area.*'     => 'integer',
        'company'    => 'required|integer',
        'app_status' => 'nullable|in:0,1',
        'is_active'  => 'required|boolean',
    ]);

    try {
        $zkbio->updateEmployee((int) $employee->id, [
            'first_name' => $data['first_name'] ?? '',
            'last_name'  => $data['last_name'] ?? '',
            'department' => (int) $data['department'],
            'area'       => array_map('intval', $data['area']),
            'company'    => (int) $data['company'],
            'app_status' => (int) ($data['app_status'] ?? 1),
            'is_active'  => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('biotime.employees.index')
            ->with('success', '✅ Employé mis à jour avec succès');

    } catch (\Throwable $e) {
        return back()->withInput()->withErrors(['api' => $e->getMessage()]);
    }
}

public function disable($empCode, ZKBioApiService $zkbio)
{
    try {
        $emp = $zkbio->findEmployeeByCode((string) $empCode);

        if (! $emp) {
            return back()->withErrors(['api' => '⚠️ Employé introuvable']);
        }

        $zkbio->updateEmployee((int) $emp['id'], ['is_active' => false]);

        return redirect()
            ->route('biotime.employees.index')
            ->with('success', '⛔ Employé désactivé avec succès');

    } catch (\Throwable $e) {
        return back()->withErrors(['api' => $e->getMessage()]);
    }
}

public function destroy($empCode, ZKBioApiService $zkbio)
{
    try {
        $emp = $zkbio->findEmployeeByCode((string) $empCode);

        if (! $emp) {
            return back()->withErrors(['api' => '⚠️ Employé introuvable']);
        }

        $zkbio->deleteEmployee((int) $emp['id']);

        return redirect()
            ->route('biotime.employees.index')
            ->with('success', '🗑️ Employé supprimé');

    } catch (\Throwable $e) {
        return back()->withErrors(['api' => $e->getMessage()]);
    }
}
}