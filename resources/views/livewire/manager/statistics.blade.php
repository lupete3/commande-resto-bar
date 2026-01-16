<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Statistiques & Performance</h4>
        <div class="d-flex gap-2">
            <select class="form-select w-auto shadow-sm" wire:model.live="period">
                <option value="today">Aujourd'hui</option>
                <option value="this_week">Cette Semaine</option>
                <option value="this_month">Ce Mois-ci</option>
                <option value="all_time">Depuis le début</option>
            </select>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card bg-primary text-white shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Chiffre d'Affaire</p>
                            <h2 class="fw-800 mb-0">${{ number_format($revenue->total_revenue ?? 0, 2) }}</h2>
                        </div>
                        <div class="avatar  rounded p-2">
                            <i class="bx bx-dollar fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card bg-info text-white shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Commandes Servies</p>
                            <h2 class="fw-800 mb-0">{{ number_format($revenue->total_orders ?? 0) }}</h2>
                        </div>
                        <div class="avatar  rounded p-2">
                            <i class="bx bx-receipt fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card bg-dark text-white shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Articles Vendus</p>
                            <h2 class="fw-800 mb-0 text-white">{{ $popularItems->sum('total_quantity') }}</h2>
                        </div>
                        <div class="avatar  rounded p-2">
                            <i class="bx bx-package fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Server Performance Ranking -->
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Performance des Serveurs</h5>
                    <i class="bx bx-trophy text-warning fs-3"></i>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Serveur</th>
                                    <th class="text-center">Commandes</th>
                                    <th class="text-end pe-4">Revenu Généré</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serverPerformance as $server)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($server->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $server->name }}</h6>
                                                    <small class="text-muted">ID: #{{ $server->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info rounded-pill px-3">{{ $server->served_orders_count }}</span>
                                        </td>
                                        <td class="text-end pe-4 font-monospace fw-bold">
                                            ${{ number_format($server->served_orders_sum_total ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            Aucune performance enregistrée pour cette période.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Items -->
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Top 5 Articles</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush mt-2">
                        @forelse($popularItems as $item)
                            <div class="list-group-item px-0 border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-600 text-dark">{{ $item->name }}</span>
                                    <span class="small fw-800 text-primary">${{ number_format($item->total_sales, 2) }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-3" style="height: 6px; border-radius: 10px;">
                                        @php 
                                            $maxQty = $popularItems->first()->total_quantity ?? 1;
                                            $percent = ($item->total_quantity / $maxQty) * 100;
                                        @endphp
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="small text-muted" style="min-width: 45px;">{{ $item->total_quantity }} vendus</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                Pas encore de données de vente.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
