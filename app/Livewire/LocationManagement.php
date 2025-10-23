<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Location;

class LocationManagement extends Component
{
    use WithPagination;

    public $editMode = false;
    public $locationId;

    public $location_name, $address, $is_active = true;

    public $location_id_to_delete;
    public $confirmingLocationDeletion = false;
    public $confirmingLocationSave = false;

    protected function rules()
    {
        return [
            'location_name' => 'required|unique:locations,location_name,' . $this->locationId . ',location_id',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', 'location-form-modal');
    }

    public function confirmLocationSave()
    {
        $this->validate();
        $this->confirmingLocationSave = true;
        $this->dispatch('open-modal', 'confirm-location-save');
    }

    public function store()
    {
        Location::create([
            'location_name' => $this->location_name,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Lokasi berhasil ditambahkan.');
        $this->closeModalAndReset();
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        $this->locationId = $id;
        $this->location_name = $location->location_name;
        $this->address = $location->address;
        $this->is_active = $location->is_active;
        $this->editMode = true;
        $this->dispatch('open-modal', 'location-form-modal');
    }

    public function update()
    {
        $location = Location::findOrFail($this->locationId);
        $location->update([
            'location_name' => $this->location_name,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Lokasi berhasil diupdate.');
        $this->closeModalAndReset();
    }

    public function confirmLocationDeletion($id)
    {
        $this->location_id_to_delete = $id;
        $this->confirmingLocationDeletion = true;
        $this->dispatch('open-modal', 'confirm-location-deletion');
    }

    public function deleteLocation()
    {
        Location::find($this->location_id_to_delete)->delete();
        session()->flash('message', 'Lokasi berhasil dihapus.');
        $this->confirmingLocationDeletion = false;
        $this->dispatch('close-modal', 'confirm-location-deletion');
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->dispatch('close-modal', 'location-form-modal');
    }

    private function closeModalAndReset()
    {
        $this->dispatch('close-modal', 'location-form-modal');
        $this->dispatch('close-modal', 'confirm-location-save');
        $this->resetInputFields();
        $this->confirmingLocationSave = false;
    }

    private function resetInputFields()
    {
        $this->locationId = null;
        $this->location_name = '';
        $this->address = '';
        $this->is_active = true;
        $this->editMode = false;
    }

    public function render()
    {
        $locations = Location::paginate(10);
        return view('livewire.location-management', ['locations' => $locations]);
    }
}
