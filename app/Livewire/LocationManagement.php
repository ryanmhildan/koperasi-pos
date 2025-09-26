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
    }

    public function store()
    {
        $this->validate();

        Location::create([
            'location_name' => $this->location_name,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Lokasi berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        $this->locationId = $id;
        $this->location_name = $location->location_name;
        $this->address = $location->address;
        $this->is_active = $location->is_active;
        $this->editMode = true;
    }

    public function update()
    {
        $this->validate();

        $location = Location::findOrFail($this->locationId);
        $location->update([
            'location_name' => $this->location_name,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Lokasi berhasil diupdate.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Location::find($id)->delete();
        session()->flash('message', 'Lokasi berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
        $this->dispatch('close-modal', 'location-form-modal');
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
