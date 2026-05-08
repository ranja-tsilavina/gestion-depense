@extends('layouts.app')

@section('title', 'Mes Comptes – VolaKo')

@section('content')

<!-- MOBILE VIEW -->
<div class="mobile-only d-lg-none">
    <div class="header-section mb-4">
        <h1 class="page-title fs-4 fw-800">Mes Comptes</h1>
    </div>
    
    <div class="history-list">
        <div class="card-custom mb-4 p-4 shadow-sm border-0 bg-primary text-white">
            <div class="small opacity-75 text-uppercase fw-bold mb-1">Patrimoine Total</div>
            <div class="display-6 fw-bold mb-0">{{ number_format($accounts->sum('balance'), 0, ',', ' ') }} Ar</div>
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach($accounts as $account)
                <div class="card-custom p-4 border-0 shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold fs-5 text-dark">{{ $account->name }}</span>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                <li><a class="dropdown-item" href="{{ route('accounts.edit', $account) }}">Détails</a></li>
                                <li>
                                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger">Supprimer</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="fw-bold text-primary fs-4">{{ number_format($account->balance, 0, ',', ' ') }} Ar</div>
                    <div class="mt-2 text-muted small"><i class="bi bi-person me-1"></i>{{ $account->user->name }}</div>
                </div>
            @endforeach
            
            <a href="{{ route('accounts.create') }}" class="btn-primary-custom py-3 text-center d-block mt-2 shadow-sm" style="background:var(--primary); color:white; border-radius:12px; text-decoration:none; font-weight:600">
                <i class="bi bi-plus-circle me-2"></i>Nouveau Compte
            </a>
        </div>
    </div>
</div>

<!-- DESKTOP VIEW -->
<div class="desktop-only d-none d-lg-block">
    <div class="page-header">
        <h1><i class="bi bi-bank2 me-2" style="color:var(--primary)"></i>Gestion des Comptes</h1>
        <a href="{{ route('accounts.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Nouveau compte
        </a>
    </div>

    <div class="row g-4 mb-4">
        @foreach($accounts as $account)
            <div class="col-md-4">
                <div class="card-custom h-100 p-4 shadow-sm border-0 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div style="width: 48px; height: 48px; background: #ede9fe; color: var(--primary); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical fs-5"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4">
                                <li><a class="dropdown-item py-2" href="{{ route('accounts.edit', $account) }}"><i class="bi bi-pencil me-2"></i>Modifier</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item py-2 text-danger"><i class="bi bi-trash3 me-2"></i>Supprimer</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $account->name }}</h5>
                    <div class="display-6 fw-bold text-primary mb-3">{{ number_format($account->balance, 0, ',', ' ') }} <span style="font-size: 1rem; opacity: 0.6;">Ar</span></div>
                    <div class="text-muted small mt-auto"><i class="bi bi-person-circle me-1"></i>Responsable: {{ $account->user->name }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
