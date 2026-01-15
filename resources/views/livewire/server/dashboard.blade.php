<div>
    <!-- Status Tabs -->
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <button type="button" wire:click="setFilter('all')"
                    class="nav-link {{ $statusFilter === 'all' ? 'active' : '' }}">
                    Toutes
                </button>
            </li>
            <li class="nav-item">
                <button type="button" wire:click="setFilter('pending')"
                    class="nav-link {{ $statusFilter === 'pending' ? 'active' : '' }}">
                    En Attente
                    <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1 text-white">
                        {{ \App\Models\Order::where('establishment_id', auth()->user()->establishment_id)->where('status', 'pending')->count() }}
                    </span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" wire:click="setFilter('preparing')"
                    class="nav-link {{ $statusFilter === 'preparing' ? 'active' : '' }}">
                    En Préparation
                </button>
            </li>
            <li class="nav-item">
                <button type="button" wire:click="setFilter('ready')"
                    class="nav-link {{ $statusFilter === 'ready' ? 'active' : '' }}">
                    Prêtes
                </button>
            </li>
            <li class="nav-item">
                <button type="button" wire:click="setFilter('served')"
                    class="nav-link {{ $statusFilter === 'served' ? 'active' : '' }}">
                    Servies
                </button>
            </li>
        </ul>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible animate__animated animate__shakeX" role="alert">
            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Occupied Tables Summary -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Tables Occupées</h5>
            <span class="badge bg-label-primary">{{ $this->occupiedTables->count() }} @choice('table|tables', $this->occupiedTables->count())</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($this->occupiedTables as $table)
                    @php 
                        $session = $table->sessions->first();
                        $ordersCount = $session->orders->count();
                        $pendingCount = $session->orders->whereNotIn('status', ['served', 'cancelled'])->count();
                        $totalSession = $session->orders->where('status', '!=', 'cancelled')->sum('total');
                    @endphp
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 bg-light position-relative">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 fw-bold">Table {{ $table->table_number }}</h6>
                                <span class="badge @if($pendingCount > 0) bg-label-warning @else bg-label-success @endif small">
                                    {{ $pendingCount }} @choice('active|actives', $pendingCount)
                                </span>
                            </div>
                            <div class="small text-muted mb-3">
                                <i class="bx bx-money me-1"></i> ${{ number_format($totalSession, 2) }}
                            </div>
                            
                            @if($pendingCount === 0)
                                <button wire:click="freeTable({{ $table->id }})" 
                                        wire:confirm="Voulez-vous vraiment libérer cette table et clôturer la session ?"
                                        class="btn btn-primary btn-sm w-100 animate__animated animate__pulse animate__infinite">
                                    <i class="bx bx-log-out me-1"></i> Libérer la table
                                </button>
                            @else
                                <button class="btn btn-outline-secondary btn-sm w-100 disabled" disabled title="Attendez que toutes les commandes soient servies">
                                    <span class="spinner-grow spinner-grow-sm me-1" role="status"></span> Service...
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-3 text-muted">
                        <i class="bx bx-info-circle me-1"></i> Aucune table n'est actuellement occupée.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($orders as $order)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-start border-{{ 
                            $order->status === 'pending' ? 'danger' :
            ($order->status === 'preparing' ? 'primary' : 'success') 
                        }} border-3 shadow-none border-top-0 border-end-0 border-bottom-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2">
                        <div>
                            <h5 class="card-title mb-0">{{ $order->order_number }}</h5>
                            <small class="text-muted">Table {{ $order->table->table_number }} •
                                {{ $order->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-label-{{ 
                                    $order->status === 'pending' ? 'danger' :
            ($order->status === 'preparing' ? 'primary' : 'success') 
                                }} uppercase">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($order->items as $item)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="fw-medium">{{ $item->quantity }}x</span>
                                        {{ $item->menuItem->name ?? 'Article inconnu' }}
                                        @if($item->notes)
                                            <br><small class="text-warning"><i class="bx bx-info-circle"></i>
                                                {{ $item->notes }}</small>
                                        @endif
                                    </span>
                                    <span class="text-muted">${{ number_format($item->subtotal, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if($order->notes)
                            <div class="alert alert-secondary py-2 px-3 mb-3 small">
                                <i class="bx bx-comment-detail me-1"></i> {{ $order->notes }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h6 class="mb-0">Total: ${{ number_format($order->total, 2) }}</h6>
                            <div class="btn-group">
                                @if($order->status === 'pending')
                                    <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')"
                                        class="btn btn-primary btn-sm">
                                        Préparer
                                    </button>
                                @elseif($order->status === 'preparing')
                                    <button wire:click="updateOrderStatus({{ $order->id }}, 'ready')"
                                        class="btn btn-success btn-sm">
                                        Prêt
                                    </button>
                                @elseif($order->status === 'ready')
                                    <button wire:click="updateOrderStatus({{ $order->id }}, 'served')"
                                        class="btn btn-label-success btn-sm">
                                        Servir
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0);"
                                            wire:click="updateOrderStatus({{ $order->id }}, 'cancelled')">Annuler</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bx bx-receipt fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Aucune commande active</h5>
                <p>Les nouvelles commandes apparaîtront ici.</p>
            </div>
        @endforelse
    </div>
</div>