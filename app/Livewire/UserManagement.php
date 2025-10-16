<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->dispatch('createUser');
    }

    public function edit($id)
    {
        $this->dispatch('editUser', id: $id);
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User berhasil dihapus.');
    }

    public function manageCard($userId)
    {
        $this->dispatch('manageUserCard', userId: $userId);
    }

    #[On('userSaved')]
    #[On('cardSaved')]
    public function render()
    {
        $users = User::with('roles')
            ->where(function($query) {
                $query->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        // This is passed for the main view, not the modal
        $roles = Role::all();

        return view('livewire.user-management', compact('users', 'roles'));
    }
}
