<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des Tables</h5>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Nouvelle Table
            </button>
        </div>

        <div class="card-body border-bottom">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Rechercher une table...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="filterLocation" class="form-select">
                        <option value="">Toutes les zones</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
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

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible mx-4 mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-body">
            <div class="row g-4">
                @forelse($tables as $table)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card shadow-none border h-100">
                            <div class="card-body text-center">
                                <div class="dropdown btn-pinned">
                                    <button type="button" class="btn dropdown-toggle hide-arrow p-0"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0);"
                                                wire:click="openEditModal({{ $table->id }})">Modifier</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);"
                                                wire:click="generateQrCode({{ $table->id }})">Générer QR Code</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);"
                                                wire:click="toggleStatus({{ $table->id }})">{{ $table->is_active ? 'Désactiver' : 'Activer' }}</a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0);"
                                                wire:click="delete({{ $table->id }})"
                                                wire:confirm="Supprimer cette table ?">Supprimer</a></li>
                                    </ul>
                                </div>
                                <div class="mx-auto mb-3">
                                    @if($table->qr_code_path)
                                        <div class="mb-2">
                                            <img src="{{ $table->getQrCodeUrl() }}" alt="QR Code"
                                                class="img-fluid rounded border" style="width: 120px;">
                                        </div>
                                    @else
                                        @php $isOccupied = $table->isOccupied(); @endphp
                                        <span
                                            class="badge rounded-pill bg-label-{{ $isOccupied ? 'danger' : ($table->is_active ? 'success' : 'secondary') }} p-3 mb-2">
                                            <i class="bx bx-table fs-1"></i>
                                        </span>
                                    @endif
                                </div>
                                <h5 class="mb-1">Table {{ $table->table_number }}</h5>
                                <p class="mb-2">
                                    <span class="badge bg-label-info">{{ $table->location ?: 'Zone non définie' }}</span>
                                </p>
                                <div class="d-flex align-items-center justify-content-center mb-0 text-muted">
                                    <i class="bx bx-user me-1"></i> {{ $table->capacity }} places
                                </div>
                                <div class="mt-3">
                                    @if($isOccupied)
                                        <button class="btn btn-label-danger btn-sm w-100" disabled>Occupée</button>
                                    @elseif($table->is_active)
                                        <button class="btn btn-label-success btn-sm w-100" disabled>Disponible</button>
                                    @else
                                        <button class="btn btn-label-secondary btn-sm w-100" disabled>Inactif</button>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-link btn-sm text-primary p-0">
                                        <i class="bx bx-qr-scan me-1"></i> QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">Aucune table trouvée</div>
                @endforelse
            </div>
        </div>

        <div class="card-footer">
            {{ $tables->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Modifier la table' : 'Nouvelle table' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Numéro de table *</label>
                                    <input type="text" wire:model="table_number"
                                        class="form-control @error('table_number') is-invalid @enderror"
                                        placeholder="ex: 12">
                                    @error('table_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Capacité (personnes) *</label>
                                    <input type="number" wire:model="capacity"
                                        class="form-control @error('capacity') is-invalid @enderror">
                                    @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Localisation / Zone</label>
                                    <input type="text" wire:model="location" class="form-control"
                                        placeholder="ex: Terrasse, Salle Principale, Étage">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="table_active">
                                        <label class="form-check-label" for="table_active">Table active (visible par les
                                            clients)</label>
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