@extends('layouts.app')

@section('content')
<style>
    /* ===== ZK 2026 LOGIN STYLE ===== */
    .zk-login-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .zk-login-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
        padding: 35px 30px;
        position: relative;
    }

    .zk-logo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #16a34a, #22c55e);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 34px;
        font-weight: 900;
        margin: -75px auto 20px;
        box-shadow: 0 10px 25px rgba(34, 197, 94, 0.35);
    }

    .zk-title {
        font-weight: 800;
        color: #14532d;
        text-align: center;
        margin-bottom: 6px;
    }

    .zk-subtitle {
        text-align: center;
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 25px;
    }

    .zk-login-card label {
        font-weight: 600;
        color: #374151;
    }

    .zk-login-card .form-control {
        border-radius: 12px;
        padding: 10px 14px;
    }

    .zk-btn-primary {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        border: none;
        border-radius: 12px;
        font-weight: 700;
        padding: 10px;
    }

    .zk-btn-primary:hover {
        background: linear-gradient(135deg, #15803d, #16a34a);
    }

    .zk-divider {
        text-align: center;
        margin: 18px 0;
        color: #9ca3af;
        font-size: 0.85rem;
        position: relative;
    }

    .zk-divider::before,
    .zk-divider::after {
        content: "";
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #e5e7eb;
    }

    .zk-divider::before { left: 0; }
    .zk-divider::after { right: 0; }

    .zk-create-link {
        display: block;
        text-align: center;
        text-decoration: none;
        border-radius: 12px;
        padding: 10px;
        font-weight: 700;
        color: #166534;
        border: 2px dashed #22c55e;
        transition: all 0.2s ease;
    }

    .zk-create-link:hover {
        background: #ecfdf5;
        text-decoration: none;
        color: #14532d;
    }
</style>

<div class="zk-login-wrapper">
    <div class="zk-login-card">

        {{-- Logo --}}
        <div class="zk-logo">
            ZK
        </div>

        <h4 class="zk-title">Connexion</h4>
        <p class="zk-subtitle">ZK Attendance Management System</p>
@if (session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Login form --}}
        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nom d’utilisateur ou Email</label>
                <input type="text"
                       name="login"
                       class="form-control"
                       placeholder="username / email"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="••••••••"
                       required>
            </div>

            <button class="btn zk-btn-primary w-100">
                🔓 Se connecter
            </button>
        </form>

        {{-- Divider --}}
        <div class="zk-divider">ou</div>

        {{-- Create account --}}
        <a href="{{ route('biotime.auth-users.create') }}"
           class="zk-create-link">
            ➕ Créer un compte
        </a>

    </div>
</div>
@endsection
