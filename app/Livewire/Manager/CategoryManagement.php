<?php

namespace App\Livewire\Manager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app', ['title' => 'Gestion des Catégories'])]
class CategoryManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $categoryId;

    public $name, $description, $icon, $sort_order = 0;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:50',
        'sort_order' => 'required|integer|min:0',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'description', 'icon', 'sort_order', 'categoryId', 'editMode']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(MenuCategory $category)
    {
        $this->resetValidation();
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->icon = $category->icon;
        $this->sort_order = $category->sort_order;
        $this->is_active = $category->is_active;
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

        if ($this->editMode) {
            MenuCategory::find($this->categoryId)->update([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'sort_order' => $this->sort_order,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Catégorie mise à jour.');
        } else {
            MenuCategory::create([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'sort_order' => $this->sort_order,
                'establishment_id' => $establishmentId,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Catégorie créée.');
        }

        $this->closeModal();
    }

    public function delete(MenuCategory $category)
    {
        if ($category->menuItems()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer une catégorie qui contient des articles.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Catégorie supprimée.');
    }

    public function render()
    {
        $categories = MenuCategory::where('establishment_id', Auth::user()->establishment_id)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('sort_order')
            ->paginate(10);

        return view('livewire.manager.category-management', [
            'categories' => $categories
        ]);
    }
}
