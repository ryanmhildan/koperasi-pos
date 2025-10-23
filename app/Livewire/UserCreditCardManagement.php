<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserCreditCard;
use App\Models\User;

class UserCreditCardManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $cardId;

    // Form fields
    public $user_id, $card_number, $credit_limit, $current_balance, $cash_out_limit, $expiry_date, $bank_name, $is_active = true;

    public $card_id_to_delete;
    public $confirmingCardDeletion = false;
    public $confirmingCardSave = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,user_id'],
            'card_number' => ['required', 'unique:user_credit_cards,card_number,' . $this->cardId . ',card_id'],
            'credit_limit' => 'required|numeric|min:0',
            'cash_out_limit' => 'required|numeric|min:0',
            'expiry_date' => 'required|date_format:m/y',
            'bank_name' => 'required|string',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
        $this->dispatch('open-modal', 'card-form-modal');
    }

    public function confirmCardSave()
    {
        $this->validate();
        $this->confirmingCardSave = true;
        $this->dispatch('open-modal', 'confirm-card-save');
    }

    public function store()
    {
        UserCreditCard::create([
            'user_id' => $this->user_id,
            'card_number' => $this->card_number,
            'credit_limit' => $this->credit_limit,
            'cash_out_limit' => $this->cash_out_limit,
            'expiry_date' => $this->expiry_date,
            'bank_name' => $this->bank_name,
            'is_active' => $this->is_active,
            'current_balance' => 0, // Initial balance
        ]);

        session()->flash('message', 'Kartu kredit berhasil ditambahkan.');
        $this->closeModalAndReset();
    }

    public function edit($id)
    {
        $card = UserCreditCard::findOrFail($id);
        $this->cardId = $id;
        $this->user_id = $card->user_id;
        $this->card_number = $card->card_number;
        $this->credit_limit = $card->credit_limit;
        $this->current_balance = $card->current_balance;
        $this->cash_out_limit = $card->cash_out_limit;
        $this->expiry_date = $card->expiry_date;
        $this->bank_name = $card->bank_name;
        $this->is_active = $card->is_active;
        
        $this->editMode = true;
        $this->dispatch('open-modal', 'card-form-modal');
    }

    public function update()
    {
        $card = UserCreditCard::findOrFail($this->cardId);
        $card->update([
            'user_id' => $this->user_id,
            'card_number' => $this->card_number,
            'credit_limit' => $this->credit_limit,
            'cash_out_limit' => $this->cash_out_limit,
            'expiry_date' => $this->expiry_date,
            'bank_name' => $this->bank_name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Kartu kredit berhasil diupdate.');
        $this->closeModalAndReset();
    }

    public function confirmCardDeletion($id)
    {
        $this->card_id_to_delete = $id;
        $this->confirmingCardDeletion = true;
        $this->dispatch('open-modal', 'confirm-card-deletion');
    }

    public function deleteCard()
    {
        UserCreditCard::find($this->card_id_to_delete)->delete();
        session()->flash('message', 'Kartu kredit berhasil dihapus.');
        $this->confirmingCardDeletion = false;
        $this->dispatch('close-modal', 'confirm-card-deletion');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'card-form-modal');
        $this->resetInputFields();
    }

    private function closeModalAndReset()
    {
        $this->dispatch('close-modal', 'card-form-modal');
        $this->dispatch('close-modal', 'confirm-card-save');
        $this->resetInputFields();
        $this->confirmingCardSave = false;
    }

    private function resetInputFields()
    {
        $this->reset(['cardId', 'user_id', 'card_number', 'credit_limit', 'current_balance', 'cash_out_limit', 'expiry_date', 'bank_name', 'is_active']);
        $this->editMode = false;
    }

    public function render()
    {
        $cards = UserCreditCard::with('user')
            ->whereHas('user', function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nrp', 'like', '%' . $this->search . '%');
            })
            ->orWhere('card_number', 'like', '%' . $this->search . '%')
            ->paginate(10);
            
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Anggota');
        })->get();

        return view('livewire.user-credit-card-management', [
            'cards' => $cards,
            'users' => $users
        ])->layout('layouts.app');
    }
}