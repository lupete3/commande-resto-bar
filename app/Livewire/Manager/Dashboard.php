<?php

namespace App\Livewire\Manager;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app', ['title' => 'Dashboard Gérant'])]
class Dashboard extends Component
{
    public $stats = [];
    public $recentOrders = [];

    public function mount()
    {
        $user = Auth::user();
        $establishmentId = $user->establishment_id;

        if (!$establishmentId) {
            return;
        }

        // Stats
        $this->stats = [
            'total_orders' => Order::where('establishment_id', $establishmentId)->count(),
            'orders_today' => Order::where('establishment_id', $establishmentId)
                ->whereDate('created_at', today())
                ->count(),
            'revenue_today' => Order::where('establishment_id', $establishmentId)
                ->whereDate('created_at', today())
                ->where('status', 'served')
                ->sum('total'),
            'active_tables' => Table::where('establishment_id', $establishmentId)
                ->whereHas('sessions', function ($query) {
                    $query->where('is_active', true);
                })
                ->count(),
            'total_tables' => Table::where('establishment_id', $establishmentId)->count(),
            'total_servers' => User::role('server')
                ->where('establishment_id', $establishmentId)
                ->count(),
            'total_menu_items' => MenuItem::where('establishment_id', $establishmentId)->count(),
        ];

        // Recent Orders
        $this->recentOrders = Order::where('establishment_id', $establishmentId)
            ->with(['table', 'server'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.manager.dashboard');
    }
}
