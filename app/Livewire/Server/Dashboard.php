<?php

namespace App\Livewire\Server;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app', ['title' => 'Commandes en Cours'])]
class Dashboard extends Component
{
    public $statusFilter = 'all';

    protected $listeners = ['refreshOrders' => '$refresh'];

    public function setFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function updateOrderStatus(Order $order, $newStatus)
    {
        $order->updateStatus($newStatus);
        session()->flash('message', "Commande {$order->order_number} mise à jour en : {$newStatus}");
    }

    public function freeTable($tableId)
    {
        $table = Table::findOrFail($tableId);
        $session = $table->getCurrentSession();

        if ($session) {
            // Check if there are any non-served orders in this session
            $pendingOrders = $session->orders()->whereNotIn('status', ['served', 'cancelled'])->count();

            if ($pendingOrders > 0) {
                session()->flash('error', "La table {$table->table_number} a encore des commandes en préparation.");
                return;
            }

            $session->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);

            session()->flash('message', "Table {$table->table_number} libérée et session clôturée.");
        }
    }

    public function getOccupiedTablesProperty()
    {
        return Table::where('establishment_id', Auth::user()->establishment_id)
            ->whereHas('sessions', fn($q) => $q->where('is_active', true))
            ->with(['sessions' => fn($q) => $q->where('is_active', true)->with('orders')])
            ->get();
    }

    public function getOrdersProperty()
    {
        $establishmentId = Auth::user()->establishment_id;

        $query = Order::where('establishment_id', $establishmentId)
            ->with(['table', 'items.menuItem', 'server', 'tableSession'])
            ->whereIn('status', ['pending', 'preparing', 'ready', 'served'])
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.server.dashboard', [
            'orders' => $this->orders
        ]);
    }
}
