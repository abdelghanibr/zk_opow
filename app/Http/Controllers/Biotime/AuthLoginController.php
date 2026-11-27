<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Biotime\AuthUser;

class AuthLoginController extends Controller
{
    /* =========================
       Form login
       ========================= */
    public function showLoginForm()
    {
        return view('biotime.auth.login');
    }

    /* =========================
       LOGIN
       ========================= */
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => 'required|string', // username ou email
            'password' => 'required|string',
        ]);

        // 🔍 chercher utilisateur
        $user = AuthUser::where('username', $data['login'])
            ->orWhere('email', $data['login'])
            ->first();

        if (! $user) {
            return back()->withErrors([
                'login' => '❌ Utilisateur introuvable'
            ]);
        }

        // 🔐 vérifier mot de passe
        if (! Hash::check($data['password'], $user->password)) {
            return back()->withErrors([
                'password' => '❌ Mot de passe incorrect'
            ]);
        }

        // 🚫 vérifier état
        if (! $user->is_active || $user->del_flag == 1) {
            return back()->withErrors([
                'login' => '🚫 Compte désactivé'
            ]);
        }

        // ✅ Auth OK → créer session
        Session::put('auth_user', [
            'id'        => $user->id,
            'username'  => $user->username,
            'email'     => $user->email,
            'is_staff'  => $user->is_staff,
            'is_superuser' => $user->is_superuser,
        ]);

        // mise à jour login
        $user->update([
            'last_login' => now(),
            'login_count'=> ($user->login_count ?? 0) + 1,
        ]);

        return redirect()->route('dashboard');
    }

    /* =========================
       LOGOUT
       ========================= */
    public function logout(Request $request)
    {
        Session::forget('auth_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
