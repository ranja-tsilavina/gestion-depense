@extends('layouts.app')

@section('title', 'Profil – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-person-circle me-2" style="color:var(--primary)"></i>Mon Profil</h1>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-custom alert-dismissible fade show mb-3" role="alert" style="max-width:700px;margin:0 auto 1.5rem">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Profile Info Form -->
    <div class="form-card mb-4">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="bi bi-person-lines-fill"></i>
            </div>
            <div>
                <h2>Informations du compte</h2>
                <p>Mettez à jour votre nom et adresse e-mail.</p>
            </div>
        </div>
        <div class="form-body">
            <form action="{{ url('/profile') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label class="field-label" for="name">
                        <i class="bi bi-person"></i>
                        Nom complet <span class="required-star">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="email">
                        <i class="bi bi-envelope"></i>
                        Adresse e-mail <span class="required-star">*</span>
                    </label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="form-divider">

                <button type="submit" class="btn-submit">
                    <i class="bi bi-check2-circle"></i>
                    Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>

    <!-- Password Form -->
    <div class="form-card">
        <div class="form-card-header" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="form-card-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div>
                <h2>Modifier le mot de passe</h2>
                <p>Utilisez un mot de passe long et unique pour sécuriser votre compte.</p>
            </div>
        </div>
        <div class="form-body">
            <form action="{{ url('/profile/password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label class="field-label" for="current_password">
                        <i class="bi bi-key"></i>
                        Mot de passe actuel <span class="required-star">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label" for="password">
                                <i class="bi bi-lock"></i>
                                Nouveau mot de passe <span class="required-star">*</span>
                            </label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label" for="password_confirmation">
                                <i class="bi bi-lock-fill"></i>
                                Confirmer le mot de passe
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <hr class="form-divider">

                <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <i class="bi bi-shield-check"></i>
                    Mettre à jour le mot de passe
                </button>
            </form>
        </div>
    </div>

@endsection
