<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Establishment;
use App\Models\Subscription;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app', ['title' => 'Dashboard Super Admin'])]
class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_establishments' => Establishment::count(),
            'active_establishments' => Establishment::where('is_active', true)->count(),
            'inactive_establishments' => Establishment::where('is_active', false)->count(),
            'total_subscriptions' => Subscription::where('status', 'active')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')
                ->where('billing_cycle', 'monthly')
                ->sum('price'),
            'total_orders' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
        ];
    }

    public function render()
    {
        $recentEstablishments = Establishment::with('subscription')
            ->latest()
            ->take(5)
            ->get();

        $subscriptionsByPlan = Subscription::select('plan', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('plan')
            ->get();

        return view('livewire.super-admin.dashboard', [
            'recentEstablishments' => $recentEstablishments,
            'subscriptionsByPlan' => $subscriptionsByPlan,
        ]);
    }
}
