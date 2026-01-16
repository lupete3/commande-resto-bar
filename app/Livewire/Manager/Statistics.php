<?php

namespace App\Livewire\Manager;

use App\Models\Order;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app', ['title' => 'Statistiques & Performance'])]
class Statistics extends Component
{
    public $period = 'this_month'; // today, this_week, this_month, all_time

    public function render()
    {
        $establishmentId = Auth::user()->establishment_id;
        $dateRange = $this->getDateRange();

        // Revenue Stats
        $revenueStats = Order::where('establishment_id', $establishmentId)
            ->where('status', 'served')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('count(*) as total_orders'),
                DB::raw('sum(total) as total_revenue')
            )->first();

        // Server Performance
        $serverPerformance = User::role('server')
            ->where('establishment_id', $establishmentId)
            ->withCount([
                'servedOrders' => function ($query) use ($dateRange) {
                    $query->where('status', 'served')
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                }
            ])
            ->withSum([
                'servedOrders' => function ($query) use ($dateRange) {
                    $query->where('status', 'served')
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                }
            ], 'total')
            ->orderByDesc('served_orders_count')
            ->get();

        // Popular Items
        $popularItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.establishment_id', $establishmentId)
            ->where('orders.status', 'served')
            ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->select(
                'menu_items.name',
                DB::raw('sum(order_items.quantity) as total_quantity'),
                DB::raw('sum(order_items.subtotal) as total_sales')
            )
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return view('livewire.manager.statistics', [
            'revenue' => $revenueStats,
            'serverPerformance' => $serverPerformance,
            'popularItems' => $popularItems
        ]);
    }

    private function getDateRange()
    {
        $now = now();
        return match ($this->period) {
            'today' => [
                'start' => $now->startOfDay(),
                'end' => $now->copy()->endOfDay()
            ],
            'this_week' => [
                'start' => $now->startOfWeek(),
                'end' => $now->copy()->endOfWeek()
            ],
            'this_month' => [
                'start' => $now->startOfMonth(),
                'end' => $now->copy()->endOfMonth()
            ],
            'all_time' => [
                'start' => now()->subYears(10),
                'end' => $now->copy()->endOfDay()
            ],
            default => [
                'start' => $now->startOfMonth(),
                'end' => $now->copy()->endOfMonth()
            ]
        };
    }
}
