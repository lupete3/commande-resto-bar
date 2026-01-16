<?php

namespace App\Livewire;

use App\Models\Establishment;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class Home extends Component
{
    public $search = '';

    public function render()
    {
        $establishments = Establishment::active()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->get();

        return view('livewire.home', [
            'establishments' => $establishments
        ]);
    }
}