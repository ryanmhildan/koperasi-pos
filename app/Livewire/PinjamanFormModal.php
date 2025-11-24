<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pinjaman;
use App\Models\User;
use App\Models\Angsuran;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class PinjamanFormModal extends Component
{
    public $showModal = false;
    public $editMode = false;
    public $pinjamanId;
    
    public $user_id, $loan_amount, $interest_rate, $tenor_months;
    public $loan_type, $loan_purpose, $loan_date, $status, $is_blocked;

    public $users, $loanTypes;
    
    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,user_id',
            'loan_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'tenor_months' => 'required|integer|min:1',
            'loan_type' => 'required|in:regular,emergency,business',
            'loan_purpose' => 'required|string|max:255',
            'loan_date' => 'required|date',
            'status' => 'required_if:editMode,true|in:pending,active,closed,overdue',
        ];
    }

    public function mount()
    {
        // Removed role filtering to show all users
        $this->users = User::orderBy('full_name')->get();
        $this->loanTypes = ['regular' => 'Regular', 'emergency' => 'Emergency', 'business' => 'Business'];
    }

    #[On('createPinjaman')]
    public function create()
    {
        $this->resetInputFields();
        $this->loan_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    #[On('editPinjaman')]
    public function edit($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $this->pinjamanId = $id;
        $this->user_id = $pinjaman->user_id;
        $this->loan_amount = $pinjaman->loan_amount;
        $this->interest_rate = $pinjaman->interest_rate;
        $this->tenor_months = $pinjaman->tenor_months;
        $this->loan_type = $pinjaman->loan_type;
        $this->loan_purpose = $pinjaman->loan_purpose;
        $this->loan_date = \Carbon\Carbon::parse($pinjaman->loan_date)->format('Y-m-d');
        $this->status = $pinjaman->status;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $this->update();
        } else {
            $this->store();
        }
    }

    private function store()
    {
        Pinjaman::create([
            'user_id' => $this->user_id,
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'tenor_months' => $this->tenor_months,
            'loan_type' => $this->loan_type,
            'loan_purpose' => $this->loan_purpose,
            'loan_date' => $this->loan_date,
            'status' => 'pending', // Always create as pending
            'remaining_balance' => $this->loan_amount,
        ]);

        session()->flash('message', 'Pinjaman berhasil ditambahkan dan menunggu persetujuan.');
        $this->closeModalAndRefresh();
    }

    private function update()
    {
        $pinjaman = Pinjaman::findOrFail($this->pinjamanId);

        // Editing a loan is a complex operation that should be handled with care
        // especially if it's already active.
        if ($pinjaman->status === 'active' && $pinjaman->wasChanged()) {
             // For simplicity, we assume admins know what they are doing.
             // In a real-world app, more checks would be needed.
        }

        $pinjaman->update([
            'user_id' => $this->user_id,
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'tenor_months' => $this->tenor_months,
            'loan_type' => $this->loan_type,
            'loan_purpose' => $this->loan_purpose,
            'loan_date' => $this->loan_date,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Pinjaman berhasil diupdate.');
        $this->closeModalAndRefresh();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }
    
    private function closeModalAndRefresh()
    {
        $this->closeModal();
        $this->dispatch('pinjamanSaved');
    }

    private function resetInputFields()
    {
        $this->pinjamanId = null;
        $this->editMode = false;
        $this->user_id = '';
        $this->loan_amount = '';
        $this->interest_rate = '';
        $this->tenor_months = '';
        $this->loan_type = 'regular';
        $this->loan_purpose = '';
        $this->loan_date = '';
        $this->status = 'pending';
    }

    public function render()
    {
        return view('livewire.pinjaman-form-modal');
    }
}