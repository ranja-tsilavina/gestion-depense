@extends('layouts.app')

@section('title', 'Nouveau Revenu – VolaKo')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--accent)"></i>Nouveau Revenu</h1>
        <a href="/revenues" class="btn btn-outline-secondary" style="border-radius:10px">
            <i class="bi bi-arrow-left"></i> Retour aux revenus
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-custom alert-dismissible fade show mb-3" role="alert" style="max-width:700px;margin:0 auto">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs ci-dessous.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card card-custom shadow-sm border-0 bg-white mx-auto" style="max-width:700px; border-radius:16px; overflow:hidden">
        <div class="p-4 text-white" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="h5 mb-1 fw-bold">Enregistrer un revenu</h2>
                    <p class="mb-0 small opacity-75">Remplissez les informations pour ajouter une nouvelle source de revenu.</p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="/revenues" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label fw-600 small text-uppercase text-muted" for="source">
                                <i class="bi bi-building me-1"></i> Source <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="source" name="source" class="form-control @error('source') is-invalid @enderror" placeholder="Ex. : Salaire, Freelance…" value="{{ old('source') }}" required>
                            @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label fw-600 small text-uppercase text-muted" for="amount">
                                <i class="bi bi-cash-stack me-1"></i> Montant <span class="text-danger">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="0" value="{{ old('amount') }}" required style="padding-right:3.5rem">
                                <span style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.85rem;font-weight:600;pointer-events:none">Ar</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-uppercase text-muted" for="account_id">
                        <i class="bi bi-bank me-1"></i> Compte à créditer <span class="text-danger">*</span>
                    </label>
                    <select id="account_id" name="account_id" class="form-select @error('account_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('account_id') ? '' : 'selected' }}>-- Choisir un compte --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} (Solde: {{ number_format($account->balance, 0, ',', ' ') }} Ar)
                            </option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-uppercase text-muted" for="revenue_date">
                        <i class="bi bi-calendar3 me-1"></i> Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" id="revenue_date" name="revenue_date" class="form-control @error('revenue_date') is-invalid @enderror" value="{{ old('revenue_date', date('Y-m-d')) }}" required>
                    @error('revenue_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small text-uppercase text-muted" for="description">
                        <i class="bi bi-pencil-square me-1"></i> Description <span class="fw-normal text-lowercase">(optionnelle)</span>
                    </label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Détails supplémentaires…" rows="3" maxlength="255" style="resize:vertical;min-height:90px">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-lg w-100 text-white fw-bold shadow-sm" style="background:linear-gradient(135deg,#10b981,#059669); border-radius:12px">
                    <i class="bi bi-check2-circle me-2"></i> Enregistrer le revenu
                </button>
            </form>
        </div>
    </div>

@endsection
