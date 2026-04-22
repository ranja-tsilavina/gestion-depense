@extends('layouts.app')

@section('title', 'Nouveau Compte – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--primary)"></i>Créer un Compte</h1>
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
                <p>Déterminez le nom et le solde initial de votre nouveau compte.</p>
            </div>
        </div>

        <form action="{{ route('accounts.store') }}" method="POST" class="form-body">
            @csrf
            <div class="field-group">
                <label class="field-label"><i class="bi bi-tag"></i> Nom du compte</label>
                <input type="text" name="name" class="form-control" placeholder="Ex: Banque, Espèces, Mobile Money" required>
            </div>

            <div class="field-group">
                <label class="field-label"><i class="bi bi-currency-exchange"></i> Solde Initial (Ar)</label>
                <div class="amount-wrapper">
                    <input type="text" name="balance" class="form-control money-input" value="0" required>
                    <span class="amount-suffix">Ar</span>
                </div>
                <p class="field-hint">Le solde sera mis à jour avec vos dépenses et revenus.</p>
            </div>

            <button type="submit" class="btn-submit mt-4">
                <i class="bi bi-check-circle"></i> Créer le compte
            </button>
        </form>
    </div>
@endsection
