<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\UserCreditCard;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $userId;
    
    public $nrp, $username, $email, $full_name, $phone, $join_date, $is_active = true;
    public $password, $password_confirmation;
    public $selectedRole;
    
    // Credit Card fields
    public $showCardModal = false;
    public $card_number, $credit_limit, $cash_out_limit, $expiry_date, $bank_name;

    protected $rules = [
        'nrp' => 'required|unique:users,nrp',
        'username' => 'required|unique:users,username',
        'email' => 'required|email|unique:users,email',
        'full_name' => 'required',
        'phone' => 'nullable',
        'join_date' => 'required|date',
        'password' => 'required|min:6|confirmed',
        'selectedRole' => 'required',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $user = User::create([
            'nrp' => $this->nrp,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'join_date' => $this->join_date,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
        ]);

        $user->assignRole($this->selectedRole);

        session()->flash('message', 'User berhasil ditambahkan.');
        $this->closeModal();
    }

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
        $this->selectedRole = $user->roles->first()?->name;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['nrp'] = 'required|unique:users,nrp,' . $this->userId . ',user_id';
        $rules['username'] = 'required|unique:users,username,' . $this->userId . ',user_id';
        $rules['email'] = 'required|email|unique:users,email,' . $this->userId . ',user_id';
        
        if (empty($this->password)) {
            unset($rules['password']);
        }
        
        $this->validate($rules);

        $user = User::findOrFail($this->userId);
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

        $user->update($updateData);
        $user->syncRoles([$this->selectedRole]);

        session()->flash('message', 'User berhasil diupdate.');
        $this->closeModal();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User berhasil dihapus.');
    }

    public function manageCard($userId)
    {
        $this->userId = $userId;
        $user = User::findOrFail($userId);
        $card = $user->creditCards->first();
        
        if ($card) {
            $this->card_number = $card->card_number;
            $this->credit_limit = $card->credit_limit;
            $this->cash_out_limit = $card->cash_out_limit;
            $this->expiry_date = $card->expiry_date;
            $this->bank_name = $card->bank_name;
        }
        
        $this->showCardModal = true;
    }

    public function saveCard()
    {
        $this->validate([
            'card_number' => 'required',
            'credit_limit' => 'required|numeric',
            'cash_out_limit' => 'required|numeric',
            'expiry_date' => 'required',
            'bank_name' => 'required',
        ]);

        UserCreditCard::updateOrCreate(
            ['user_id' => $this->userId],
            [
                'card_number' => $this->card_number,
                'credit_limit' => $this->credit_limit,
                'cash_out_limit' => $this->cash_out_limit,
                'expiry_date' => $this->expiry_date,
                'bank_name' => $this->bank_name,
                'is_active' => true,
            ]
        );

        session()->flash('message', 'Kartu kredit berhasil disimpan.');
        $this->showCardModal = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showCardModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->nrp = '';
        $this->username = '';
        $this->email = '';
        $this->full_name = '';
        $this->phone = '';
        $this->join_date = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRole = '';
        $this->is_active = true;
        $this->editMode = false;
        $this->userId = null;
        
        // Card fields
        $this->card_number = '';
        $this->credit_limit = '';
        $this->cash_out_limit = '';
        $this->expiry_date = '';
        $this->bank_name = '';
    }

    public function render()
    {
        $users = User::with('roles')
            ->where(function($query) {
                $query->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.user-management', compact('users', 'roles'));
    }
}