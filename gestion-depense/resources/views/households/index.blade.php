@extends('layouts.app')

@section('title', 'Ma Maison – Fintech System')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-house-door me-2" style="color:var(--primary)"></i>Ma Maison / Household</h1>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header-custom">
                    <div class="stat-icon" style="background:rgba(16,185,129,0.1);width:32px;height:32px;font-size:1rem">
                        <i class="bi bi-people" style="color:var(--accent)"></i>
                    </div>
                    <h5>Membres de la maison : <strong>{{ $household->name }}</strong></h5>
                </div>
                <div class="table-responsive-custom">
                    <table class="table data-table mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <span class="badge {{ $member->pivot->role == 'owner' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $member->pivot->role }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header-custom">
                    <h5>Mes Maisons</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group">
                        @foreach(auth()->user()->households as $h)
                            <a href="{{ route('households.switch', $h->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ session('active_household_id') == $h->id ? 'active' : '' }}">
                                {{ $h->name }}
                                @if(session('active_household_id') == $h->id)
                                    <span class="badge bg-white text-primary">Actif</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header-custom">
                    <h5>Inviter un membre</h5>
                </div>
                <form action="{{ route('households.add_member') }}" method="POST" class="card-body p-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email de l'utilisateur</label>
                        <input type="email" name="email" class="form-control" placeholder="utilisateur@exemple.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius:10px">Ajouter à la maison</button>
                    <p class="mt-2 small text-muted">L'utilisateur doit déjà avoir un compte sur DepenseApp.</p>
                </form>
            </div>

            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header-custom">
                    <h5>Créer une nouvelle maison</h5>
                </div>
                <form action="{{ route('households.store') }}" method="POST" class="card-body p-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nom de la maison</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Maison de Vacances" required>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100 py-2" style="border-radius:10px">Confirmer la création</button>
                </form>
            </div>
        </div>
    </div>
@endsection
