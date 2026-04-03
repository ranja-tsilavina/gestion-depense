@extends('layouts.app')

@section('title', 'Journal d\'Activité – FintechApp')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-activity me-2" style="color:var(--primary)"></i>Journal d'Activité</h1>
    <span class="badge-count">{{ $activities->total() }} entrées</span>
</div>

@if(session('error'))
    <div class="alert-custom alert alert-danger mb-3">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
@endif

<div class="table-card">
    @if($activities->isEmpty())
        <div class="empty-state">
            <i class="bi bi-clock-history"></i>
            <p class="mb-0">Aucune activité enregistrée pour ce foyer.</p>
        </div>
    @else
        {{-- Timeline --}}
        <div class="p-3 p-md-4">
            <div class="position-relative" style="padding-left: 2.5rem;">
                {{-- Vertical line --}}
                <div style="position:absolute;left:0.9rem;top:0;bottom:0;width:2px;background:linear-gradient(180deg,#6366f1 0%,#e2e8f0 100%);border-radius:2px;"></div>

                @foreach($activities as $activity)
                    @php
                        $icons = [
                            'expense_created'  => ['icon' => 'bi-receipt-cutoff', 'color' => '#ef4444', 'bg' => '#fef2f2'],
                            'revenue_created'  => ['icon' => 'bi-plus-circle-fill','color' => '#16a34a', 'bg' => '#f0fdf4'],
                            'transfer_created' => ['icon' => 'bi-arrow-left-right','color' => '#6366f1', 'bg' => '#ede9fe'],
                        ];
                        $style = $icons[$activity->action] ?? ['icon'=>'bi-circle','color'=>'#94a3b8','bg'=>'#f8fafc'];
                    @endphp
                    <div class="d-flex align-items-start mb-4 position-relative">
                        {{-- Icon bubble --}}
                        <div class="position-absolute" style="left:-2.5rem;top:2px;">
                            <div style="width:2rem;height:2rem;border-radius:50%;background:{{ $style['bg'] }};border:2px solid {{ $style['color'] }};display:flex;align-items:center;justify-content:center;">
                                <i class="bi {{ $style['icon'] }}" style="color:{{ $style['color'] }};font-size:.75rem;"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-grow-1 ms-1">
                            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                                <div class="card-body py-3 px-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="avatar-circle" style="width:24px;height:24px;font-size:.65rem;flex-shrink:0;">
                                                    {{ strtoupper(substr($activity->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <strong style="font-size:.9rem;color:#1e293b;">{{ $activity->user->name ?? 'Utilisateur inconnu' }}</strong>
                                                <span class="badge" style="font-size:.65rem;background:{{ $style['bg'] }};color:{{ $style['color'] }};border:1px solid {{ $style['color'] }};border-radius:20px;padding:.2rem .6rem;">
                                                    {{ str_replace('_', ' ', $activity->action) }}
                                                </span>
                                            </div>
                                            <p class="mb-0 text-secondary" style="font-size:.85rem;">{{ $activity->description }}</p>
                                        </div>
                                        <div class="text-muted" style="font-size:.75rem;white-space:nowrap;">
                                            <i class="bi bi-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $activities->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
