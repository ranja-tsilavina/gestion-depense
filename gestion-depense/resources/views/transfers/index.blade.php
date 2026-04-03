@extends('layouts.app')

@section('title', 'Transferts – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-arrow-left-right me-2" style="color:var(--primary)"></i>Transferts entre comptes</h1>
        <a href="{{ route('transfers.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Nouveau transfert
        </a>
    </div>

    @if(session('success'))
        <div class="alert-custom alert alert-success mb-3">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        <div class="table-responsive-custom">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>De</th>
                        <th>Vers</th>
                        <th>Montant</th>
                        <th>Description</th>
                        <th>Créé par</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr class="table-row-animate">
                            <td>
                                <span class="date-pill">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $transfer->transfer_date }}
                                </span>
                            </td>
                            <td>
                                <span class="badge p-2 border" style="background:#f1f5f9;color:#334155;border-color:#e2e8f0!important;border-radius:8px;">
                                    <i class="bi bi-box-arrow-right me-1 text-danger"></i>{{ $transfer->fromAccount->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge p-2 border" style="background:#ede9fe;color:#5b21b6;border-color:#c4b5fd!important;border-radius:8px;">
                                    <i class="bi bi-box-arrow-in-right me-1 text-success"></i>{{ $transfer->toAccount->name }}
                                </span>
                            </td>
                            <td class="amount-cell">
                                {{ number_format($transfer->amount, 0, ',', ' ') }}
                                <span class="amount-currency">Ar</span>
                            </td>
                            <td class="desc-cell">{{ $transfer->description ?? '—' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="avatar-circle" style="width:22px;height:22px;font-size:.6rem;flex-shrink:0;">
                                        {{ strtoupper(substr($transfer->creator->name ?? '?', 0, 1)) }}
                                    </div>
                                    <small class="text-muted">{{ $transfer->creator->name ?? '—' }}</small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <p class="mb-0">Aucun transfert enregistré.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $transfers->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
