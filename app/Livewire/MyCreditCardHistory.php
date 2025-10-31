<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class MyCreditCardHistory extends Component
{
    public function render()
    {
        return view('livewire.my-credit-card-history', [
            'userId' => Auth::id(), // Pass the authenticated user's ID
        ])->layout('layouts.app'); // Assuming it uses the app layout
    }
}
