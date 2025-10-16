<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class UserFormModal extends Component
{
    public $editMode = false;
    public $userId;
    
    public $nrp, $username, $email, $full_name, $phone, $join_date, $is_active = true;
    public $password, $password_confirmation;
    public $selectedRoles = [];
    public $allRoles;

    public function mount()
    {
        $this->allRoles = Role::all();
    }

    protected function rules()
    {
        $rules = [
            'nrp' => ['required', Rule::unique('users')->ignore($this->userId, 'user_id')],
            'username' => ['required', Rule::unique('users')->ignore($this->userId, 'user_id')],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId, 'user_id')],
            'full_name' => 'required',
            'phone' => 'nullable',
            'join_date' => 'required|date',
            'selectedRoles' => 'required|array|min:1',
        ];

        if (!$this->editMode) {
            $rules['password'] = 'required|min:6|confirmed';
        } elseif (!empty($this->password)) {
            $rules['password'] = 'sometimes|min:6|confirmed';
        }

        return $rules;
    }

    #[On('createUser')]
    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
        $this->dispatch('open-modal', 'user-form-modal');
    }

    #[On('editUser')]
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->nrp = $user->nrp;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->full_name = $user->full_name;
        $this->phone = $user->phone;
        $this->join_date = $user->join_date->format('Y-m-d');
        $this->is_active = $user->is_active;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        
        $this->editMode = true;
        $this->dispatch('open-modal', 'user-form-modal');
    }

    public function save()
    {
        $this->validate($this->rules());

        $updateData = [
            'nrp' => $this->nrp,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'join_date' => $this->join_date,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['user_id' => $this->userId], $updateData);
        $user->syncRoles($this->selectedRoles);

        session()->flash('message', $this->editMode ? 'User berhasil diupdate.' : 'User berhasil ditambahkan.');
        $this->closeModal();
        $this->dispatch('userSaved');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'user-form-modal');
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->editMode = false;
        $this->userId = null;
        $this->nrp = '';
        $this->username = '';
        $this->email = '';
        $this->full_name = '';
        $this->phone = '';
        $this->join_date = '';
        $this->is_active = true;
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = [];
    }

    public function render()
    {
        return view('livewire.user-form-modal');
    }
}
