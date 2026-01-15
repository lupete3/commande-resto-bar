<?php

namespace App\Livewire\Manager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

#[Layout('components.layouts.app', ['title' => 'Gestion du Personnel'])]
class StaffManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $userId;

    public $name, $email, $phone, $password, $password_confirmation;
    public $is_active = true;

    protected $listeners = ['refresh' => '$refresh'];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'phone' => 'nullable|string|max:20',
            'password' => $this->editMode ? 'nullable|confirmed|min:8' : 'required|confirmed|min:8',
            'is_active' => 'boolean',
        ];
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'phone', 'password', 'password_confirmation', 'userId', 'editMode']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(User $user)
    {
        $this->resetValidation();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->password_confirmation = '';
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
            $user = User::find($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'is_active' => $this->is_active,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            session()->flash('message', 'Serveur mis à jour avec succès.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'establishment_id' => $establishmentId,
                'is_active' => $this->is_active,
            ]);

            $user->assignRole('server');

            session()->flash('message', 'Nouveau serveur créé avec succès.');
        }

        $this->closeModal();
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('message', 'Statut du serveur mis à jour.');
    }

    public function delete(User $user)
    {
        $user->delete();
        session()->flash('message', 'Serveur supprimé avec succès.');
    }

    public function render()
    {
        $establishmentId = Auth::user()->establishment_id;

        $staff = User::role('server')
            ->where('establishment_id', $establishmentId)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.manager.staff-management', [
            'staff' => $staff
        ]);
    }
}
