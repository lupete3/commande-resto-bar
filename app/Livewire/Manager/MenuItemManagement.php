<?php

namespace App\Livewire\Manager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app', ['title' => 'Gestion de la Carte'])]
class MenuItemManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterCategory = '';
    public $showModal = false;
    public $editMode = false;
    public $itemId;

    public $name, $description, $price, $image, $existingImage, $category_id, $is_available = true, $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|max:1024',
        'category_id' => 'required|exists:menu_categories,id',
        'is_available' => 'boolean',
        'sort_order' => 'required|integer|min:0',
    ];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'description', 'price', 'image', 'existingImage', 'category_id', 'sort_order', 'itemId', 'editMode']);
        $this->is_available = true;
        $this->showModal = true;
    }

    public function openEditModal(MenuItem $item)
    {
        $this->resetValidation();
        $this->itemId = $item->id;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->price = $item->price;
        $this->category_id = $item->category_id;
        $this->is_available = $item->is_available;
        $this->sort_order = $item->sort_order;
        $this->existingImage = $item->image_path;
        $this->image = null;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        $establishmentId = Auth::user()->establishment_id;
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'is_available' => $this->is_available,
            'sort_order' => $this->sort_order,
            'establishment_id' => $establishmentId,
        ];

        if ($this->image) {
            $data['image_path'] = $this->image->store('menu-items', 'public');
            if ($this->editMode && $this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
        }

        if ($this->editMode) {
            MenuItem::find($this->itemId)->update($data);
            session()->flash('message', 'Article mis à jour.');
        } else {
            MenuItem::create($data);
            session()->flash('message', 'Article créé.');
        }

        $this->closeModal();
    }

    public function delete(MenuItem $item)
    {
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();
        session()->flash('message', 'Article supprimé.');
    }

    public function toggleAvailability(MenuItem $item)
    {
        $item->update(['is_available' => !$item->is_available]);
    }

    public function render()
    {
        $establishmentId = Auth::user()->establishment_id;
        $categories = MenuCategory::where('establishment_id', $establishmentId)->get();

        $items = MenuItem::where('establishment_id', $establishmentId)
            ->with('category')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->filterCategory, fn($q) => $q->where('category_id', $this->filterCategory))
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->paginate(12);

        return view('livewire.manager.menu-item-management', [
            'items' => $items,
            'categories' => $categories
        ]);
    }
}
