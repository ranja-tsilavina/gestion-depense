@extends('layouts.app')

@section('title', 'Catégories – Gestion de Dépenses')

@section('content')

    <!-- Header -->
    <div class="page-header">
        <h1><i class="bi bi-tag me-2" style="color:var(--primary)"></i>Gestion des Catégories</h1>
        <span class="badge-count">{{ count($categories) }} catégorie(s)</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-custom alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-custom alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        <!-- ── Add Category Form ── -->
        <div class="col-lg-4">
            <div class="card-custom h-100">
                <div class="card-header-custom">
                    <span class="card-header-icon" style="background:#ede9fe">
                        <i class="bi bi-plus-lg" style="color:var(--primary)"></i>
                    </span>
                    <h5>Ajouter une catégorie</h5>
                </div>
                <div class="p-4">
                    <form method="POST" action="/categories" id="categoryForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ex. : Alimentation, Transport…"
                                value="{{ old('name') }}"
                                required
                                maxlength="60"
                                autocomplete="off"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted mt-1" style="font-size:.78rem">
                                <i class="bi bi-info-circle"></i> Maximum 60 caractères.
                            </div>
                        </div>
                        <button type="submit" class="btn-primary-custom w-100" id="submitBtn">
                            <i class="bi bi-plus-circle"></i> Ajouter la catégorie
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ── Category List ── -->
        <div class="col-lg-8">
            <div class="card-custom h-100">
                <div class="card-header-custom">
                    <span class="card-header-icon" style="background:#dcfce7">
                        <i class="bi bi-list-ul" style="color:#16a34a"></i>
                    </span>
                    <h5>Liste des catégories</h5>
                </div>
                <div class="p-4">
                    @if($categories->isEmpty())
                        <div class="empty-state">
                            <i class="bi bi-folder-x"></i>
                            <p class="mb-0">Aucune catégorie pour l'instant.<br>
                                <small>Ajoutez-en une à gauche !</small>
                            </p>
                        </div>
                    @else
                        <ul class="category-list">
                            @foreach($categories as $index => $category)
                                <li class="d-flex justify-content-between align-items-center" style="background:var(--light);padding:0.5rem;border-radius:12px;border:1px solid rgba(0,0,0,0.03);">
                                    <span class="category-pill mb-0" style="background:transparent;border:none;padding:0">
                                        <span class="category-num">{{ $index + 1 }}</span>
                                        {{ $category->name }}
                                    </span>
                                    <form method="POST" action="/categories/{{ $category->id }}" class="m-0" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:0.25rem 0.5rem" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    // Prevent double-submit
    document.getElementById('categoryForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';
    });
</script>
@endpush
