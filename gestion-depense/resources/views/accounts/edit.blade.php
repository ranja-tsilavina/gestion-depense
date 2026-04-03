@extends('layouts.app')

@section('title', 'Modifier le Compte – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--primary)"></i>Modifier le Compte</h1>
        <a href="{{ route('accounts.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="form-card shadow-lg">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="bi bi-bank"></i>
            </div>
            <div>
                <h2>Informations du compte</h2>
                <p>Mettez à jour le nom et le solde de votre compte.</p>
            </div>
        </div>

        <form action="{{ route('accounts.update', $account) }}" method="POST" class="form-body">
            @csrf
            @method('PATCH')
            
            <div class="field-group">
                <label class="field-label"><i class="bi bi-tag"></i> Nom du compte</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}" required>
            </div>

            <div class="field-group">
                <label class="field-label"><i class="bi bi-currency-exchange"></i> Solde actuel (Ar)</label>
                <div class="amount-wrapper">
                    <input type="text" name="balance" class="form-control money-input" value="{{ old('balance', $account->balance) }}" required>
                    <span class="amount-suffix">Ar</span>
                </div>
                <p class="field-hint">Le solde sera mis à jour avec vos dépenses et revenus.</p>
            </div>

            <button type="submit" class="btn-submit mt-4">
                <i class="bi bi-check-circle"></i> Mettre à jour le compte
            </button>
        </form>
    </div>
@endsection
