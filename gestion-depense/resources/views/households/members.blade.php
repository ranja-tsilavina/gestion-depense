@extends('layouts.app')

@section('title', 'Gestion des Membres – FintechApp')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-people-fill me-2" style="color:#f472b6"></i>Gestion des Membres</h1>
        <small class="text-muted">Foyer : <strong>{{ $household->name }}</strong></small>
    </div>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addMemberModal">
        <i class="bi bi-person-plus-fill"></i> Ajouter un membre
    </button>
</div>

@if(session('success'))
    <div class="alert-custom alert alert-success mb-3">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert-custom alert alert-danger mb-3">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
@endif

<div class="table-card">
    <div class="table-card-header">
        <h5><i class="bi bi-people me-2 text-primary"></i>Membres du Foyer</h5>
        <span class="badge-count">{{ $members->count() }} membre(s)</span>
    </div>
    @if($members->isEmpty())
        <div class="empty-state">
            <i class="bi bi-person-slash"></i>
            <p class="mb-0">Aucun membre dans ce foyer.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Membres depuis</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr class="table-row-animate">
                        <td><span class="id-chip">{{ $member->id }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="width:34px;height:34px;font-size:.8rem;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="color:#1e293b;">{{ $member->name }}</div>
                                    @if($member->id === $household->owner_id)
                                        <small class="text-muted">Propriétaire</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $member->email }}</td>
                        <td>
                            @if($member->pivot->role === 'owner')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border:1px solid #fcd34d;border-radius:20px;padding:.3rem .75rem;font-size:.75rem;">
                                    <i class="bi bi-shield-fill me-1"></i>Propriétaire
                                </span>
                            @else
                                <span class="badge" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;border-radius:20px;padding:.3rem .75rem;font-size:.75rem;">
                                    <i class="bi bi-person me-1"></i>Membre
                                </span>
                            @endif
                        </td>
                        <td class="date-pill">
                            <i class="bi bi-clock me-1"></i>{{ $member->pivot->created_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="text-end">
                            @if($member->id !== $household->owner_id)
                                <form action="{{ route('households.remove_member', $member) }}" method="POST"
                                      onsubmit="return confirm('Retirer {{ $member->name }} du foyer ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-delete" type="submit">
                                        <i class="bi bi-person-dash"></i> Retirer
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Add Member Modal --}}
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addMemberModalLabel">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Créer un Nouveau Membre
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('households.add_member') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:1.5rem;">
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3" style="font-size:.85rem;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="field-group">
                        <label class="field-label"><i class="bi bi-person"></i> Nom complet</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Marie Dupont" required value="{{ old('name') }}">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><i class="bi bi-envelope"></i> Adresse e-mail</label>
                        <input type="email" name="email" class="form-control" placeholder="marie@example.com" required value="{{ old('email') }}">
                    </div>
                    <div class="field-group mb-0">
                        <label class="field-label"><i class="bi bi-lock"></i> Mot de passe</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 caractères" required>
                        <p class="field-hint"><i class="bi bi-info-circle"></i> Ce membre pourra se connecter avec ces identifiants.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-person-check-fill"></i> Créer le membre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
