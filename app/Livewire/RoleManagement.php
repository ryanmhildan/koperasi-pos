<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RoleManagement extends Component
{
    public $roles;

    #[On('roleSaved')]
    public function render()
    {
        $this->roles = Role::with('permissions')->get();
        return view('livewire.role-management');
    }

    public function create()
    {
        $this->dispatch('createRole');
    }

    public function edit($id)
    {
        $this->dispatch('editRole', id: $id);
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