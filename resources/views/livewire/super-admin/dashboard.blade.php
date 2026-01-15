<div>
    <div class="row g-4">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Établissements Actifs</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['active_establishments'] ?? 0 }}</h4>
                                <small class="text-muted">/ {{ $stats['total_establishments'] ?? 0 }}</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-building bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Abonnements Actifs</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['total_subscriptions'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-credit-card bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Revenus Mensuels</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <span class="badge bg-label-info rounded p-2">
                            <i class="bx bx-dollar bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Commandes (Aujourd'hui)</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['orders_today'] ?? 0 }}</h4>
                                <small class="text-muted">/ {{ $stats['total_orders'] ?? 0 }}</small>
                            </div>
                        </div>
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="bx bx-cart bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Establishments -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Établissements Récents</h5>
                    <a href="{{ route('super-admin.establishments') }}" class="btn btn-sm btn-primary" wire:navigate>
                        Voir tous
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th>Abonnement</th>
                                <th>Status</th>
                                <th>Créé le</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEstablishments as $establishment)
                                <tr>
                                    <td>
                                        <strong>{{ $establishment->name }}</strong><br>
                                        <small class="text-muted">{{ $establishment->slug }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ ucfirst($establishment->type) }}</span>
                                    </td>
                                    <td>{{ $establishment->email }}</td>
                                    <td>
                                        @if($establishment->subscription)
                                            <span
                                                class="badge bg-label-{{ $establishment->subscription->status === 'active' ? 'success' : 'warning' }}">
                                                {{ ucfirst($establishment->subscription->plan) }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary">Aucun</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($establishment->is_active)
                                            <span class="badge bg-label-success">Actif</span>
                                        @else
                                            <span class="badge bg-label-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>{{ $establishment->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucun établissement</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Subscriptions by Plan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Abonnements par Plan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($subscriptionsByPlan as $sub)
                            <li class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-label-primary me-2">{{ ucfirst($sub->plan) }}</span>
                                </div>
                                <h5 class="mb-0">{{ $sub->total }}</h5>
                            </li>
                        @empty
                            <li class="text-center text-muted">Aucun abonnement actif</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>