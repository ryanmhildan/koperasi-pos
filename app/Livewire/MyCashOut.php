<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\CashOutTransaction;
use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MyCashOut extends Component
{
    use WithPagination;

    public $amount;
    public $notes;
    public $current_balance;

    protected $rules = [
        'amount' => 'required|numeric|min:10000',
        'notes' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $simpananWallet = Auth::user()->getWallet('simpanan');
        $this->current_balance = $simpananWallet ? $simpananWallet->balance : 0;
    }

    public function requestCashOut()
    {
        $this->validate();

        $user = Auth::user();
        $simpananWallet = $user->getWallet('simpanan');

        if (!$simpananWallet || $this->amount > $simpananWallet->balance) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Saldo simpanan tidak mencukupi.',
            ]);
            return;
        }

        CashOutTransaction::create([
            'user_id' => $user->user_id,
            'amount' => $this->amount,
            'transaction_date' => now(),
            'notes' => $this->notes,
            'status' => 'pending', // pending, approved, rejected
        ]);

        $this->reset(['amount', 'notes']);

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pengajuan cash out berhasil dikirim. Mohon tunggu persetujuan dari admin.',
        ]);
        
        $this->mount();
    }

    public function render()
    {
        $cashouts = CashOutTransaction::where('user_id', Auth::id())->latest()->paginate(10);
        return view('livewire.my-cash-out', [
            'cashouts' => $cashouts,
        ]);
    }
}
