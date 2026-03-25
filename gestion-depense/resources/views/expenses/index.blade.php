@extends('layouts.app')

@section('title', 'Mes Dépenses – Gestion de Dépenses')

@section('content')

    @php
        $total   = $expenses->sum('amount');
        $count   = $expenses->count();
        $average = $count > 0 ? round($total / $count, 0) : 0;
    @endphp

    <!-- ── Page Header ── -->
    <div class="page-header">
        <h1><i class="bi bi-receipt-cutoff me-2" style="color:var(--primary)"></i>Mes Dépenses</h1>
        <a href="/expenses/create" class="btn-add">
            <i class="bi bi-plus-lg"></i> Nouvelle dépense
        </a>
    </div>

    <!-- ── Budget Alerts ── -->
    @if(!empty($alertes) && count($alertes) > 0)
        @foreach($alertes as $alerte)
        <div class="alert alert-danger alert-custom alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $alerte }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endforeach
    @endif

    <!-- ── Stat Cards ── -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card card-total">
                <div class="stat-icon" style="background:#ede9fe">
                    <i class="bi bi-cash-coin" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="stat-label">Total dépensé</div>
                    <div class="stat-value">{{ number_format($total, 0, ',', ' ') }} <span style="font-size:.9rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card card-count">
                <div class="stat-icon" style="background:#dcfce7">
                    <i class="bi bi-list-check" style="color:#16a34a"></i>
                </div>
                <div>
                    <div class="stat-label">Nombre de dépenses</div>
                    <div class="stat-value">{{ $count }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card card-avg">
                <div class="stat-icon" style="background:#fef3c7">
                    <i class="bi bi-bar-chart-line" style="color:#d97706"></i>
                </div>
                <div>
                    <div class="stat-label">Dépense moyenne</div>
                    <div class="stat-value">{{ number_format($average, 0, ',', ' ') }} <span style="font-size:.9rem;font-weight:500;color:#94a3b8">Ar</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Charts ── -->
    <div class="row g-4 mb-4">

        <!-- Monthly Bar Chart -->
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

        <!-- Category Doughnut + Legend -->
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

    <!-- ── Filter / Search Bar ── -->
    <div class="filter-bar mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold" style="font-size:.8rem;color:#475569">
                    <i class="bi bi-search me-1"></i>Rechercher
                </label>
                <input
                    type="text"
                    id="searchInput"
                    class="form-control"
                    placeholder="Description, catégorie…"
                    oninput="filterTable()"
                >
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:.8rem;color:#475569">
                    <i class="bi bi-tag me-1"></i>Catégorie
                </label>
                <select id="categoryFilter" class="form-select" onchange="filterTable()">
                    <option value="">Toutes les catégories</option>
                    @foreach($expenses->pluck('category.name')->unique()->filter() as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn w-100" onclick="clearFilters()"
                    style="border:1.5px solid #e2e8f0;border-radius:10px;font-size:.875rem;padding:.55rem;font-weight:500;color:#64748b">
                    <i class="bi bi-x-circle me-1"></i>Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- ── Expenses Table ── -->
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
                        <th class="sort-th" data-col="0" data-type="num" title="Trier par ID">
                            #
                            <span class="sort-icon"><i class="bi bi-caret-up-fill ci-up"></i><i class="bi bi-caret-down-fill ci-down"></i></span>
                        </th>
                        <th class="sort-th" data-col="1" data-type="str" title="Trier par catégorie">
                            Catégorie
                            <span class="sort-icon"><i class="bi bi-caret-up-fill ci-up"></i><i class="bi bi-caret-down-fill ci-down"></i></span>
                        </th>
                        <th class="sort-th" data-col="2" data-type="num" title="Trier par montant">
                            Montant
                            <span class="sort-icon"><i class="bi bi-caret-up-fill ci-up"></i><i class="bi bi-caret-down-fill ci-down"></i></span>
                        </th>
                        <th class="sort-th" data-col="3" data-type="str" title="Trier par description">
                            Description
                            <span class="sort-icon"><i class="bi bi-caret-up-fill ci-up"></i><i class="bi bi-caret-down-fill ci-down"></i></span>
                        </th>
                        <th class="sort-th" data-col="4" data-type="date" title="Trier par date">
                            Date
                            <span class="sort-icon"><i class="bi bi-caret-up-fill ci-up"></i><i class="bi bi-caret-down-fill ci-down"></i></span>
                        </th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="expenseBody">
                    @forelse($expenses as $expense)
                    <tr class="table-row-animate" data-id="{{ $expense->id }}">
                        <td><span class="id-chip">{{ $expense->id }}</span></td>
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
                        <td>
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
                        <td class="text-center">
                            <button
                                class="btn-delete"
                                onclick="confirmDelete({{ $expense->id }}, '{{ addslashes($expense->description ?: 'cette dépense') }}')"
                                title="Supprimer">
                                <i class="bi bi-trash3"></i> Supprimer
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Aucune dépense enregistrée.<br>
                                    <a href="/expenses/create" style="color:var(--primary);font-weight:600">Ajouter votre première dépense →</a>
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- No results row (injected by JS) -->
        <div id="noResults" class="empty-state" style="display:none">
            <i class="bi bi-search"></i>
            <p>Aucun résultat pour votre recherche.</p>
        </div>
    </div>

<!-- ═══════════════ DELETE CONFIRM MODAL ═══════════════ -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:.9rem;color:#475569;padding:1.25rem 1.5rem">
                Voulez-vous vraiment supprimer <strong id="deleteItemName"></strong> ?
                <br><small class="text-muted">Cette action est irréversible.</small>
            </div>
            <div class="modal-footer" style="gap:.5rem">
                <button type="button" class="btn" data-bs-dismiss="modal"
                    style="border:1.5px solid #e2e8f0;border-radius:9px;font-weight:500;font-size:.875rem;padding:.55rem 1.1rem">
                    Annuler
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-custom">
                        <i class="bi bi-trash3 me-1"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Delete modal ──
    function confirmDelete(id, name) {
        document.getElementById('deleteItemName').textContent = '"' + name + '"';
        document.getElementById('deleteForm').action = '/expenses/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // ═══════════════ CHART.JS ═══════════════

    const PALETTE = [
        '#6366f1','#f97316','#10b981','#eab308','#ef4444',
        '#3b82f6','#8b5cf6','#06b6d4','#ec4899','#14b8a6',
        '#f43f5e','#a855f7','#22c55e','#0ea5e9','#fb923c'
    ];

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // ── Monthly Bar Chart ──
    const monthlyCanvas = document.getElementById('monthlyChart');
    if (monthlyCanvas) {
        const monthlyLabels = @json($monthlyLabels);
        const monthlyTotals = @json($monthlyTotals);
        const maxVal = Math.max(...monthlyTotals);

        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Dépenses (Ar)',
                    data: monthlyTotals,
                    backgroundColor: monthlyTotals.map(v =>
                        v === maxVal ? 'rgba(99,102,241,1)' : 'rgba(99,102,241,0.35)'
                    ),
                    borderColor: monthlyTotals.map(v =>
                        v === maxVal ? '#4f46e5' : 'rgba(99,102,241,0.6)'
                    ),
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + parseInt(ctx.parsed.y).toLocaleString('fr-FR') + ' Ar'
                        },
                        backgroundColor: '#1e1b4b', padding: 10, cornerRadius: 10,
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, callback: v => parseInt(v).toLocaleString('fr-FR') },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    // ── Category Doughnut Chart ──
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const catLabels = @json($categoryLabels);
        const catTotals = @json($categoryTotals);
        const colors    = catLabels.map((_, i) => PALETTE[i % PALETTE.length]);

        catLabels.forEach((_, i) => {
            const dot = document.getElementById('dot-' + i);
            if (dot) dot.style.background = colors[i];
        });

        new Chart(categoryCanvas, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catTotals,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + parseInt(ctx.parsed).toLocaleString('fr-FR') + ' Ar'
                        },
                        backgroundColor: '#1e1b4b', padding: 10, cornerRadius: 10,
                    }
                }
            }
        });
    }

    // ── Search + filter ──
    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const cat    = document.getElementById('categoryFilter').value.toLowerCase();
        const rows   = document.querySelectorAll('#expenseBody tr[data-id]');
        let visible  = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const catText = row.querySelector('.cat-badge')?.textContent.toLowerCase() ?? '';
            const matchSearch = text.includes(search);
            const matchCat    = !cat || catText.includes(cat);
            if (matchSearch && matchCat) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('rowCount').textContent = visible + ' enregistrement(s)';
        document.getElementById('noResults').style.display = visible === 0 ? 'block' : 'none';
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        filterTable();
    }

    // ── Column sorting ──
    (function () {
        const tbody   = document.getElementById('expenseBody');
        const headers = document.querySelectorAll('th.sort-th');
        const state = {};

        function cellValue(row, col, type) {
            const cell = row.cells[col];
            if (!cell) return '';
            const raw = cell.textContent.trim();

            if (type === 'num') {
                const cleaned = raw.replace(/[^\d.,-]/g, '').replace(/\s/g,'').replace(',','.');
                const n = parseFloat(cleaned);
                return isNaN(n) ? 0 : n;
            }
            if (type === 'date') {
                const parts = raw.split('/');
                if (parts.length === 3) {
                    return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
                }
                return 0;
            }
            return raw.toLowerCase();
        }

        function updateHeaders(activeCol, dir) {
            headers.forEach(th => {
                const col = parseInt(th.dataset.col);
                th.classList.remove('active', 'sort-asc', 'sort-desc');
                if (col === activeCol) {
                    th.classList.add('active', dir === 'asc' ? 'sort-asc' : 'sort-desc');
                }
            });
        }

        function animateRows() {
            const rows = tbody.querySelectorAll('tr[data-id]');
            rows.forEach((row, i) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(6px)';
                setTimeout(() => {
                    row.style.transition = 'opacity .2s ease, transform .2s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, i * 18);
            });
        }

        function doSort(colIndex, type) {
            const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
            if (rows.length === 0) return;

            const prev = state[colIndex];
            const dir  = prev === 'asc' ? 'desc' : 'asc';
            Object.keys(state).forEach(k => delete state[k]);
            state[colIndex] = dir;

            rows.sort((a, b) => {
                const aVal = cellValue(a, colIndex, type);
                const bVal = cellValue(b, colIndex, type);

                if (typeof aVal === 'number') {
                    return dir === 'asc' ? aVal - bVal : bVal - aVal;
                }
                return dir === 'asc'
                    ? aVal.localeCompare(bVal, 'fr', { sensitivity: 'base' })
                    : bVal.localeCompare(aVal, 'fr', { sensitivity: 'base' });
            });

            rows.forEach(r => tbody.appendChild(r));
            updateHeaders(colIndex, dir);
            animateRows();
        }

        headers.forEach(th => {
            th.addEventListener('click', () => {
                doSort(parseInt(th.dataset.col), th.dataset.type);
            });
        });
    })();
</script>
@endpush
