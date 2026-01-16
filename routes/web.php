<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\SuperAdmin;

Route::get('/', \App\Livewire\Home::class)->name('home');

Route::view('dashboard', 'dashboard')
  ->middleware(['auth', 'verified'])
  ->name('dashboard');

Route::middleware(['auth'])->group(function () {
  Route::redirect('settings', 'settings/profile');

  Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
  Volt::route('settings/password', 'settings.password')->name('settings.password');
});

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])
  ->prefix('super-admin')
  ->name('super-admin.')
  ->group(function () {
    Route::get('/dashboard', SuperAdmin\Dashboard::class)->name('dashboard');
    Route::get('/establishments', SuperAdmin\EstablishmentManagement::class)->name('establishments');
    Route::get('/subscriptions', SuperAdmin\SubscriptionManagement::class)->name('subscriptions');
  });

// Manager Routes (à implémenter)
Route::middleware(['auth', 'role:manager'])
  ->prefix('manager')
  ->name('manager.')
  ->group(function () {
    Route::get('/dashboard', \App\Livewire\Manager\Dashboard::class)->name('dashboard');
    Route::get('/staff', \App\Livewire\Manager\StaffManagement::class)->name('staff');
    Route::get('/categories', \App\Livewire\Manager\CategoryManagement::class)->name('categories');
    Route::get('/menu', \App\Livewire\Manager\MenuItemManagement::class)->name('menu');
    Route::get('/tables', \App\Livewire\Manager\TableManagement::class)->name('tables');
  });

// Server Routes (à implémenter)
Route::middleware(['auth', 'role:server'])
  ->prefix('server')
  ->name('server.')
  ->group(function () {
    Route::get('/dashboard', \App\Livewire\Server\Dashboard::class)->name('dashboard');
    Route::get('/tables', \App\Livewire\Server\TableManagement::class)->name('tables');
  });

// Client / Public Routes
Route::get('/b/{slug}', \App\Livewire\Client\Menu::class)->name('client.menu');
Route::get('/order/{order}/status', \App\Livewire\Client\OrderStatus::class)->name('client.order-status');

require __DIR__ . '/auth.php';

