@extends('layouts.app')

@section('title', 'Mes Dépenses – VolaKo')

@section('content')

    @php
        $total   = $expenses->sum('amount');
        $count   = $expenses->count();
        $average = $count > 0 ? round($total / $count, 0) : 0;
    @endphp

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-receipt-cutoff me-2" style="color:var(--primary)"></i>Mes Dépenses</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('expenses.export.pdf', request()->query()) }}" class="btn btn-sm" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:8px;font-weight:600" title="Exporter en PDF">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
            </a>
            <a href="{{ route('expenses.export.excel', request()->query()) }}" class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:8px;font-weight:600" title="Exporter en Excel">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
            </a>
            <a href="/expenses/create" class="btn-add">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-sm-inline"> Nouvelle dépense</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-custom mb-4 border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Année</label>
                    <select name="year" class="form-select" style="border-radius:10px;border:1px solid #e2e8f0">
                        <option value="">Toutes</option>
                        @php $currentYearForLoop = date('Y'); @endphp
                        @for($i = $currentYearForLoop; $i >= $currentYearForLoop - 5; $i--)
                            <option value="{{ $i }}" {{ (isset($selectedYear) && $selectedYear == $i) ? 'selected' : '' }}>{{ $i }}</option>
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
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Catégorie</label>
                    <select name="category_id" class="form-select" style="border-radius:10px;border:1px solid #e2e8f0">
                        <option value="">-- Toutes --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                
                <div class="col-md-8 mt-4">
                    <label class="form-label" style="font-weight:600;font-size:0.75rem;color:#64748b;text-transform:uppercase">Mot-clé (Description)</label>
                    <input type="text" name="keyword" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0" placeholder="Rechercher une dépense..." value="{{ $keyword ?? '' }}">
                </div>
                <div class="col-md-4 mt-4 d-flex align-items-end">
                    <div class="d-flex w-100 gap-2">
                        <button type="submit" class="btn flex-grow-1" style="background:var(--primary);color:white;font-weight:600;border-radius:10px;box-shadow:0 4px 12px rgba(79,70,229,0.2)">
                            <i class="bi bi-search me-2"></i>Rechercher
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-light" style="border-radius:10px; border:1px solid #e2e8f0" title="Réinitialiser">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Budget Alerts -->
    @if(!empty($alertes) && count($alertes) > 0)
        @foreach($alertes as $alerte)
        <div class="alert alert-danger alert-custom alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $alerte }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endforeach
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="stat-card card-total">
                <div class="stat-icon" style="background:#ede9fe">
                    <i class="bi bi-cash-coin" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="stat-label">Total dépensé</div>
                    <div class="stat-value">{{ number_format($total, 0, ',', ' ') }} <span style="font-size:.8rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="stat-card card-count">
                <div class="stat-icon" style="background:#dcfce7">
                    <i class="bi bi-list-check" style="color:#16a34a"></i>
                </div>
                <div>
                    <div class="stat-label">Nombre</div>
                    <div class="stat-value">{{ $count }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="stat-card card-avg">
                <div class="stat-icon" style="background:#fef3c7">
                    <i class="bi bi-bar-chart-line" style="color:#d97706"></i>
                </div>
                <div>
                    <div class="stat-label">Moyenne</div>
                    <div class="stat-value">{{ number_format($average, 0, ',', ' ') }} <span style="font-size:.8rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h5><i class="bi bi-bar-chart-fill me-2" style="color:var(--primary)"></i>Dépenses mensuelles</h5>
                    <span style="font-size:.78rem;color:#94a3b8;font-weight:500">12 derniers mois</span>
                </div>
                <div class="chart-body">
                    @if($monthlyTotals->isEmpty())
                        <div class="chart-empty">
                            <i class="bi bi-bar-chart"></i>
                            <p>Aucune donnée disponible pour le moment.</p>
                        </div>
                    @else
                        <canvas id="monthlyChart" style="max-height:280px"></canvas>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h5><i class="bi bi-pie-chart-fill me-2" style="color:#f97316"></i>Répartition par catégorie</h5>
                    <span style="font-size:.78rem;color:#94a3b8;font-weight:500">Toutes périodes</span>
                </div>
                <div class="chart-body">
                    @if($categoryTotals->isEmpty())
                        <div class="chart-empty">
                            <i class="bi bi-pie-chart"></i>
                            <p>Aucune donnée disponible.</p>
                        </div>
                    @else
                        <div class="row g-3 align-items-center">
                            <div class="col-6">
                                <canvas id="categoryChart" style="max-height:200px"></canvas>
                            </div>
                            <div class="col-6">
                                @php $catTotal = $categoryTotals->sum(); @endphp
                                <ul class="legend-list">
                                    @foreach($categoryLabels as $i => $label)
                                        @php
                                            $pct = $catTotal > 0 ? round($categoryTotals[$i] / $catTotal * 100) : 0;
                                            $rankClass = $i === 0 ? 'top-1' : ($i === 1 ? 'top-2' : ($i === 2 ? 'top-3' : ''));
                                        @endphp
                                        <li class="legend-item {{ $rankClass }}">
                                            <span class="legend-dot" id="dot-{{ $i }}"></span>
                                            <span class="legend-name">{{ $label }}
                                                @if($i === 0)<span class="high-badge">&#9650; TOP</span>@endif
                                            </span>
                                            <span class="legend-amount">{{ number_format($categoryTotals[$i], 0, ',', ' ') }} Ar<br>
                                                <span style="font-weight:400;color:#94a3b8;font-size:.72rem">{{ $pct }}%</span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="bi bi-table me-2" style="color:var(--primary)"></i>Liste des dépenses</h5>
            <span id="rowCount" class="badge" style="background:#ede9fe;color:var(--primary-dark);font-size:.8rem;font-weight:600;padding:.35rem .75rem;border-radius:20px">
                {{ $count }} enregistrement(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table data-table" id="expenseTable">
                <thead>
                    <tr>
                        <th class="sort-th d-none d-md-table-cell" data-col="0" data-type="num">#</th>
                        <th class="sort-th" data-col="1" data-type="str">Catégorie</th>
                        <th class="sort-th" data-col="2" data-type="num">Montant</th>
                        <th class="sort-th d-none d-lg-table-cell" data-col="3" data-type="str">Description</th>
                        <th class="sort-th" data-col="4" data-type="date">Date</th>
                        <th class="d-none d-md-table-cell">Créé par</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="expenseBody">
                    @forelse($expenses as $expense)
                    <tr class="table-row-animate" data-id="{{ $expense->id }}">
                        <td class="d-none d-md-table-cell"><span class="id-chip">{{ $expense->id }}</span></td>
                        <td>
                            <span class="cat-badge">
                                <i class="bi bi-tag-fill"></i>
                                {{ $expense->category->name }}
                            </span>
                        </td>
                        <td class="amount-cell">
                            {{ number_format($expense->amount, 0, ',', ' ') }}
                            <span class="amount-currency">Ar</span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="desc-cell d-block" title="{{ $expense->description }}">
                                {{ $expense->description ?: '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="date-pill">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <div class="d-flex align-items-center gap-1">
                                <div class="avatar-circle" style="width:22px;height:22px;font-size:.6rem;flex-shrink:0;">
                                    {{ strtoupper(substr($expense->creator->name ?? '?', 0, 1)) }}
                                </div>
                                <small class="text-muted">{{ $expense->creator->name ?? '—' }}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <button class="btn-delete" onclick="confirmDelete({{ $expense->id }}, '{{ addslashes($expense->description ?: 'cette dépense') }}')">
                                <i class="bi bi-trash3"></i>
                                <span class="d-none d-sm-inline"> Supprimer</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Aucune dépense enregistrée.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirmer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Voulez-vous vraiment supprimer <strong id="deleteItemName"></strong> ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteItemName').textContent = '"' + name + '"';
        document.getElementById('deleteForm').action = '/expenses/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    const PALETTE = ['#6366f1','#f97316','#10b981','#eab308','#ef4444','#3b82f6','#8b5cf6'];
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    const monthlyCanvas = document.getElementById('monthlyChart');
    if (monthlyCanvas) {
        const monthlyLabels = {!! json_encode($monthlyLabels) !!};
        const monthlyTotals = {!! json_encode($monthlyTotals) !!};
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Dépenses (Ar)',
                    data: monthlyTotals,
                    backgroundColor: 'rgba(99,102,241,0.35)',
                    borderColor: '#6366f1',
                    borderWidth: 1.5,
                    borderRadius: 8,
                }]
            }
        });
    }

    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const catLabels = {!! json_encode($categoryLabels) !!};
        const catTotals = {!! json_encode($categoryTotals) !!};
        catLabels.forEach((_, i) => {
            const dot = document.getElementById('dot-' + i);
            if (dot) dot.style.background = PALETTE[i % PALETTE.length];
        });
        new Chart(categoryCanvas, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catTotals,
                    backgroundColor: PALETTE,
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            }
        });
    }
</script>
@endpush
