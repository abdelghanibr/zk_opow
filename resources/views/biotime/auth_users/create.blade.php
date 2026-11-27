@extends('layouts.app')

@section('content')
<style>
    .zk-card {
        max-width: 520px;
        margin: 70px auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 20px 45px rgba(0,0,0,.08);
        padding: 35px 30px;
    }
    .zk-logo {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg,#16a34a,#22c55e);
        color:#fff; font-size:34px; font-weight:900;
        display:flex; align-items:center; justify-content:center;
        margin:-75px auto 20px;
        box-shadow:0 10px 25px rgba(34,197,94,.35);
    }
    .zk-title { text-align:center; font-weight:800; color:#14532d; }
    .zk-btn { background:linear-gradient(135deg,#16a34a,#22c55e); border:none; border-radius:12px; font-weight:700; }
    .zk-btn:hover { background:linear-gradient(135deg,#15803d,#16a34a); }
</style>

<div class="zk-card">
    <div class="zk-logo">ZK</div>
    <h4 class="zk-title mb-3">➕ Créer un compte</h4>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('biotime.auth-users.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nom d’utilisateur</label>
                <input name="username" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input name="first_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input name="last_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmer mot de passe</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Employé PIN</label>
                <input name="emp_pin" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Téléphone</label>
                <input name="tele_phone" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Staff</label>
                <select name="is_staff" class="form-select">
                    <option value="1">Oui</option>
                    <option value="0">Non</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Statut</label>
                <select name="is_active" class="form-select">
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button class="btn zk-btn text-white">💾 Créer</button>
            <a href="{{ route('login.form') }}" class="btn btn-secondary">↩️ Retour</a>
        </div>
    </form>
</div>
@endsection
