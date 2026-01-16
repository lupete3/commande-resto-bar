<div>
    <div class="position-relative overflow-hidden">
        <!-- Hero Section with Dynamic Background -->
        <div class="header-bg position-relative shadow-sm" style="min-height: 280px;">
            <div class="px-4 pt-5 pb-4 text-center">
                <h1 class="fw-800 text-white mb-2 animate__animated animate__fadeInDown"
                    style="font-size: 2.5rem; line-height: 1; letter-spacing: -1.5px;">
                    Découvrez <br><span class="text-accent">Nos Établissements</span>
                </h1>
                <p class="text-white opacity-90 small mb-4 animate__animated animate__fadeIn" style="font-size: 14px;">
                    Explorez les meilleurs bars et restaurants autour de vous.
                </p>

                <!-- Search Bar -->
                <div class="mx-auto animate__animated animate__fadeInUp" style="max-width: 400px;">
                    <div class="glass-nav d-flex align-items-center mb-0 shadow-lg" style="margin: 0; padding: 5px 15px;">
                        <i class="bx bx-search text-muted me-2 fs-4"></i>
                        <input type="text" wire:model.live="search" class="form-control border-0 bg-transparent shadow-none"
                            placeholder="Rechercher par nom ou type..." style="height: 45px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Establishment Grid -->
        <div class="px-4 pb-5 mb-5 mt-n4 position-relative" style="z-index: 10;">
            <div class="row g-4">
                @forelse($establishments as $establishment)
                    <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s">
                        <a href="{{ route('client.menu', $establishment->slug) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm overflow-hidden"
                                style="border-radius: 24px; transition: transform 0.3s ease;">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4">
                                        <div class="aspect-ratio-square bg-light d-flex align-items-center justify-content-center"
                                            style="height: 120px;">
                                            @if($establishment->logo)
                                                <img src="{{ Storage::url($establishment->logo) }}"
                                                    class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i
                                                        class="bx {{ $establishment->type == 'bar' ? 'bx-drink' : 'bx-restaurant' }} fs-1"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="fw-800 text-dark mb-0">{{ $establishment->name }}</h6>
                                                <span class="badge bg-light text-primary rounded-pill text-uppercase"
                                                    style="font-size: 10px;">{{ $establishment->type }}</span>
                                            </div>
                                            <p class="text-muted small mb-2 text-truncate" style="font-size: 11px;">
                                                <i class="bx bx-map-pin me-1"></i> {{ $establishment->address }}
                                            </p>
                                            <div class="d-flex align-items-center text-primary fw-bold"
                                                style="font-size: 12px;">
                                                <span>Voir le menu</span>
                                                <i class="bx bx-right-arrow-alt ms-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="bx bx-search-alt fs-1 text-muted opacity-50"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Aucun établissement trouvé</h6>
                        <p class="text-muted small">Essayez une autre recherche.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .aspect-ratio-square img {
            object-fit: cover;
        }

        .card:active {
            transform: scale(0.98);
        }
    </style>
</div>