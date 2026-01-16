<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Establishment;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app', ['title' => 'Gestion des Établissements'])]
class EstablishmentManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    public $showModal = false;
    public $editMode = false;
    public $establishmentId;

    // Form fields
    public $name;
    public $slug;
    public $type = 'bar';
    public $address;
    public $phone;
    public $currency = '$';
    public $email;
    public $description;
    public $logo;
    public $max_tables = 50;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:establishments,slug',
        'type' => 'required|in:bar,restaurant,hotel',
        'address' => 'required|string',
        'phone' => 'required|string',
        'currency' => 'required|string|max:10',
        'email' => 'required|email',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|max:500',
        'max_tables' => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function updatedName()
    {
        if (!$this->editMode) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function openEditModal($id)
    {
        $establishment = Establishment::findOrFail($id);

        $this->establishmentId = $establishment->id;
        $this->name = $establishment->name;
        $this->slug = $establishment->slug;
        $this->type = $establishment->type;
        $this->address = $establishment->address;
        $this->phone = $establishment->phone;
        $this->currency = $establishment->currency ?? '$';
        $this->email = $establishment->email;
        $this->description = $establishment->description;
        $this->max_tables = $establishment->max_tables;
        $this->is_active = $establishment->is_active;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editMode) {
            $this->rules['slug'] = 'required|string|max:255|unique:establishments,slug,' . $this->establishmentId;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'address' => $this->address,
            'phone' => $this->phone,
            'currency' => $this->currency,
            'email' => $this->email,
            'description' => $this->description,
            'max_tables' => $this->max_tables,
            'is_active' => $this->is_active,
        ];

        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $data['logo'] = $logoPath;
        }

        if ($this->editMode) {
            $establishment = Establishment::findOrFail($this->establishmentId);

            // Delete old logo if new one uploaded
            if ($this->logo && $establishment->logo) {
                Storage::disk('public')->delete($establishment->logo);
            }

            $establishment->update($data);
            session()->flash('message', 'Établissement mis à jour avec succès!');
        } else {
            $data['subscription_status'] = 'trial';
            $data['subscription_ends_at'] = now()->addDays(30);

            Establishment::create($data);
            session()->flash('message', 'Établissement créé avec succès!');
        }

        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $establishment = Establishment::findOrFail($id);
        $establishment->update(['is_active' => !$establishment->is_active]);

        session()->flash('message', 'Statut de l\'établissement mis à jour!');
    }

    public function delete($id)
    {
        $establishment = Establishment::findOrFail($id);

        if ($establishment->logo) {
            Storage::disk('public')->delete($establishment->logo);
        }

        $establishment->delete();
        session()->flash('message', 'Établissement supprimé avec succès!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'establishmentId',
            'name',
            'slug',
            'type',
            'address',
            'phone',
            'currency',
            'email',
            'description',
            'logo',
            'max_tables',
            'is_active'
        ]);
        $this->type = 'bar';
        $this->currency = '$';
        $this->max_tables = 50;
        $this->is_active = true;
    }

    public function render()
    {
        $query = Establishment::query()->with('subscription');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $establishments = $query->latest()->paginate(10);

        return view('livewire.super-admin.establishment-management', [
            'establishments' => $establishments,
        ]);
    }
}
