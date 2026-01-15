<?php

namespace App\Livewire\Server;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Table;
use App\Models\TableSession;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app', ['title' => 'Salles & Tables'])]
class TableManagement extends Component
{
    public $search = '';

    public function toggleTableStatus(Table $table)
    {
        // Simple toggle or session end logic
        if ($table->isOccupied()) {
            $session = $table->getCurrentSession();
            if ($session) {
                // Check if all orders are served/paid before closing session?
                // For now, allow closing if all orders are served.
                $pendingOrders = $table->orders()->whereIn('status', ['pending', 'preparing', 'ready'])->count();

                if ($pendingOrders > 0) {
                    session()->flash('error', "Impossible de libérer la table {$table->table_number} : il y a des commandes en cours.");
                    return;
                }

                $session->update([
                    'is_active' => false,
                    'closed_at' => now()
                ]);
                session()->flash('message', "Table {$table->table_number} libérée.");
            }
        } else {
            // Start new session
            TableSession::create([
                'table_id' => $table->id,
                'establishment_id' => $table->establishment_id,
                'is_active' => true,
                'started_at' => now()
            ]);
            session()->flash('message', "Table {$table->table_number} occupée.");
        }
    }

    public function render()
    {
        $establishmentId = Auth::user()->establishment_id;

        $tables = Table::where('establishment_id', $establishmentId)
            ->where('is_active', true)
            ->when($this->search, fn($q) => $q->where('table_number', 'like', '%' . $this->search . '%'))
            ->orderBy('location')
            ->orderBy('table_number')
            ->get();

        return view('livewire.server.table-management', [
            'tables' => $tables
        ]);
    }
}
