<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed" data-base-url="{{url('/')}}"
  data-framework="laravel">

<head>
  @include('partials.head')
</head>

<body>

  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Layout Content -->
      <x-layouts.menu.vertical :title="$title ?? null"></x-layouts.menu.vertical>
      <!--/ Layout Content -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <x-layouts.navbar.default :title="$title ?? null"></x-layouts.navbar.default>
        <!--/ Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            {{ $slot }}
          </div>
          <!-- / Content -->

          <!-- Footer -->
          <x-layouts.footer.default :title="$title ?? null"></x-layouts.footer.default>
          <!--/ Footer -->
          <div class="content-backdrop fade"></div>
        </div>
        <!-- / Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  <!-- Include Scripts -->
  @include('partials.scripts')
  <!-- / Include Scripts -->

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Toggle Menu
      const menuToggles = document.querySelectorAll('.layout-menu-toggle');
      const htmlElement = document.documentElement;

      menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          htmlElement.classList.toggle('layout-menu-expanded');
        });
      });

      // Close menu on navigation (Livewire support)
      document.addEventListener('livewire:navigating', function () {
        htmlElement.classList.remove('layout-menu-expanded');
      });
    });
  </script>
</body>

</html>