<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\UserCreditCard;
use Livewire\Attributes\On;

class UserCardModal extends Component
{
    public $userId;
    public $card_number, $credit_limit, $cash_out_limit, $expiry_date, $bank_name;

    #[On('manageUserCard')]
    public function manageCard($userId)
    {
        $this->resetInputFields();
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
        
        $this->dispatch('open-modal', 'card-management-modal');
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
        $this->closeModal();
        $this->dispatch('cardSaved');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'card-management-modal');
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->userId = null;
        $this->card_number = '';
        $this->credit_limit = '';
        $this->cash_out_limit = '';
        $this->expiry_date = '';
        $this->bank_name = '';
    }

    public function render()
    {
        return view('livewire.user-card-modal');
    }
}
