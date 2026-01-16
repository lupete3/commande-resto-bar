<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @auth
      @if (auth()->user()->isSuperAdmin())
        <!-- Super Admin Menu -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Super Admin</span></li>
        
        <li class="menu-item {{ request()->is('super-admin/dashboard*') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('super-admin.dashboard') }}" >
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div class="text-truncate">Dashboard</div>
          </a>
        </li>

        <li class="menu-item {{ request()->is('super-admin/establishments*') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('super-admin.establishments') }}" >
            <i class="menu-icon tf-icons bx bx-building"></i>
            <div class="text-truncate">Établissements</div>
          </a>
        </li>

        <li class="menu-item {{ request()->is('super-admin/subscriptions*') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('super-admin.subscriptions') }}" >
            <i class="menu-icon tf-icons bx bx-credit-card"></i>
            <div class="text-truncate">Abonnements</div>
          </a>
        </li>

      @elseif (auth()->user()->isManager())
        @role('manager')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Gestion Établissement</span>
            </li>
            <li class="menu-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                <a href="{{ route('manager.dashboard') }}" class="menu-link" >
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('manager.staff') ? 'active' : '' }}">
                <a href="{{ route('manager.staff') }}" class="menu-link" >
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <div>Équipe (Serveurs)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('manager.menu') || request()->routeIs('manager.categories') ? 'active' : '' }}">
                <a href="{{ route('manager.menu') }}" class="menu-link" >
                    <i class="menu-icon tf-icons bx bx-food-menu"></i>
                    <div>Carte (Menu)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('manager.tables') ? 'active' : '' }}">
                <a href="{{ route('manager.tables') }}" class="menu-link" >
                    <i class="menu-icon tf-icons bx bx-table"></i>
                    <div>Salles & Tables</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('manager.statistics') ? 'active' : '' }}">
                <a href="{{ route('manager.statistics') }}" class="menu-link" >
                    <i class="menu-icon tf-icons bx bx-line-chart"></i>
                    <div>Statistiques</div>
                </a>
            </li>
        @endrole

      @elseif (auth()->user()->isServer())
        <!-- Server Menu -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Service</span></li>
        
        <li class="menu-item {{ request()->routeIs('server.dashboard') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('server.dashboard') }}" >
            <i class="menu-icon tf-icons bx bx-list-check"></i>
            <div class="text-truncate">Commandes</div>
          </a>
        </li>

        <li class="menu-item {{ request()->routeIs('server.tables') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('server.tables') }}" >
            <i class="menu-icon tf-icons bx bx-table"></i>
            <div class="text-truncate">Tables</div>
          </a>
        </li>

      @else
        <!-- Default Dashboard --  >
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('dashboard') }}" >
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div class="text-truncate">{{ __('Dashboard') }}</div>
          </a>
        </li>
      @endif

      <!-- Divider -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Account') }}</span></li>

      <!-- Settings -->
      <li class="menu-item {{ request()->is('settings/*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-cog"></i>
          <div class="text-truncate">{{ __('Settings') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.profile') }}" >{{ __('Profile') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.password') }}" >{{ __('Password') }}</a>
          </li>
        </ul>
      </li>
    @endauth
  </ul>
</aside>
<!-- / Menu -->

<script>
  // Toggle the 'open' class when the menu-toggle is clicked
  document.querySelectorAll('.menu-toggle').forEach(function (menuToggle) {
    menuToggle.addEventListener('click', function () {
      const menuItem = menuToggle.closest('.menu-item');
      // Toggle the 'open' class on the clicked menu-item
      menuItem.classList.toggle('open');
    });
  });
</script>