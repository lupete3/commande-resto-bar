<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Catégories de Menu</h5>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Nouvelle Catégorie
            </button>
        </div>

        <div class="card-body border-bottom">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Rechercher...">
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible mx-4 mt-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible mx-4 mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Icône</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td><span class="badge bg-label-secondary">{{ $category->sort_order }}</span></td>
                            <td><i class="bx {{ $category->icon ?: 'bx-category' }} fs-4"></i></td>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td><small>{{ Str::limit($category->description, 50) }}</small></td>
                            <td>
                                <span class="badge bg-label-{{ $category->is_active ? 'success' : 'danger' }}">
                                    {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="openEditModal({{ $category->id }})">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);"
                                            wire:click="delete({{ $category->id }})"
                                            wire:confirm="Supprimer cette catégorie ?">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucune catégorie trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model="description" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Icône (Boxicons)</label>
                                    <input type="text" wire:model="icon" class="form-control" placeholder="bx-coffee">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ordre d'affichage</label>
                                    <input type="number" wire:model="sort_order" class="form-control">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="cat_active">
                                        <label class="form-check-label" for="cat_active">Catégorie active</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>