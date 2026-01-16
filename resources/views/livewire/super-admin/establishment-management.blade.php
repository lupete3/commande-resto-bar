<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des Établissements</h5>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Nouvel Établissement
            </button>
        </div>

        <!-- Filters -->
        <div class="card-body border-bottom">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Rechercher...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterType" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="bar">Bar</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="hotel">Hôtel</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="1">Actifs</option>
                        <option value="0">Inactifs</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible mx-4 mt-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Établissement</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Abonnement</th>
                        <th>Tables</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($establishments as $establishment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($establishment->logo)
                                        <img src="{{ Storage::url($establishment->logo) }}" alt="{{ $establishment->name }}"
                                            class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="avatar avatar-sm me-2">
                                            <span
                                                class="avatar-initial rounded bg-label-primary">{{ substr($establishment->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $establishment->name }}</strong><br>
                                        <small class="text-muted">{{ $establishment->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-label-info">{{ ucfirst($establishment->type) }}</span></td>
                            <td>
                                {{ $establishment->email }}<br>
                                <small class="text-muted">{{ $establishment->phone }}</small>
                            </td>
                            <td>
                                @if($establishment->subscription)
                                    <span
                                        class="badge bg-label-{{ $establishment->subscription->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($establishment->subscription->plan) }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $establishment->max_tables }}</td>
                            <td>
                                @if($establishment->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @else
                                    <span class="badge bg-label-danger">Inactif</span>
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
                                            wire:click="openEditModal({{ $establishment->id }})">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="toggleStatus({{ $establishment->id }})">
                                            <i class="bx bx-power-off me-1"></i>
                                            {{ $establishment->is_active ? 'Désactiver' : 'Activer' }}
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);"
                                            wire:click="delete({{ $establishment->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cet établissement ?">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Aucun établissement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            {{ $establishments->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Modifier l\'établissement' : 'Nouvel établissement' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" wire:model.live="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Type *</label>
                                    <select wire:model="type" class="form-select @error('type') is-invalid @enderror">
                                        <option value="bar">Bar</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="hotel">Hôtel</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Slug *</label>
                                    <input type="text" wire:model="slug"
                                        class="form-control @error('slug') is-invalid @enderror">
                                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" wire:model="email"
                                        class="form-control @error('email') is-invalid @enderror">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone *</label>
                                    <input type="text" wire:model="phone"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Devise *</label>
                                    <input type="text" wire:model="currency"
                                        class="form-control @error('currency') is-invalid @enderror"
                                        placeholder="ex: $, CDF, EUR">
                                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Adresse *</label>
                                    <textarea wire:model="address"
                                        class="form-control @error('address') is-invalid @enderror" rows="2"></textarea>
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Logo</label>
                                    <input type="file" wire:model="logo"
                                        class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                    @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre max de tables *</label>
                                    <input type="number" wire:model="max_tables"
                                        class="form-control @error('max_tables') is-invalid @enderror">
                                    @error('max_tables') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="is_active">
                                        <label class="form-check-label" for="is_active">Établissement actif</label>
                                    </div>
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
    @endif
    </div>
</div>