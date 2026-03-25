@extends('layouts.app')

@section('title', 'Nouvelle Dépense – Gestion de Dépenses')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--primary)"></i>Nouvelle Dépense</h1>
        <a href="/expenses" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour aux dépenses
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-custom alert-dismissible fade show mb-3" role="alert"
             style="max-width:700px;margin:0 auto 1.5rem">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Veuillez corriger les erreurs ci-dessous avant de continuer.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">

        <!-- Gradient Header -->
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <h2>Enregistrer une dépense</h2>
                <p>Remplissez les informations ci-dessous pour ajouter une nouvelle dépense.</p>
            </div>
        </div>

        <!-- Form Body -->
        <div class="form-body">
            <form action="/expenses" method="POST" id="expenseForm">
                @csrf

                <!-- Row: Montant + Catégorie -->
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label" for="amount">
                                <i class="bi bi-cash-stack"></i>
                                Montant <span class="required-star">*</span>
                            </label>
                            <div class="amount-wrapper">
                                <input
                                    type="number"
                                    id="amount"
                                    name="amount"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    placeholder="0"
                                    value="{{ old('amount') }}"
                                    min="1"
                                    step="1"
                                    required
                                    oninput="updatePreview(this.value)"
                                >
                                <span class="amount-suffix">Ar</span>
                            </div>
                            <div class="amount-preview" id="amountPreview"></div>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="field-hint">
                                <i class="bi bi-info-circle"></i> Entrez le montant en Ariary.
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label" for="category_id">
                                <i class="bi bi-tag-fill"></i>
                                Catégorie <span class="required-star">*</span>
                            </label>
                            <select
                                id="category_id"
                                name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                required
                            >
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Choisir une catégorie --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="select-hint">
                                <i class="bi bi-plus-circle"></i>
                                <a href="/categories" style="color:var(--primary);font-weight:600;text-decoration:none">Gérer les catégories</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date -->
                <div class="field-group">
                    <label class="field-label" for="expense_date">
                        <i class="bi bi-calendar3"></i>
                        Date de la dépense <span class="required-star">*</span>
                    </label>
                    <input
                        type="date"
                        id="expense_date"
                        name="expense_date"
                        class="form-control @error('expense_date') is-invalid @enderror"
                        value="{{ old('expense_date', date('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}"
                        required
                    >
                    @error('expense_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="field-hint">
                        <i class="bi bi-info-circle"></i> La date ne peut pas être dans le futur.
                    </div>
                </div>

                <!-- Description -->
                <div class="field-group">
                    <label class="field-label" for="description">
                        <i class="bi bi-pencil-square"></i>
                        Description <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0">(optionnelle)</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Ex. : Courses du marché, carburant, loyer…"
                        maxlength="255"
                        oninput="updateCharCount(this)"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="field-hint">
                        <i class="bi bi-type"></i>
                        <span id="charCount">0</span> / 255 caractères
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-check2-circle"></i>
                    Enregistrer la dépense
                </button>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Live amount preview (formatted)
    function updatePreview(value) {
        const preview = document.getElementById('amountPreview');
        if (!value || value < 1) {
            preview.style.display = 'none';
            return;
        }
        const formatted = parseInt(value).toLocaleString('fr-FR');
        preview.style.display = 'block';
        preview.innerHTML = '<i class="bi bi-check-circle-fill me-1" style="color:#10b981"></i>' + formatted + ' Ar';
    }

    // Character counter for description
    function updateCharCount(el) {
        document.getElementById('charCount').textContent = el.value.length;
    }

    // Pre-fill char count on page load (old() value)
    const desc = document.getElementById('description');
    if (desc && desc.value) updateCharCount(desc);

    // Pre-fill amount preview on page load (old() value)
    const amt = document.getElementById('amount');
    if (amt && amt.value) updatePreview(amt.value);

    // Prevent double-submit
    document.getElementById('expenseForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement en cours…';
    });
</script>
@endpush