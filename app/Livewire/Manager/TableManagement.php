<?php

namespace App\Livewire\Manager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('components.layouts.app', ['title' => 'Gestion des Tables'])]
class TableManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterLocation = '';
    public $showModal = false;
    public $editMode = false;
    public $tableId;

    public $table_number, $capacity = 4, $location;
    public $is_active = true;

    protected $rules = [
        'table_number' => 'required|string|max:50',
        'capacity' => 'required|integer|min:1',
        'location' => 'nullable|string|max:100',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['table_number', 'capacity', 'location', 'tableId', 'editMode']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(Table $table)
    {
        $this->resetValidation();
        $this->tableId = $table->id;
        $this->table_number = $table->table_number;
        $this->capacity = $table->capacity;
        $this->location = $table->location;
        $this->is_active = $table->is_active;
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

        // Check for duplicate table number in same establishment
        $query = Table::where('establishment_id', $establishmentId)
            ->where('table_number', $this->table_number);

        if ($this->editMode) {
            $query->where('id', '!=', $this->tableId);
        }

        if ($query->exists()) {
            $this->addError('table_number', 'Ce numéro de table existe déjà.');
            return;
        }

        if ($this->editMode) {
            Table::find($this->tableId)->update([
                'table_number' => $this->table_number,
                'capacity' => $this->capacity,
                'location' => $this->location,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Table mise à jour.');
        } else {
            Table::create([
                'table_number' => $this->table_number,
                'capacity' => $this->capacity,
                'location' => $this->location,
                'establishment_id' => $establishmentId,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Table créée.');
        }

        $this->closeModal();
    }

    public function delete(Table $table)
    {
        if ($table->isOccupied()) {
            session()->flash('error', 'Impossible de supprimer une table occupée.');
            return;
        }

        $table->delete();
        session()->flash('message', 'Table supprimée.');
    }

    public function generateQrCode(Table $table)
    {
        $url = $table->getTableUrl();
        $fileName = 'qrcodes/table-' . $table->id . '-' . Str::random(5) . '.svg';

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($url);

        Storage::disk('public')->put($fileName, $qrCode);

        // Delete old QR code if exists
        if ($table->qr_code_path) {
            Storage::disk('public')->delete($table->qr_code_path);
        }

        $table->update(['qr_code_path' => $fileName]);
        session()->flash('message', "QR Code généré pour la table {$table->table_number}");
    }

    public function toggleStatus(Table $table)
    {
        $table->update(['is_active' => !$table->is_active]);
    }

    public function render()
    {
        $establishmentId = Auth::user()->establishment_id;

        $tables = Table::where('establishment_id', $establishmentId)
            ->when($this->search, fn($q) => $q->where('table_number', 'like', '%' . $this->search . '%'))
            ->when($this->filterLocation, fn($q) => $q->where('location', $this->filterLocation))
            ->orderBy('location')
            ->orderBy('table_number')
            ->paginate(12);

        $locations = Table::where('establishment_id', $establishmentId)
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location');

        return view('livewire.manager.table-management', [
            'tables' => $tables,
            'locations' => $locations
        ]);
    }
}
