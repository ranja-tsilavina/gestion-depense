@extends('layouts.app')

@section('title', 'Profil – Gestion de Dépenses')

@section('content')

    <!-- ── Page Header ── -->
    <div class="page-header">
        <h1><i class="bi bi-person-circle me-2" style="color:var(--primary)"></i>Mon Profil</h1>
    </div>

    <!-- ── Global Success Messages ── -->
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        <div class="alert alert-success alert-custom alert-dismissible fade show mb-4" role="alert" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a">
            <i class="bi bi-check-circle-fill me-2"></i>Mise à jour réussie.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- ======================= LEFT COLUMN ======================= -->
        <div class="col-lg-7 d-flex flex-column gap-4">
            
            <!-- ── User Information Form ── -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <span class="card-header-icon" style="background:#e0e7ff">
                        <i class="bi bi-person-vcard" style="color:var(--primary)"></i>
                    </span>
                    <h5>Informations Personnelles</h5>
                </div>
                <!-- padding p-4 roughly equals Tailwind p-6 (1.5rem) -->
                <div class="p-4" style="padding: 2rem !important;">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" for="name">Nom complet <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom"><i class="bi bi-person"></i></span>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" for="email">Adresse e-mail <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-3 mt-2">
                            @if (session('status') === 'profile-updated')
                                <span class="text-success fw-semibold text-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">Enregistré.</span>
                            @endif
                            <button type="submit" class="btn-primary-custom" style="padding: 0.6rem 1.25rem;">
                                <i class="bi bi-save2 me-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ── Change Password Form ── -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <span class="card-header-icon" style="background:#fef3c7">
                        <i class="bi bi-shield-lock" style="color:#d97706"></i>
                    </span>
                    <h5>Sécurité du Compte</h5>
                </div>
                <div class="p-4" style="padding: 2rem !important;">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" for="update_password_current_password">Mot de passe actuel <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom"><i class="bi bi-lock"></i></span>
                                <input type="password" id="update_password_current_password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                            </div>
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" for="update_password_password">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom"><i class="bi bi-key"></i></span>
                                <input type="password" id="update_password_password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                            </div>
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" for="update_password_password_confirmation">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom"><i class="bi bi-key-fill"></i></span>
                                <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                            </div>
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-3 mt-2">
                            @if (session('status') === 'password-updated')
                                <span class="text-success fw-semibold text-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">Mot de passe mis à jour.</span>
                            @endif
                            <button type="submit" class="btn-primary-custom" style="padding: 0.6rem 1.25rem;">
                                <i class="bi bi-shield-check me-2"></i> Modifier le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- ======================= RIGHT COLUMN ======================= -->
        <div class="col-lg-5 d-flex flex-column gap-4">
            
            <!-- ── Financial Summary Card ── -->
            <div class="card-custom" style="background: linear-gradient(to bottom, #ffffff, #f8fafc)">
                <div class="card-header-custom border-0 pb-0 pt-4 px-4" style="background: transparent">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2" style="color:#10b981"></i>Résumé Financier</h5>
                </div>
                <div class="p-4" style="padding: 2rem !important;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-4 border-bottom" style="border-color:#f1f5f9 !important">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:48px;height:48px;border-radius:12px;background:#dcfce7;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-arrow-down-circle" style="color:#16a34a;font-size:1.4rem"></i>
                            </div>
                            <div>
                                <div style="font-size:0.85rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Total Revenus</div>
                                <div style="font-size:1.2rem;font-weight:800;color:#1e293b">{{ number_format($totalRevenues, 0, ',', ' ') }} <span style="font-size:0.85rem;color:#94a3b8">Ar</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4 pb-4 border-bottom" style="border-color:#f1f5f9 !important">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:48px;height:48px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-arrow-up-circle" style="color:#ef4444;font-size:1.4rem"></i>
                            </div>
                            <div>
                                <div style="font-size:0.85rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Total Dépenses</div>
                                <div style="font-size:1.2rem;font-weight:800;color:#1e293b">{{ number_format($totalExpenses, 0, ',', ' ') }} <span style="font-size:0.85rem;color:#94a3b8">Ar</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-4" style="background:#ede9fe;border-radius:16px;box-shadow:inset 0 2px 4px rgba(0,0,0,0.02)">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:52px;height:52px;border-radius:14px;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(99,102,241,0.3)">
                                <i class="bi bi-piggy-bank" style="font-size:1.5rem"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;color:var(--primary-dark);font-weight:700;text-transform:uppercase;letter-spacing:0.5px">Solde Actuel</div>
                                <div style="font-size:1.5rem;font-weight:900;{{ $balance < 0 ? 'color:#ef4444' : 'color:#4f46e5' }}">
                                    {{ number_format($balance, 0, ',', ' ') }} <span style="font-size:1rem;font-weight:600">Ar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Danger Zone (Delete Account) ── -->
            <div class="card-custom border-danger border-opacity-25" style="box-shadow:0 4px 20px rgba(239, 68, 68, 0.08); background:#fffaf5">
                <div class="card-header-custom border-0 pb-0 pt-4 px-4" style="background: transparent">
                    <h5 class="text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zone dangereuse</h5>
                </div>
                <div class="p-4" style="padding: 1.5rem 2rem !important;">
                    <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">Une fois votre compte supprimé, toutes ses ressources et données seront effacées de manière permanente. Cette action est irréversible.</p>
                    
                    <button type="button" class="btn w-100" style="background:#ef4444;color:white;font-weight:600;padding:0.75rem;border-radius:12px" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                        <i class="bi bi-trash3 me-2"></i> Supprimer mon compte
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================= DELETE ACCOUNT MODAL ======================= -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-danger">
                        Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body p-4" style="font-size:1rem;color:#475569;line-height:1.6">
                        Êtes-vous <span class="fw-bold text-danger">absolument sûr</span> de vouloir supprimer votre compte ?<br><br>
                        Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte. Cette action détruira toutes vos données financières.
                        
                        <div class="mt-4">
                            <label for="password" class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom"><i class="bi bi-key"></i></span>
                                <input type="password" id="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Votre mot de passe" required>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback d-block fw-medium mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0" style="gap:.75rem">
                        <button type="button" class="btn text-secondary fw-semibold" data-bs-dismiss="modal" style="background:#f1f5f9;border:0;padding:0.6rem 1.25rem;border-radius:10px">Annuler</button>
                        <button type="submit" class="btn btn-danger fw-semibold" style="padding:0.6rem 1.25rem;border-radius:10px">
                            <i class="bi bi-trash3 me-2"></i>Supprimer le compte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
