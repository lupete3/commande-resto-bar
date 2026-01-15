<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Carte du Menu</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('manager.categories') }}" class="btn btn-outline-primary" wire:navigate>
                    <i class="bx bx-list-ul me-1"></i> Catégories
                </a>
                <button wire:click="openCreateModal" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Nouvel Article
                </button>
            </div>
        </div>

        <div class="card-body border-bottom">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Rechercher un article...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="filterCategory" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible mx-4 mt-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Disponibilité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->image_path)
                                    <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" class="rounded"
                                        style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="avatar avatar-md">
                                        <span class="avatar-initial rounded bg-label-secondary"><i
                                                class="bx bx-image"></i></span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->name }}</strong><br>
                                <small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                            </td>
                            <td><span class="badge bg-label-info">{{ $item->category->name }}</span></td>
                            <td><strong>${{ number_format($item->price, 2) }}</strong></td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        wire:click="toggleAvailability({{ $item->id }})" {{ $item->is_available ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="openEditModal({{ $item->id }})">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);"
                                            wire:click="delete({{ $item->id }})" wire:confirm="Supprimer cet article ?">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucun article trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $items->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Modifier l\'article' : 'Nouvel article' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Prix ($) *</label>
                                    <input type="number" wire:model="price" step="0.01"
                                        class="form-control @error('price') is-invalid @enderror">
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Catégorie *</label>
                                    <select wire:model="category_id"
                                        class="form-select @error('category_id') is-invalid @enderror">
                                        <option value="">Sélectionner...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ordre d'affichage</label>
                                    <input type="number" wire:model="sort_order" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Image</label>
                                    <input type="file" wire:model="image"
                                        class="form-control @error('image') is-invalid @enderror">
                                    <div wire:loading wire:target="image" class="text-primary small mt-1">Upload en cours...
                                    </div>
                                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 rounded" style="width: 100px;">
                                    @elseif ($existingImage)
                                        <img src="{{ Storage::url($existingImage) }}" class="mt-2 rounded"
                                            style="width: 100px;">
                                    @endif
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" wire:model="is_available"
                                            id="item_available">
                                        <label class="form-check-label" for="item_available">Article disponible</label>
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