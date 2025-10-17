<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfilePage extends Component
{
    public string $activeTab = 'profil';

    protected $queryString = ['activeTab'];

    public function mount()
    {
        // Default tab
        $this->activeTab = request()->query('activeTab', 'profil');
    }

    public function render()
    {
        return view('livewire.profile-page', [
            'user' => Auth::user()
        ])->layout('layouts.app');
    }
}
