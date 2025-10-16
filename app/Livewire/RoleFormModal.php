<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class RoleFormModal extends Component
{
    public $role_id;
    public $name;
    public $selectedPermissions = [];
    public $allPermissions;

    public function mount()
    {
        $this->allPermissions = Permission::all();
    }

    #[On('createRole')]
    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', 'role-modal');
    }

    #[On('editRole')]
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->dispatch('open-modal', 'role-modal');
    }

    public function store()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->role_id)],
            'selectedPermissions' => 'array'
        ]);

        $role = Role::updateOrCreate(['id' => $this->role_id], ['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        session()->flash('message', $this->role_id ? 'Role Berhasil Diperbarui.' : 'Role Berhasil Dibuat.');

        $this->closeModal();

        // Dispatch an event to tell the RoleManagement table to re-render
        $this->dispatch('roleSaved');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'role-modal');
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->role_id = null;
        $this->name = '';
        $this->selectedPermissions = [];
    }

    public function render()
    {
        return view('livewire.role-form-modal');
    }
}
