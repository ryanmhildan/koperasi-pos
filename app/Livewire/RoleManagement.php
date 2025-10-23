<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RoleManagement extends Component
{
    use WithPagination;

    public $role_id_to_delete;
    public $confirmingRoleDeletion = false;

    #[On('roleSaved')]
    public function render()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('livewire.role-management', ['roles' => $roles]);
    }

    public function create()
    {
        $this->dispatch('createRole');
    }

    public function edit($id)
    {
        $this->dispatch('editRole', id: $id);
    }

    public function confirmRoleDeletion($id)
    {
        $this->role_id_to_delete = $id;
        $this->confirmingRoleDeletion = true;
        $this->dispatch('open-modal', 'confirm-role-deletion');
    }

    public function deleteRole()
    {
        // Prevent deleting core roles if they exist
        $role = Role::findOrFail($this->role_id_to_delete);
        if (in_array($role->name, ['Admin', 'Kasir', 'Anggota Koperasi'])) {
            session()->flash('error', 'Role inti tidak dapat dihapus.');
            $this->confirmingRoleDeletion = false;
            $this->dispatch('close-modal', 'confirm-role-deletion');
            return;
        }
        
        $role->delete();
        session()->flash('message', 'Role Berhasil Dihapus.');
        $this->confirmingRoleDeletion = false;
        $this->dispatch('close-modal', 'confirm-role-deletion');
    }
}