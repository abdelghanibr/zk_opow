<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ZKBioApiService;
use Illuminate\Validation\ValidationException;

class ZkBioEmployeeController extends Controller
{
    /**
     * Create employee in ZKBio via API
     */
    public function store(Request $request, ZKBioApiService $zkbio)
    {
        /* =========================
           Validation (selon doc API)
           ========================= */
        $data = $request->validate([
            'emp_code'   => 'required|string|max:20',
            'department' => 'required|integer',
            'area'       => 'required|array|min:1',

            // optionnels
            'hire_date'  => 'nullable|date',
            'first_name' => 'nullable|string|max:25',
            'last_name'  => 'nullable|string|max:25',
            'gender'     => 'nullable|in:M,F,S',
            'mobile'     => 'nullable|string|max:20',
            'national'   => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:200',
            'email'      => 'nullable|email|max:50',
            'app_status' => 'nullable|in:0,1',
        ]);

        /* =========================
           Appel API ZKBio
           ========================= */
        $response = $zkbio->post(
            '/personnel/api/employees/',
            $data
        );

        /* =========================
           Gestion réponse
           ========================= */
        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'ZKBio API error',
                'error'   => $response->json(),
            ], $response->status());
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Employee created in ZKBio',
            'data'    => $response->json(),
        ]);
    }
}
