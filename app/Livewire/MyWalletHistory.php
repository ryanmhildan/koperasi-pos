<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MyWalletHistory extends Component
{
    use WithPagination;

    public $walletType = 'all';
    public $userId;
    public $user;

    public function mount($userId = null)
    {
        $this->userId = $userId;
        if ($this->userId) {
            $this->user = User::findOrFail($this->userId);
        } else {
            $this->user = Auth::user();
        }
    }

    public function render()
    {
        $wallets = $this->user->wallets;
        $walletIds = $wallets->pluck('id');

        $query = \Bavix\Wallet\Models\Transaction::query()->whereIn('wallet_id', $walletIds)
            ->where(function ($query) {
                $query->whereNull('meta->hidden_from_history')
                      ->orWhere('meta->hidden_from_history', '!=', true);
            })
            ->latest();

        if ($this->walletType !== 'all') {
            $wallet = $this->user->getWallet($this->walletType);
            if ($wallet) {
                $query->where('wallet_id', $wallet->id);
            }
        }

        $transactions = $query->paginate(10);

        return view('livewire.my-wallet-history', [
            'wallets' => $wallets,
            'transactions' => $transactions,
        ]);
    }
}
