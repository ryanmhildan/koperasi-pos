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

    public function store()
    {
        $this->validate();

        Unit::create([
            'unit_name' => $this->unit_name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Unit berhasil ditambahkan.');
        $this->closeModal();
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
        $this->validate();

        $unit = Unit::findOrFail($this->unitId);
        $unit->update([
            'unit_name' => $this->unit_name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Unit berhasil diupdate.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Unit::find($id)->delete();
        session()->flash('message', 'Unit berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'unit-form-modal');
        $this->resetInputFields();
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
