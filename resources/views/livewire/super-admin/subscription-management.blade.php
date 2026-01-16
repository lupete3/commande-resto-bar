<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des Abonnements</h5>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Nouvel Abonnement
            </button>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible mx-4 mt-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Établissement</th>
                        <th>Plan</th>
                        <th>Prix</th>
                        <th>Cycle</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td>
                                <strong>{{ $subscription->establishment->name }}</strong><br>
                                <small class="text-muted">{{ $subscription->establishment->type }}</small>
                            </td>
                            <td>
                                <span
                                    class="badge bg-label-{{ $subscription->plan === 'enterprise' ? 'info' : ($subscription->plan === 'premium' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($subscription->plan) }}
                                </span>
                            </td>
                            <td>{{ $subscription->establishment->currency ?? '$' }}{{ number_format($subscription->price, 2) }}
                            </td>
                            <td>{{ ucfirst($subscription->billing_cycle) }}</td>
                            <td>{{ $subscription->started_at->format('d/m/Y') }}</td>
                            <td>{{ $subscription->ends_at?->format('d/m/Y') }}</td>
                            <td>
                                @if($subscription->status === 'active')
                                    <span class="badge bg-label-success">Actif</span>
                                @elseif($subscription->status === 'cancelled')
                                    <span class="badge bg-label-danger">Annulé</span>
                                @else
                                    <span class="badge bg-label-warning">Expiré</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="openEditModal({{ $subscription->id }})">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        @if($subscription->status === 'active')
                                            <a class="dropdown-item text-success" href="javascript:void(0);"
                                                wire:click="renew({{ $subscription->id }})">
                                                <i class="bx bx-refresh me-1"></i> Renouveler
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                wire:click="cancel({{ $subscription->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir annuler cet abonnement ?">
                                                <i class="bx bx-x me-1"></i> Annuler
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Aucun abonnement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $subscriptions->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Modifier l\'abonnement' : 'Nouvel abonnement' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Établissement *</label>
                                    <select wire:model="establishment_id"
                                        class="form-select @error('establishment_id') is-invalid @enderror">
                                        <option value="">Sélectionner...</option>
                                        @foreach($establishments as $est)
                                            <option value="{{ $est->id }}">{{ $est->name }} ({{ $est->type }})</option>
                                        @endforeach
                                    </select>
                                    @error('establishment_id') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Plan *</label>
                                    <select wire:model.live="plan" class="form-select @error('plan') is-invalid @enderror">
                                        <option value="basic">Basic - $20</option>
                                        <option value="premium">Premium - $50</option>
                                        <option value="enterprise">Enterprise - $100</option>
                                    </select>
                                    @error('plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prix ($) *</label>
                                    <input type="number" wire:model="price"
                                        class="form-control @error('price') is-invalid @enderror" step="0.01">
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cycle *</label>
                                    <select wire:model.live="billing_cycle"
                                        class="form-select @error('billing_cycle') is-invalid @enderror">
                                        <option value="monthly">Mensuel</option>
                                        <option value="yearly">Annuel</option>
                                    </select>
                                    @error('billing_cycle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status *</label>
                                    <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active">Actif</option>
                                        <option value="cancelled">Annulé</option>
                                        <option value="expired">Expiré</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de début *</label>
                                    <input type="date" wire:model="started_at"
                                        class="form-control @error('started_at') is-invalid @enderror">
                                    @error('started_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de fin *</label>
                                    <input type="date" wire:model="ends_at"
                                        class="form-control @error('ends_at') is-invalid @enderror">
                                    @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            {{ $editMode ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>