@extends('layouts.app')

@section('title', 'Connexion – Gestion de Dépenses')

@section('content')
<div style="min-height:90vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem">
    
    <div style="width:100%;max-width:440px;background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.06);padding:2.5rem;text-align:center">
        
        <!-- Logo/Icon -->
        <div style="width:60px;height:60px;background:var(--primary);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 8px 20px rgba(99,102,241,0.3)">
            <i class="bi bi-wallet2 text-white" style="font-size:1.8rem"></i>
        </div>
        
        <h3 style="font-weight:800;color:#1e293b;margin-bottom:0.5rem">Bienvenue</h3>
        <p style="color:#64748b;font-size:0.95rem;margin-bottom:2rem">Connectez-vous à votre compte</p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success alert-custom mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="text-align:left">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label" style="font-weight:600;color:#475569;font-size:0.85rem">Adresse e-mail</label>
                <div class="input-group-custom">
                    <span class="input-group-text-custom"><i class="bi bi-envelope"></i></span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control @error('email') is-invalid @enderror" placeholder="Ex: jean@example.com">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0" style="font-weight:600;color:#475569;font-size:0.85rem">Mot de passe</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color:var(--primary);font-size:0.8rem;text-decoration:none;font-weight:600">Oublié ?</a>
                    @endif
                </div>
                
                <div class="input-group-custom">
                    <span class="input-group-text-custom"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control @error('password') is-invalid @enderror" placeholder="Votre mot de passe">
                </div>
                @error('password')
                    <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-4 form-check d-flex align-items-center">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember" style="width:1.2rem;height:1.2rem;margin-top:0.1rem;cursor:pointer">
                <label for="remember_me" class="form-check-label ms-2" style="font-size:0.9rem;color:#64748b;cursor:pointer;padding-top:2px">
                    Se souvenir de moi
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn w-100 mb-4" style="background:linear-gradient(135deg,var(--primary),#4f46e5);color:white;font-weight:600;padding:0.75rem;border-radius:12px;font-size:1rem;box-shadow:0 4px 12px rgba(99,102,241,0.25)">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </button>

            <p style="text-align:center;font-size:0.9rem;color:#64748b">
                Vous n'avez pas de compte ? 
                <a href="{{ route('register') }}" style="color:var(--primary);font-weight:700;text-decoration:none">S'inscrire</a>
            </p>
        </form>
    </div>
</div>
@endsection
