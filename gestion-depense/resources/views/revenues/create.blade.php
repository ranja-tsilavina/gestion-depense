@extends('layouts.app')

@section('title', 'Nouveau Revenu – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--accent)"></i>Nouveau Revenu</h1>
        <a href="/revenues" class="btn-back">
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
    <div class="form-card">
        <div class="form-card-header" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="form-card-icon">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <h2>Enregistrer un revenu</h2>
                <p>Remplissez les informations pour ajouter une nouvelle source de revenu.</p>
            </div>
        </div>

        <div class="form-body">
            <form action="/revenues" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label" for="source">
                                <i class="bi bi-building"></i>
                                Source <span class="required-star">*</span>
                            </label>
                            <input type="text" id="source" name="source" class="form-control @error('source') is-invalid @enderror" placeholder="Ex. : Salaire, Freelance…" value="{{ old('source') }}" required>
                            @error('source')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label" for="amount">
                                <i class="bi bi-cash-stack"></i>
                                Montant <span class="required-star">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="number" step="0.01" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="0" value="{{ old('amount') }}" min="1" required style="padding-right:3.5rem">
                                <span style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.85rem;font-weight:600;pointer-events:none">Ar</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="account_id">
                        <i class="bi bi-bank"></i>
                        Compte à créditer <span class="required-star">*</span>
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
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="revenue_date">
                        <i class="bi bi-calendar3"></i>
                        Date <span class="required-star">*</span>
                    </label>
                    <input type="date" id="revenue_date" name="revenue_date" class="form-control @error('revenue_date') is-invalid @enderror" value="{{ old('revenue_date', date('Y-m-d')) }}" required>
                    @error('revenue_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="description">
                        <i class="bi bi-pencil-square"></i>
                        Description <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0">(optionnelle)</span>
                    </label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Détails supplémentaires…" rows="3" maxlength="255" style="resize:vertical;min-height:90px">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="form-divider">

                <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <i class="bi bi-check2-circle"></i>
                    Enregistrer le revenu
                </button>
            </form>
        </div>
    </div>

@endsection
