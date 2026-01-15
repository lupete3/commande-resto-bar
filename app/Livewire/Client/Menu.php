<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Establishment;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\TableSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.guest')]
#[Title('Menu')]
class Menu extends Component
{
    public Establishment $establishment;
    public $table_number;
    public $table_id;
    public $cart = [];
    public $activeCategory = null;
    public $showTableModal = false;
    public $tableSearch = '';

    public function selectTable($id)
    {
        $table = Table::find($id);
        if ($table) {
            $this->table_id = $table->id;
            $this->table_number = $table->table_number;
            $this->showTableModal = false;
            session()->flash('message', "Table {$table->table_number} sélectionnée.");
        }
    }

    public function openTableModal()
    {
        $this->showTableModal = true;
    }

    public function mount($slug)
    {
        $this->establishment = Establishment::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->table_number = request()->query('table');
        if ($this->table_number) {
            $table = Table::where('establishment_id', $this->establishment->id)
                ->where('table_number', $this->table_number)
                ->first();
            $this->table_id = $table?->id;
        }

        $this->cart = Session::get("cart_{$this->establishment->id}", []);
    }

    public function addToCart($itemId)
    {
        $item = MenuItem::findOrFail($itemId);

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity']++;
        } else {
            $this->cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
                'image' => $item->image_path
            ];
        }

        $this->saveCart();
    }

    public function removeFromCart($itemId)
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity']--;
            if ($this->cart[$itemId]['quantity'] <= 0) {
                unset($this->cart[$itemId]);
            }
        }
        $this->saveCart();
    }

    private function saveCart()
    {
        Session::put("cart_{$this->establishment->id}", $this->cart);
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function placeOrder()
    {
        if (empty($this->cart))
            return;
        if (!$this->table_id) {
            session()->flash('error', 'Veuillez sélectionner votre numéro de table avant de commander.');
            $this->showTableModal = true;
            return;
        }

        $order = DB::transaction(function () {
            // Ensure an active session exists for this table
            $session = TableSession::firstOrCreate(
                [
                    'table_id' => $this->table_id,
                    'establishment_id' => $this->establishment->id,
                    'is_active' => true
                ],
                [
                    'started_at' => now(),
                    'session_token' => \Illuminate\Support\Str::uuid()->toString()
                ]
            );

            $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $tax = 0;

            $order = Order::create([
                'establishment_id' => $this->establishment->id,
                'table_id' => $this->table_id,
                'table_session_id' => $session->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Clear cart
            $this->cart = [];
            $this->saveCart();

            return $order;
        });

        session()->flash('message', 'Votre commande a été envoyée avec succès ! Bon appétit.');
        return redirect()->route('client.order-status', ['order' => $order->id]);
    }

    public function render()
    {
        $categories = MenuCategory::where('establishment_id', $this->establishment->id)
            ->where('is_active', true)
            ->with(['menuItems' => fn($q) => $q->where('is_available', true)])
            ->orderBy('sort_order')
            ->get();

        $tables = Table::where('establishment_id', $this->establishment->id)
            ->where('is_active', true)
            ->when($this->tableSearch, fn($q) => $q->where('table_number', 'like', '%' . $this->tableSearch . '%'))
            ->get();

        return view('livewire.client.menu', [
            'categories' => $categories,
            'tables' => $tables
        ]);
    }
}
