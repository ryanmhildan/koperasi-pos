<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MyTransactionHistory extends Component
{
    use WithPagination;

    public $simpanan_balance;

    public function mount()
    {
        $user = Auth::user();
        $simpananWallet = $user->getWallet('simpanan');
        $this->simpanan_balance = $simpananWallet ? $simpananWallet->balance() : 0;
    }

    public function render()
    {
        $user = Auth::user();
        $simpananWallet = $user->getWallet('simpanan');
        $operasionalWallet = $user->getWallet('operasional');

        $transactions = collect([]);

        if ($simpananWallet) {
            $transactions = $transactions->merge($simpananWallet->transactions);
        }

        if ($operasionalWallet) {
            $transactions = $transactions->merge($operasionalWallet->transactions);
        }

        $sortedTransactions = $transactions->sortByDesc('created_at');

        // Manual pagination
        $page = request()->get('page', 1);
        $perPage = 10;
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedTransactions->forPage($page, $perPage),
            $sortedTransactions->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('livewire.my-transaction-history', [
            'transactions' => $paginatedTransactions,
        ]);
    }
}