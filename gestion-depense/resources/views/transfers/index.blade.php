@extends('layouts.app')

@section('title', 'Transferts – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-arrow-left-right me-2" style="color:var(--primary)"></i>Transferts entre comptes</h1>
        <a href="{{ route('transfers.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Nouveau transfert
        </a>
    </div>

    <div class="card card-custom shadow-sm border-0" style="border-radius:16px;">
        <div class="table-responsive-custom">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>De</th>
                        <th>Vers</th>
                        <th>Montant</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->transfer_date }}</td>
                            <td><span class="badge bg-light text-dark p-2 border">{{ $transfer->fromAccount->name }}</span></td>
                            <td><span class="badge bg-primary-subtle text-primary p-2 border">{{ $transfer->toAccount->name }}</span></td>
                            <td class="amount-cell">{{ number_format($transfer->amount, 0, ',', ' ') }} Ar</td>
                            <td class="desc-cell">{{ $transfer->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucun transfert enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
