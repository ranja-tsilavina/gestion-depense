@extends('layouts.app')

@section('title', 'Nouveau Transfert – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--primary)"></i>Effectuer un transfert</h1>
        <a href="{{ route('transfers.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="form-card shadow-lg">
        <div class="form-card-header" style="background:linear-gradient(135deg, #4f46e5, #3730a3)">
            <div class="form-card-icon">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div>
                <h2>Mouvement de fonds</h2>
                <p>Déplacez de l'argent d'un compte à un autre instantanément.</p>
            </div>
        </div>

        <form action="{{ route('transfers.store') }}" method="POST" class="form-body">
            @csrf
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="field-label">Compte Source</label>
                    <select name="from_account_id" class="form-select" required>
                        <option value="">Sélectionner</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} ({{ number_format($account->balance, 0, ',', ' ') }} Ar)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="field-label">Compte Destination</label>
                    <select name="to_account_id" class="form-select" required>
                        <option value="">Sélectionner</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} ({{ number_format($account->balance, 0, ',', ' ') }} Ar)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Montant du transfert (Ar)</label>
                <div class="amount-wrapper">
                    <input type="text" name="amount" class="form-control money-input" placeholder="0" required>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Date</label>
                <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="field-group">
                <label class="field-label">Description (Optionnel)</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn-submit mt-4">
                <i class="bi bi-check-circle"></i> Confirmer le transfert
            </button>
        </form>
    </div>
@endsection
