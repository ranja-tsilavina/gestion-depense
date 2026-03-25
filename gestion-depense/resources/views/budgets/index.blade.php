@extends('layouts.app')

@section('title', 'Budgets – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-wallet me-2" style="color:var(--primary)"></i>Mes Budgets</h1>
        <div class="d-flex align-items-center gap-2">
            <span class="badge-count">{{ $budgets->count() }} budget(s)</span>
            <a href="/budgets/create" class="btn-add">
                <i class="bi bi-plus-lg"></i> Nouveau budget
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-custom alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Budgets Table -->
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="bi bi-table me-2" style="color:var(--primary)"></i>Liste des budgets</h5>
        </div>
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Catégorie</th>
                        <th>Montant</th>
                        <th>Mois</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                    <tr>
                        <td><span class="id-chip">{{ $budget->id }}</span></td>
                        <td>
                            <span class="cat-badge">
                                <i class="bi bi-tag-fill"></i>
                                {{ $budget->category->name }}
                            </span>
                        </td>
                        <td class="amount-cell">
                            {{ number_format($budget->amount, 0, ',', ' ') }}
                            <span class="amount-currency">Ar</span>
                        </td>
                        <td>
                            <span class="date-pill">
                                <i class="bi bi-calendar3 me-1"></i>{{ $budget->month }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="/budgets/{{ $budget->id }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce budget ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">
                                    <i class="bi bi-trash3"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-wallet"></i>
                                <p>Aucun budget enregistré.<br>
                                    <a href="/budgets/create" style="color:var(--primary);font-weight:600">Créer votre premier budget →</a>
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
