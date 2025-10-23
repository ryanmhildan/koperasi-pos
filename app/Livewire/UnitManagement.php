<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Unit;

class UnitManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $unitId;

    public $unit_name, $description;

    public $unit_id_to_delete;
    public $confirmingUnitDeletion = false;
    public $confirmingUnitSave = false;

    protected function rules()
    {
        return [
            'unit_name' => 'required|unique:units,unit_name,' . $this->unitId . ',unit_id',
            'description' => 'nullable|string',
        ];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', 'unit-form-modal');
    }

    public function confirmUnitSave()
    {
        $this->validate();
        $this->confirmingUnitSave = true;
        $this->dispatch('open-modal', 'confirm-unit-save');
    }

    public function store()
    {
        Unit::create([
            'unit_name' => $this->unit_name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Unit berhasil ditambahkan.');
        $this->closeModalAndReset();
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->unitId = $id;
        $this->unit_name = $unit->unit_name;
        $this->description = $unit->description;
        $this->editMode = true;
        $this->dispatch('open-modal', 'unit-form-modal');
    }

    public function update()
    {
        $unit = Unit::findOrFail($this->unitId);
        $unit->update([
            'unit_name' => $this->unit_name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Unit berhasil diupdate.');
        $this->closeModalAndReset();
    }

    public function confirmUnitDeletion($id)
    {
        $this->unit_id_to_delete = $id;
        $this->confirmingUnitDeletion = true;
        $this->dispatch('open-modal', 'confirm-unit-deletion');
    }

    public function deleteUnit()
    {
        Unit::find($this->unit_id_to_delete)->delete();
        session()->flash('message', 'Unit berhasil dihapus.');
        $this->confirmingUnitDeletion = false;
        $this->dispatch('close-modal', 'confirm-unit-deletion');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'unit-form-modal');
        $this->resetInputFields();
    }

    private function closeModalAndReset()
    {
        $this->dispatch('close-modal', 'unit-form-modal');
        $this->dispatch('close-modal', 'confirm-unit-save');
        $this->resetInputFields();
        $this->confirmingUnitSave = false;
    }

    private function resetInputFields()
    {
        $this->unitId = null;
        $this->unit_name = '';
        $this->description = '';
        $this->editMode = false;
    }

    public function render()
    {
        $units = Unit::paginate(10);
        return view('livewire.unit-management', ['units' => $units]);
    }
}
