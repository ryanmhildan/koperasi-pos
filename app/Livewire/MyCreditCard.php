<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCreditCard;

class MyCreditCard extends Component
{
    public $card;

    public function mount()
    {
        $this->card = UserCreditCard::where('user_id', Auth::id())->first();
    }

    public function render()
    {
        return view('livewire.my-credit-card')->layout('layouts.app');
    }
}