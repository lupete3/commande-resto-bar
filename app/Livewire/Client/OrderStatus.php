<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;

#[Layout('components.layouts.guest')]
#[Title('Suivi de commande')]
class OrderStatus extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function getStatusStepsProperty()
    {
        return [
            'pending' => ['label' => 'Reçue', 'icon' => 'bx-receipt', 'color' => 'danger'],
            'preparing' => ['label' => 'En cuisine', 'icon' => 'bx-bowl-hot', 'color' => 'primary'],
            'ready' => ['label' => 'Prête', 'icon' => 'bx-bell', 'color' => 'info'],
            'served' => ['label' => 'Servie', 'icon' => 'bx-check-double', 'color' => 'success'],
        ];
    }

    public function render()
    {
        return view('livewire.client.order-status');
    }
}
