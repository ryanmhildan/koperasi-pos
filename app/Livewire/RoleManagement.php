<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleManagement extends Component
{
    public $roles, $permissions;
    public $role_id, $name;
    public $selectedPermissions = [];

    public function render()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();
        return view('livewire.role-management');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', 'role-modal');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'role-modal');
    }

    private function resetInputFields()
    {
        $this->role_id = null;
        $this->name = '';
        $this->selectedPermissions = [];
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
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->dispatch('open-modal', 'role-modal');
    }

    public function delete($id)
    {
        // Prevent deleting core roles if they exist
        $role = Role::findOrFail($id);
        if (in_array($role->name, ['Admin', 'Kasir', 'Anggota Koperasi'])) {
            session()->flash('error', 'Role inti tidak dapat dihapus.');
            return;
        }
        
        $role->delete();
        session()->flash('message', 'Role Berhasil Dihapus.');
    }
}
