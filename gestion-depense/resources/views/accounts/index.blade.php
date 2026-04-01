@extends('layouts.app')

@section('title', 'Mes Comptes – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-bank me-2" style="color:var(--primary)"></i>Mes Comptes</h1>
        <a href="{{ route('accounts.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Nouveau compte
        </a>
    </div>

    <div class="row g-4 mb-4">
        @foreach($accounts as $account)
            <div class="col-md-4">
                <div class="card card-custom h-100 shadow-sm border-0" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="stat-icon" style="background:rgba(99,102,241,0.1)">
                                <i class="bi bi-wallet2" style="color:var(--primary)"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('accounts.edit', $account) }}">Détails / Modifier</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('accounts.destroy', $account) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger">Supprimer</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <h5 class="mb-1" style="font-weight:700;color:#1e1b4b">{{ $account->name }}</h5>
                        <div class="stat-value" style="font-size:1.75rem">
                            {{ number_format($account->balance, 0, ',', ' ') }} <span style="font-size:0.9rem;color:#94a3b8">Ar</span>
                        </div>
                        <div class="text-muted small mt-2">
                            Créé par {{ $account->user->name }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
