@extends('layouts.app')

@section('title', 'Nouveau Transfert – VolaKo')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--primary)"></i>Effectuer un transfert</h1>
        <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary" style="border-radius:10px">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card card-custom shadow-sm border-0 bg-white mx-auto" style="max-width:700px; border-radius:16px; overflow:hidden">
        <div class="p-4 text-white" style="background:linear-gradient(135deg, #4f46e5, #3730a3)">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <h2 class="h5 mb-1 fw-bold">Mouvement de fonds</h2>
                    <p class="mb-0 small opacity-75">Déplacez de l'argent d'un compte à un autre instantanément.</p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('transfers.store') }}" method="POST">
                @csrf
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600 small text-uppercase text-muted">Compte Source</label>
                        <select name="from_account_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ number_format($account->balance, 0, ',', ' ') }} Ar)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600 small text-uppercase text-muted">Compte Destination</label>
                        <select name="to_account_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ number_format($account->balance, 0, ',', ' ') }} Ar)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-uppercase text-muted">Montant du transfert (Ar)</label>
                    <input type="number" name="amount" class="form-control" placeholder="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-uppercase text-muted">Date</label>
                    <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small text-uppercase text-muted">Description (Optionnel)</label>
                    <textarea name="description" class="form-control" rows="2" style="resize:vertical"></textarea>
                </div>

                <button type="submit" class="btn btn-lg w-100 text-white fw-bold shadow-sm" style="background:linear-gradient(135deg, #4f46e5, #3730a3); border-radius:12px">
                    <i class="bi bi-check-circle me-2"></i> Confirmer le transfert
                </button>
            </form>
        </div>
    </div>
@endsection
