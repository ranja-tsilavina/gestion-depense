@props(['type', 'amount', 'category', 'date', 'description' => null])

@php
    $isExpense = $type === 'expense';
    $isRevenue = in_array($type, ['revenue', 'income', 'revenu']);
    $isSaving = $type === 'saving';
    
    $colorClass = $isExpense ? 'negative' : ($isRevenue ? 'text-success' : 'text-primary');
    $prefix = $isExpense ? '-' : ($isRevenue ? '+' : '');
    $icon = $isExpense ? 'bi-receipt' : ($isRevenue ? 'bi-cash-coin' : 'bi-piggy-bank');
@endphp

<div class="transaction-item">
    <div class="t-left">
        <div class="t-icon">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="t-info">
            <span class="t-title">{{ $category }}</span>
            <span class="t-date">
                {{ $date }}
                @if($description)
                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 400; margin-top: 2px;">{{ $description }}</div>
                @endif
            </span>
        </div>
    </div>
    <div class="t-amount {{ $colorClass }}">
        {{ $prefix }}{{ number_format($amount, 0, ',', ' ') }} Ar
    </div>
</div>
