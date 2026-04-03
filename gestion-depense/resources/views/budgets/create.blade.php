@extends('layouts.app')

@section('title', 'Nouveau Budget – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-wallet me-2" style="color:var(--primary)"></i>Nouveau Budget</h1>
        <a href="/budgets" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour aux budgets
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-custom alert-dismissible fade show mb-3" role="alert" style="max-width:700px;margin-left:auto;margin-right:auto">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs ci-dessous.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <h2>Définir un budget</h2>
                <p>Fixez un plafond de dépenses pour une catégorie et un mois.</p>
            </div>
        </div>

        <div class="form-body">
            <form action="/budgets" method="POST">
                @csrf

                <div class="field-group">
                    <label class="field-label" for="category_id">
                        <i class="bi bi-tag-fill"></i>
                        Catégorie <span class="required-star">*</span>
                    </label>
                    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Choisir une catégorie --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="amount">
                        <i class="bi bi-cash-stack"></i>
                        Montant <span class="required-star">*</span>
                    </label>
                    <div style="position:relative">
                        <input type="text" id="amount" name="amount" class="form-control money-input @error('amount') is-invalid @enderror" placeholder="0" value="{{ old('amount') }}" required style="padding-right:3.5rem">
                        <span style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.85rem;font-weight:600;pointer-events:none">Ar</span>
                    </div>
                    @error('amount')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="field-hint"><i class="bi bi-info-circle"></i> Entrez le montant maximum en Ariary.</div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="month">
                        <i class="bi bi-calendar3"></i>
                        Mois <span class="required-star">*</span>
                    </label>
                    <input type="month" id="month" name="month" class="form-control @error('month') is-invalid @enderror" value="{{ old('month') }}" required>
                    @error('month')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="form-divider">

                <button type="submit" class="btn-submit">
                    <i class="bi bi-check2-circle"></i>
                    Enregistrer le budget
                </button>
            </form>
        </div>
    </div>

@endsection
