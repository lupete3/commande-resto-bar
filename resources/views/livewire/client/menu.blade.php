<div class="position-relative overflow-hidden">
    <!-- Header Section with Dynamic Background -->
    <div class="header-bg position-relative shadow-sm">
        <!-- Top Nav -->
        <div class="glass-nav d-flex justify-content-between align-items-center mb-0">
            <div class="d-flex align-items-center">
                @if($establishment->logo)
                    <img src="{{ Storage::url($establishment->logo) }}" class="rounded-circle me-2 shadow-sm"
                        style="width: 32px; height: 32px; object-fit: cover; border: 1.5px solid white;">
                @else
                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 32px; height: 32px; border: 1.5px solid white;">
                        <i class="bx bx-restaurant text-white small"></i>
                    </div>
                @endif
                <span class="fw-800 small text-dark" style="letter-spacing: -0.3px;">{{ $establishment->name }}</span>
            </div>
            <div wire:click="openTableModal" class="cursor-pointer">
                @if($table_number)
                    <span class="badge bg-white text-dark shadow-sm rounded-pill py-2 px-3 border border-light">
                        <span class="status-dot"></span> Table {{ $table_number }}
                    </span>
                @else
                    <span
                        class="badge bg-danger text-white shadow-sm rounded-pill py-2 px-3 animate__animated animate__pulse animate__infinite">
                        <i class="bx bx-qr-scan me-1"></i> Choisir Table
                    </span>
                @endif
            </div>
        </div>

        <!-- Hero Section -->
        <div class="px-4 pt-4 position-relative">
            <h2 class="fw-800 text-white mb-2 animate__animated animate__fadeInLeft"
                style="font-size: 1.8rem; line-height: 1.1; letter-spacing: -1px;">
                Menu <br><span class="text-accent">Dégustation</span>
            </h2>
            <p class="text-white opacity-90 small mb-0" style="font-size: 12px; max-width: 80%;">Découvrez nos
                spécialités préparées avec passion.</p>
        </div>
    </div>

    <!-- Category Pill Navigation (Horizontal Scroll) -->
    <div class="px-4 mt-n3 position-relative" style="z-index: 10;">
        <div class="d-flex overflow-auto no-scrollbar py-2">
            @foreach($categories as $category)
                <a href="#cat-{{ $category->id }}"
                    class="category-pill shadow-sm @if($activeCategory == $category->id) active @endif">
                    @if($category->icon) <i class="bx {{ $category->icon }} me-1"></i> @endif
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Menu Items List -->
    <div class="px-4 pb-5 mb-5 mt-4 position-relative">
        @foreach($categories as $category)
            <div id="cat-{{ $category->id }}" class="mb-5 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center mb-4">
                    <h5 class="fw-800 mb-0 text-dark">{{ $category->name }}</h5>
                    <div class="ms-3 flex-grow-1 border-bottom" style="height:1px; opacity:0.1;"></div>
                </div>

                @foreach($category->menuItems as $item)
                    <div class="menu-item-card d-flex align-items-center animate__animated animate__fadeInUp"
                        style="animation-delay: {{ $loop->index * 0.05 }}s">
                        <!-- Image Section -->
                        <div class="me-3 position-relative">
                            @if($item->image_path)
                                <img src="{{ Storage::url($item->image_path) }}"
                                    class="menu-item-img shadow-sm border border-white">
                            @else
                                <div class="menu-item-img d-flex align-items-center justify-content-center bg-light">
                                    <i class="bx bx-dish text-muted fs-1 opacity-25"></i>
                                </div>
                            @endif
                            @if(isset($cart[$item->id]))
                                <span
                                    class="item-quantity-badge animate__animated animate__bounceIn">{{ $cart[$item->id]['quantity'] }}</span>
                            @endif
                        </div>

                        <!-- Details Section -->
                        <div class="flex-grow-1" style="min-width: 0;">
                            <h6 class="fw-700 mb-1 text-dark text-truncate">{{ $item->name }}</h6>
                            <p class="text-muted small mb-2 line-clamp-2"
                                style="font-size: 11px; line-height: 1.4; height: 30px;">
                                {{ $item->description }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span
                                    class="price-text text-nowrap">{{ $establishment->currency ?? '$' }}{{ number_format($item->price, 2) }}</span>
                                <div class="d-flex align-items-center">
                                    @if(isset($cart[$item->id]))
                                        <div class="d-flex align-items-center bg-light rounded-pill p-1">
                                            <button wire:click.stop="removeFromCart({{ $item->id }})" class="btn-add"
                                                style="width: 28px; height: 28px;">
                                                <i class="bx bx-minus small"></i>
                                            </button>
                                            <span class="mx-2 fw-bold small">{{ $cart[$item->id]['quantity'] }}</span>
                                            <button wire:click.stop="addToCart({{ $item->id }})"
                                                class="btn-add bg-primary text-white shadow-sm" style="width: 28px; height: 28px;">
                                                <i class="bx bx-plus small"></i>
                                            </button>
                                        </div>
                                    @else
                                        <button wire:click.stop="addToCart({{ $item->id }})"
                                            class="btn btn-primary rounded-pill px-3 py-1 fw-bold border-0 text-nowrap"
                                            style="font-size: 12px;">
                                            <i class="bx bx-plus-circle"></i> Ajouter
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Overlay if loading (Fixed CSS conflict) -->
    <div wire:loading wire:target="placeOrder"
        class="fixed-top w-100 h-100 d-none align-items-center justify-content-center" wire:loading.class="d-flex"
        style="background: rgba(255,255,255,0.7); z-index: 9999; backdrop-filter: blur(4px);">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <h6 class="fw-bold text-dark">Envoi de votre commande...</h6>
        </div>
    </div>

    <!-- Floating CART / ORDER Button -->
    @if($this->cartCount > 0)
        <a href="javascript:void(0);" wire:click="placeOrder" wire:loading.attr="disabled"
            class="floating-action-button animate__animated animate__slideInUp">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center border border-white"
                    style="width: 48px; height: 48px;">
                    <i class="bx bx-shopping-bag text-white fs-4"></i>
                </div>
                <div>
                    <span class="d-block fw-800" style="font-size: 1.1rem;">{{ $this->cartCount }} Articles</span>
                    <small class="text-white-50">Confirmer la commande</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <small class="d-block opacity-50 text-uppercase fw-bold" style="font-size: 10px;">Total</small>
                    <span
                        class="fw-800 fs-4">{{ $establishment->currency ?? '$' }}{{ number_format($this->cartTotal, 2) }}</span>
                </div>
                <i class="bx bx-right-arrow-alt fs-2 animate__animated animate__headShake animate__infinite"></i>
            </div>
        </a>
    @endif

    <!-- Table Selection Modal -->
    @if($showTableModal)
        <div class="modal fade show d-block" tabindex="-1"
            style="background-color: rgba(0,0,0,0.7); backdrop-filter: blur(10px); z-index: 3000;">
            <div class="modal-dialog modal-dialog-centered mx-auto px-3" style="max-width: 450px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h4 class="fw-800 mb-0">Où êtes-vous ?</h4>
                        <button type="button" class="btn-close shadow-none"
                            wire:click="$set('showTableModal', false)"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-4">Veuillez sélectionner votre numéro de table pour que nous
                            puissions
                            vous servir.</p>

                        <div class="input-group mb-4 bg-light rounded-pill px-3 py-1 border border-light">
                            <span class="input-group-text border-0 bg-transparent ps-0"><i
                                    class="bx bx-search opacity-50"></i></span>
                            <input type="text" wire:model.live="tableSearch"
                                class="form-control border-0 bg-transparent shadow-none small"
                                placeholder="Rechercher ma table...">
                        </div>

                        <div class="row g-3" style="max-height: 350px; overflow-y: auto;">
                            @forelse($tables as $table)
                                @php $occupied = $table->isOccupied(); @endphp
                                <div class="col-4 text-center">
                                    <div wire:click="selectTable({{ $table->id }})"
                                        class="p-3 rounded-4 transition-all border @if($occupied) bg-light-subtle @if($table_id == $table->id) border-primary border-2 @else border-light @endif @elseif($table_id == $table->id) bg-primary text-white border-primary shadow-lg scale-110 @else bg-light border-light cursor-pointer @endif"
                                        style="transition: all 0.2s ease;">
                                        <i
                                            class="bx @if($occupied) bx-user-check @else bx-table @endif fs-3 mb-1 @if($table_id == $table->id && !$occupied) text-white @elseif($occupied) text-primary @endif"></i>
                                        <div class="fw-bold small @if($table_id == $table->id && !$occupied) text-white @endif">
                                            T.{{ $table->table_number }}</div>
                                        @if($occupied)
                                            <span
                                                class="badge @if($table_id == $table->id) bg-primary @else bg-label-primary @endif rounded-pill"
                                                style="font-size: 7px; padding: 2px 4px;">Active</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="bx bx-search-alt fs-1 opacity-25 d-block mb-3"></i>
                                    Aucune table trouvée
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert / Toast for Notifications -->
    @if (session()->has('message') || session()->has('error'))
        <div class="position-fixed top-0 start-50 translate-middle-x mt-4 animate__animated animate__fadeInDown"
            style="z-index: 4000; width: 90%; max-width: 400px;">
            <div
                class="alert @if(session()->has('error')) alert-danger @else alert-success @endif shadow-lg rounded-4 p-3 border-0 d-flex align-items-center">
                <i class="bx @if(session()->has('error')) bx-error-circle @else bx-check-circle @endif fs-4 me-3"></i>
                <div class="fw-bold small">{{ session('message') ?? session('error') }}</div>
                <button type="button" class="btn-close ms-auto small shadow-none" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
</div>