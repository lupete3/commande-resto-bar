<div>
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Statut des Tables</h5>
                </div>
                <div class="col-md-6">
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Rechercher une table...">
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($tables as $table)
            @php $isOccupied = $table->isOccupied(); @endphp
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 {{ $isOccupied ? 'border-danger' : 'border-success' }} border shadow-none text-center cursor-pointer"
                    wire:click="toggleTableStatus({{ $table->id }})">
                    <div class="card-body p-3">
                        <div class="avatar avatar-md mx-auto mb-2">
                            <span class="avatar-initial rounded bg-label-{{ $isOccupied ? 'danger' : 'success' }}">
                                <i class="bx bx-table fs-3"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">T.{{ $table->table_number }}</h5>
                        <small class="text-muted d-block">{{ $table->location }}</small>
                        <hr class="my-2">
                        <small class="fw-medium text-{{ $isOccupied ? 'danger' : 'success' }}">
                            {{ $isOccupied ? 'Occupée' : 'Disponible' }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Aucune table active</p>
            </div>
        @endforelse
    </div>
</div>