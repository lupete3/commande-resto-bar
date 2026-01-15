<div>
    <div class="row g-4">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">Commandes Aujourd'hui</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $stats['orders_today'] }}</h4>
                                <small class="text-success">(Total: {{ $stats['total_orders'] }})</small>
                            </div>
                            <p class="mb-0 text-muted">Aujourd'hui</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-cart bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">Revenus Aujourd'hui</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">${{ number_format($stats['revenue_today'], 2) }}</h4>
                            </div>
                            <p class="mb-0 text-muted">Ventes réalisées</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-dollar bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">Tables Occupées</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $stats['active_tables'] }}</h4>
                                <small class="text-muted">/ {{ $stats['total_tables'] }}</small>
                            </div>
                            <p class="mb-0 text-muted">Occupation actuelle</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-table bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">Équipe & Menu</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $stats['total_servers'] }}</h4>
                                <small class="text-muted">Serveurs | {{ $stats['total_menu_items'] }} Items</small>
                            </div>
                            <p class="mb-0 text-muted">Capacité opérationnelle</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-user bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Commandes Récentes</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="recentOrders" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentOrders">
                            <a class="dropdown-item" href="javascript:void(0);">Voir tout</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th># Numéro</th>
                                <th>Table</th>
                                <th>Serveur</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($recentOrders as $order)
                                                    <tr>
                                                        <td><span class="fw-medium">{{ $order->order_number }}</span></td>
                                                        <td>Table {{ $order->table->number ?? 'N/A' }}</td>
                                                        <td>{{ $order->server->name ?? 'Client (QR)' }}</td>
                                                        <td>${{ number_format($order->total, 2) }}</td>
                                                        <td>
                                                            <span class="badge bg-label-{{ 
                                                                    $order->status === 'served' ? 'success' :
                                ($order->status === 'ready' ? 'info' :
                                    ($order->status === 'preparing' ? 'primary' : 'warning')) 
                                                                }}">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucune commande récente</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>