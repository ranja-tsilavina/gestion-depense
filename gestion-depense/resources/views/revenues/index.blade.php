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

    <!-- Filters -->
    <div class="card card-custom mb-4 border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h5 class="text-secondary mb-0" style="font-size:1rem;font-weight:600"><i class="bi bi-funnel-fill me-2"></i>Recherche avancée</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('revenues.export.pdf', request()->query()) }}" class="btn btn-sm" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:8px;font-weight:600" title="Exporter en PDF">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
                    </a>
                    <a href="{{ route('revenues.export.excel', request()->query()) }}" class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:8px;font-weight:600" title="Exporter en Excel">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
                    </a>
                </div>
            </div>
            <form method="GET" action="{{ route('revenues.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Année</label>
                    <select name="year" class="form-select" style="border-radius:10px;border:1px solid #e2e8f0">
                        <option value="">Toutes</option>
                        @php $currentYearForLoop = date('Y'); @endphp
                        @for($i = $currentYearForLoop; $i >= $currentYearForLoop - 5; $i--)
                            <option value="{{ $i }}" {{ (isset($selectedYear) && $selectedYear != '' && $selectedYear == $i) ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Mois</label>
                    <select name="month" class="form-select" style="border-radius:10px;border:1px solid #e2e8f0">
                        <option value="">Tous</option>
                        @php
                            $months = [
                                1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
                                5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Aoû',
                                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ (isset($selectedMonth) && $selectedMonth == $num) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Min (Ar)</label>
                    <input type="number" name="min_amount" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0" placeholder="0" value="{{ $minAmount ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Max (Ar)</label>
                    <input type="number" name="max_amount" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0" placeholder="∞" value="{{ $maxAmount ?? '' }}">
                </div>
                <div class="col-md-4 mt-4 mt-md-0">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Mot-clé (Source/Desc)</label>
                    <input type="text" name="keyword" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0" placeholder="Rechercher..." value="{{ $keyword ?? '' }}">
                </div>
                
                <div class="col-12 mt-4 d-flex align-items-end justify-content-end">
                    <div class="d-flex gap-2" style="max-width: 300px; width: 100%;">
                        <button type="submit" class="btn flex-grow-1" style="background:var(--accent);color:white;font-weight:600;border-radius:10px;box-shadow:0 4px 12px rgba(16,185,129,0.2)">
                            <i class="bi bi-search me-2"></i>Rechercher
                        </button>
                        <a href="{{ route('revenues.index') }}" class="btn btn-light" style="border-radius:10px; border:1px solid #e2e8f0" title="Réinitialiser">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
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
