<div class="position-relative overflow-hidden" wire:poll.5s>
    <!-- Header Section with Dynamic Background -->
    <div class="header-bg position-relative shadow-sm">
        <!-- Top Nav -->
        <div class="glass-nav d-flex justify-content-between align-items-center mb-0">
            <a href="{{ route('client.menu', $order->establishment->slug) }}" class="text-dark text-decoration-none">
                <i class="bx bx-chevron-left fs-4"></i>
            </a>
            <span class="fw-bold small">Ma Commande</span>
            <div style="width: 24px;"></div>
        </div>

        <!-- Order Header -->
        <div class="text-center text-white px-4 pt-4 animate__animated animate__fadeIn">
            <h5 class="fw-bold mb-1">Commande #{{ $order->order_number }}</h5>
            <p class="small opacity-75">Suivez l'état de votre commande en direct</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 mt-n3 position-relative" style="z-index: 2;">
        @if (session()->has('message'))
            <div class="alert alert-success shadow-lg rounded-4 p-3 border-0 d-flex align-items-center mb-4 animate__animated animate__bounceIn">
                <i class="bx bx-check-circle fs-4 me-3"></i>
                <div class="fw-bold small">{{ session('message') }}</div>
                <button type="button" class="btn-close ms-auto small shadow-none" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Status Card -->
        <div class="bg-white rounded-4 shadow-lg p-4 mb-4 animate__animated animate__zoomIn">
            <div class="text-center mb-4">
                @php 
                    $statusConfig = [
                        'pending' => ['label' => 'Reçue', 'icon' => 'bx-receipt', 'color' => 'danger', 'desc' => 'Nous préparons votre commande.'],
                        'preparing' => ['label' => 'En Cuisine', 'icon' => 'bx-bowl-hot', 'color' => 'primary', 'desc' => 'Le chef s\'active pour vous !'],
                        'ready' => ['label' => 'Prête', 'icon' => 'bx-bell', 'color' => 'info', 'desc' => 'Votre commande est prête à être servie.'],
                        'served' => ['label' => 'Déjà Servie', 'icon' => 'bx-check-double', 'color' => 'success', 'desc' => 'Bon appétit et à bientôt !'],
                        'cancelled' => ['label' => 'Annulée', 'icon' => 'bx-x', 'color' => 'secondary', 'desc' => 'Désolé, la commande a été annulée.'],
                    ];
                    $current = $statusConfig[$order->status] ?? $statusConfig['pending'];
                @endphp
                
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-4 mb-3" style="width: 100px; height: 100px;">
                    <i class="bx {{ $current['icon'] }} text-{{ $current['color'] }} fs-1 pulse"></i>
                </div>
                <h3 class="fw-800 text-{{ $current['color'] }} mb-2">{{ $current['label'] }}</h3>
                <p class="text-muted small">{{ $current['desc'] }}</p>
            </div>

            <!-- Timeline -->
            <div class="status-steps pt-3">
                @php 
                    $steps = ['pending', 'preparing', 'ready', 'served'];
                    if($order->status == 'cancelled') $steps = ['cancelled'];
                    $currentIndex = array_search($order->status, $steps);
                @endphp

                <div class="d-flex justify-content-between position-relative mb-4">
                    <div class="position-absolute top-50 start-0 end-0 bg-light" style="height: 2px; transform: translateY(-50%); z-index: 1;"></div>
                    @foreach($steps as $index => $step)
                        @php 
                            $stepInfo = $statusConfig[$step];
                            $isActive = $index <= $currentIndex;
                            $isCurrent = $index == $currentIndex;
                        @endphp
                        <div class="text-center position-relative" style="z-index: 2; width: 25%;">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center @if($isActive) bg-{{ $stepInfo['color'] }} text-white shadow @else bg-white text-muted border @endif" style="width: 35px; height: 35px; transition: 0.5s;">
                                <i class="bx {{ $stepInfo['icon'] }} small"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-5 animate__animated animate__fadeInUp">
            <h6 class="fw-bold mb-4 d-flex align-items-center">
                <i class="bx bx-list-check me-2 text-primary"></i> Récapitulatif
            </h6>
            
            <div class="list-group list-group-flush">
                @foreach($order->items as $item)
                    <div class="list-group-item px-0 border-0 d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-3 text-center fw-bold small" style="width: 35px;">
                                {{ $item->quantity }}x
                            </div>
                            <span class="small fw-600 text-dark">{{ $item->menuItem->name ?? 'Article inconnu' }}</span>
                        </div>
                        <span class="small fw-bold text-muted">${{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 mt-3 border-top d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">Total</span>
                <span class="fw-800 fs-5 text-primary">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>
        
        <div class="text-center pb-5 opacity-25">
            <small class="d-flex align-items-center justify-content-center">
                <i class="bx bx-refresh bx-spin me-2"></i> Actualisation en direct
            </small>
        </div>
    </div>
</div>
