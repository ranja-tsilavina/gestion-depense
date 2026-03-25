@extends('layouts.app')

@section('title', 'Inscription – Gestion de Dépenses')

@section('content')
<div style="min-height:90vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem">
    
    <div style="width:100%;max-width:480px;background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.06);padding:2.5rem;text-align:center">
        
        <!-- Logo/Icon -->
        <div style="width:60px;height:60px;background:#10b981;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 8px 20px rgba(16,185,129,0.3)">
            <i class="bi bi-person-plus-fill text-white" style="font-size:1.8rem"></i>
        </div>
        
        <h3 style="font-weight:800;color:#1e293b;margin-bottom:0.5rem">Créer un compte</h3>
        <p style="color:#64748b;font-size:0.95rem;margin-bottom:2rem">Rejoignez-nous pour gérer vos finances</p>

        <form method="POST" action="{{ route('register') }}" style="text-align:left">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label" style="font-weight:600;color:#475569;font-size:0.85rem">Nom complet</label>
                <div class="input-group-custom">
                    <span class="input-group-text-custom"><i class="bi bi-person"></i></span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-control @error('name') is-invalid @enderror" placeholder="Jean Dupont">
                </div>
                @error('name')
                    <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label" style="font-weight:600;color:#475569;font-size:0.85rem">Adresse e-mail</label>
                <div class="input-group-custom">
                    <span class="input-group-text-custom"><i class="bi bi-envelope"></i></span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-control @error('email') is-invalid @enderror" placeholder="jean@example.com">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label mb-0" style="font-weight:600;color:#475569;font-size:0.85rem">Mot de passe</label>
                <div class="input-group-custom">
                    <span class="input-group-text-custom"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 caractères">
                </div>
                @error('password')
                    <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label mb-0" style="font-weight:600;color:#475569;font-size:0.85rem">Confirmer le mot de passe</label>
                <div class="input-group-custom">
                    <span class="input-group-text-custom"><i class="bi bi-key"></i></span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Retapez le mot de passe">
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn w-100 mb-4" style="background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:600;padding:0.75rem;border-radius:12px;font-size:1rem;box-shadow:0 4px 12px rgba(16,185,129,0.25)">
                <i class="bi bi-person-check-fill me-2"></i>M'inscrire
            </button>

            <p style="text-align:center;font-size:0.9rem;color:#64748b">
                Vous avez déjà un compte ? 
                <a href="{{ route('login') }}" style="color:#10b981;font-weight:700;text-decoration:none">Se connecter</a>
            </p>
        </form>
    </div>
</div>
@endsection
