@extends('layouts.app')

@section('title', 'Dashboard – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-speedometer2 me-2" style="color:var(--primary)"></i>Dashboard</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/expenses/create" class="btn-add" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                <i class="bi bi-dash-circle"></i> Nouvelle dépense
            </a>
            <a href="/revenues/create" class="btn-add" style="background:linear-gradient(135deg,#10b981,#059669)">
                <i class="bi bi-plus-circle"></i> Nouveau revenu
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-custom mb-4 border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('dashboard') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;font-size:0.85rem;color:#64748b">Année</label>
                    <select name="year" class="form-select" style="border-radius:12px;cursor:pointer;border:1px solid #e2e8f0;padding:0.6rem 1rem">
                        @php $currentYearForLoop = date('Y'); @endphp
                        @for($i = $currentYearForLoop; $i >= $currentYearForLoop - 5; $i--)
                            <option value="{{ $i }}" {{ (isset($selectedYear) && $selectedYear == $i) ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;font-size:0.85rem;color:#64748b">Mois</label>
                    <select name="month" class="form-select" style="border-radius:12px;cursor:pointer;border:1px solid #e2e8f0;padding:0.6rem 1rem">
                        <option value="">-- Toute l'année --</option>
                        @php
                            $months = [
                                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ (isset($selectedMonth) && $selectedMonth == $num) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn w-100" style="background:var(--primary);color:white;font-weight:600;border-radius:12px;padding:0.6rem 1rem;box-shadow:0 4px 12px rgba(79,70,229,0.2)">
                        <i class="bi bi-funnel-fill me-2"></i>Filtrer
                    </button>
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
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card card-revenue">
                <div class="stat-icon" style="background:#dcfce7">
                    <i class="bi bi-arrow-down-circle" style="color:#16a34a"></i>
                </div>
                <div>
                    <div class="stat-label">Total Revenus</div>
                    <div class="stat-value">{{ number_format($totalRevenues ?? 0, 0, ',', ' ') }} <span style="font-size:.9rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card card-expense">
                <div class="stat-icon" style="background:#fef2f2">
                    <i class="bi bi-arrow-up-circle" style="color:#ef4444"></i>
                </div>
                <div>
                    <div class="stat-label">Total Dépenses</div>
                    <div class="stat-value">{{ number_format($totalExpenses ?? 0, 0, ',', ' ') }} <span style="font-size:.9rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            @php $balance = ($totalRevenues ?? 0) - ($totalExpenses ?? 0); @endphp
            <div class="stat-card card-balance">
                <div class="stat-icon" style="background:#ede9fe">
                    <i class="bi bi-wallet2" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="stat-label">Solde restant</div>
                    <div class="stat-value" style="{{ $balance < 0 ? 'color:#ef4444' : '' }}">{{ number_format($balance, 0, ',', ' ') }} <span style="font-size:.9rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h5><i class="bi bi-bar-chart-fill me-2" style="color:var(--primary)"></i>Dépenses vs Budget</h5>
                </div>
                <div class="chart-body">
                    <canvas id="expensesBudgetChart" style="max-height:280px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h5><i class="bi bi-pie-chart-fill me-2" style="color:#f97316"></i>Répartition dépenses</h5>
                </div>
                <div class="chart-body">
                    <canvas id="expenseDistributionChart" style="max-height:280px"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection

@php
    $chartCategoryLabels = $chartCategories ?? [];
    $chartExpenseValues = $chartExpenses ?? [];
    $chartBudgetValues = $chartBudgets ?? [];
@endphp

@push('scripts')
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // Bar Chart - Expenses vs Budget
    new Chart(document.getElementById('expensesBudgetChart'), {
        type: 'bar',
        data: {
            labels: @json($chartCategoryLabels),
            datasets: [
                {
                    label: 'Dépenses',
                    data: @json($chartExpenseValues),
                    backgroundColor: 'rgba(239,68,68,0.7)',
                    borderColor: 'rgba(239,68,68,1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Budget',
                    data: @json($chartBudgetValues),
                    backgroundColor: 'rgba(99,102,241,0.35)',
                    borderColor: 'rgba(99,102,241,1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: { label: ctx => ' ' + parseInt(ctx.parsed.y).toLocaleString('fr-FR') + ' Ar' },
                    backgroundColor: '#1e1b4b', padding: 10, cornerRadius: 10,
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    grid: { color: '#f1f5f9' },
                    beginAtZero: true,
                    ticks: { callback: v => parseInt(v).toLocaleString('fr-FR') }
                }
            }
        }
    });

    // Doughnut Chart - Expense Distribution
    new Chart(document.getElementById('expenseDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: @json($chartCategoryLabels),
            datasets: [{
                data: @json($chartExpenseValues),
                backgroundColor: ['#6366f1','#f97316','#10b981','#eab308','#ef4444','#3b82f6','#8b5cf6'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 16, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: { label: ctx => ' ' + parseInt(ctx.parsed).toLocaleString('fr-FR') + ' Ar' },
                    backgroundColor: '#1e1b4b', padding: 10, cornerRadius: 10,
                }
            }
        }
    });
</script>
@endpush
