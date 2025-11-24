<?php

namespace App\Livewire;

use Bavix\Wallet\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class SimpananManagement extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        // This will open the SimpananFormModal, which now only performs a deposit.
        $this->dispatch('createSimpanan');
    }

    /**
     * Editing and Deleting wallet transactions is not advisable as they are immutable records.
     * The functions are kept here as placeholders in case a different business logic
     * (like creating reversal transactions) is decided upon later.
     */
    public function edit($transactionId)
    {
        // Not implemented. Wallet transactions are immutable.
        session()->flash('error', 'Mengedit transaksi secara langsung tidak diperbolehkan.');
    }

    public function delete($transactionId)
    {
        // Not implemented. Wallet transactions are immutable.
        session()->flash('error', 'Menghapus transaksi secara langsung tidak diperbolehkan.');
    }

    #[On('simpananSaved')]
    public function render()
    {
        $transactions = Transaction::whereHas('wallet', function ($query) {
            $query->where('slug', 'simpanan');
        })
        ->where('type', 'deposit') // Filter to only show deposit transactions
        ->with('payable') // Eager load the user model
        ->where(function ($query) {
            // Search by user name or NRP
            $query->whereHas('payable', function ($subQuery) {
                $subQuery->where('full_name', 'like', '%' . $this->search . '%')
                         ->orWhere('nrp', 'like', '%'.$this->search.'%');
            })
            // Search by transaction meta description
            ->orWhereJsonContains('meta->description', $this->search);
        })
        ->latest()
        ->paginate(10);

        return view('livewire.simpanan-management', [
            'transactions' => $transactions,
        ]);
    }
}