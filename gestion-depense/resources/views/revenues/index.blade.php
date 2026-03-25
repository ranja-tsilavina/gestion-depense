@extends('layouts.app')

@section('title', 'Revenus – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--accent)"></i>Mes Revenus</h1>
        <div class="d-flex align-items-center gap-2">
            <span class="badge-count" style="background:var(--accent)">{{ $revenues->count() }} revenu(s)</span>
            <a href="/revenues/create" class="btn-add" style="background:linear-gradient(135deg,#10b981,#059669)">
                <i class="bi bi-plus-lg"></i> Nouveau revenu
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-custom alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stat Cards -->
    @php
        $totalRevenue = $revenues->sum('amount');
        $revCount = $revenues->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-6">
            <div class="stat-card card-revenue">
                <div class="stat-icon" style="background:#dcfce7">
                    <i class="bi bi-cash-coin" style="color:#16a34a"></i>
                </div>
                <div>
                    <div class="stat-label">Total revenus</div>
                    <div class="stat-value">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size:.9rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-6">
            <div class="stat-card card-count">
                <div class="stat-icon" style="background:#ede9fe">
                    <i class="bi bi-list-check" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="stat-label">Nombre de revenus</div>
                    <div class="stat-value">{{ $revCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenues Table -->
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="bi bi-table me-2" style="color:var(--accent)"></i>Liste des revenus</h5>
        </div>
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Source</th>
                        <th>Montant</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues as $revenue)
                    <tr>
                        <td><span class="id-chip">{{ $revenue->id }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:32px;height:32px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-cash-coin" style="color:#16a34a;font-size:.85rem"></i>
                                </span>
                                <span class="fw-medium">{{ $revenue->source }}</span>
                            </div>
                        </td>
                        <td class="amount-cell text-success">
                            +{{ number_format($revenue->amount, 0, ',', ' ') }}
                            <span class="amount-currency">Ar</span>
                        </td>
                        <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $revenue->description }}">{{ $revenue->description ?? '—' }}</td>
                        <td>
                            <span class="date-pill">
                                <i class="bi bi-calendar3 me-1"></i>{{ $revenue->revenue_date }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="/revenues/{{ $revenue->id }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce revenu ?')">
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
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-cash-coin"></i>
                                <p>Aucun revenu enregistré.<br>
                                    <a href="/revenues/create" style="color:var(--accent);font-weight:600">Ajouter votre premier revenu →</a>
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
