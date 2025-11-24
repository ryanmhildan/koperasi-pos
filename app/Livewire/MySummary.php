<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pinjaman;

class MySummary extends Component
{
    public $totalSimpanan;
    public $totalPinjaman;

    protected $listeners = ['loanApproved' => 'refresh'];

    public function mount()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $user = Auth::user();

        // Get total savings from 'simpanan' wallet
        $simpananWallet = $user->getWallet('simpanan');
        $this->totalSimpanan = $simpananWallet ? $simpananWallet->balance : 0;

        // Get total outstanding loans
        $this->totalPinjaman = Pinjaman::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->sum('remaining_balance');
    }

    public function render()
    {
        return view('livewire.my-summary');
    }
}
