<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Biotime\AuthUser;

class AuthUserController extends Controller
{
    /* =========================
       INDEX : liste des logins
       ========================= */
    public function index()
    {
        $users = AuthUser::orderByDesc('id')->get();

        return view('biotime.auth_users.index', compact('users'));
    }

    /* =========================
       CREATE : formulaire
       ========================= */
    public function create()
    {
        return view('biotime.auth_users.create');
    }

    /* =========================
       STORE : création LOGIN
       ========================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username'   => 'required|string|max:30|unique:auth_user,username',
            'password'   => 'required|string|min:6|confirmed',
            'first_name' => 'required|string|max:30',
            'last_name'  => 'required|string|max:30',
            'email'      => 'required|email|unique:auth_user,email',
            'emp_pin'    => 'nullable|string|max:30',
            'tele_phone' => 'nullable|string|max:30',
            'is_staff'   => 'required|boolean',
            'is_active'  => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

      AuthUser::create([
    'username'  => $data['username'],
    'password'  => Hash::make($data['password']),
    'first_name'=> $data['first_name'],
    'last_name' => $data['last_name'],
    'email'     => $data['email'],
    'emp_pin'   => $data['emp_pin'] ?? null,

    // ✅ PostgreSQL BOOLEAN
    'is_staff'          => DB::raw($data['is_staff'] ? 'TRUE' : 'FALSE'),
    'is_active'         => DB::raw($data['is_active'] ? 'TRUE' : 'FALSE'),
    'is_superuser'      => DB::raw('FALSE'),
    'is_public'         => DB::raw('FALSE'),
    'can_manage_all_dept'=> DB::raw('FALSE'),

    'date_joined' => now(),
]);


            DB::commit();

           return redirect()
    ->route('login.form')
    ->with('success', '✅ Compte créé avec succès. Vous pouvez vous connecter.');


        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'db' => 'Erreur PostgreSQL : ' . $e->getMessage()
            ]);
        }
    }

    /* =========================
       EDIT
       ========================= */
    public function edit($id)
    {
        $user = AuthUser::findOrFail($id);

        return view('biotime.auth_users.edit', compact('user'));
    }

    /* =========================
       UPDATE
       ========================= */
    public function update(Request $request, $id)
    {
        $user = AuthUser::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:30',
            'last_name'  => 'required|string|max:30',
            'email'      => 'required|email|unique:auth_user,email,' . $user->id,
            'emp_pin'    => 'nullable|string|max:30',
            'tele_phone' => 'nullable|string|max:30',
            'is_staff'   => 'required|boolean',
            'is_active'  => 'required|boolean',
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {

            $update = [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'emp_pin'    => $data['emp_pin'] ?? null,
                'tele_phone' => $data['tele_phone'] ?? null,
                'is_staff'   => $data['is_staff'],
                'is_active'  => $data['is_active'],
                'update_time'=> now(),
            ];

            if (!empty($data['password'])) {
                $update['password'] = Hash::make($data['password']);
            }

            $user->update($update);

            DB::commit();

            return redirect()
                ->route('biotime.auth-users.index')
                ->with('success', '✅ Compte utilisateur mis à jour');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'db' => 'Erreur PostgreSQL : ' . $e->getMessage()
            ]);
        }
    }

    /* =========================
       DESTROY : désactivation
       ========================= */
    public function destroy($id)
    {
        $user = AuthUser::findOrFail($id);

        DB::beginTransaction();

        try {

            $user->update([
                'is_active'  => false,
                'del_flag'   => 1,
                'update_time'=> now(),
            ]);

            DB::commit();

            return redirect()
                ->route('biotime.auth-users.index')
                ->with('success', '🚫 Compte utilisateur désactivé');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'db' => 'Erreur PostgreSQL : ' . $e->getMessage()
            ]);
        }
    }
}
