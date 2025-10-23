<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\SalesTransaction;
use Livewire\WithPagination;
use Carbon\Carbon;

class TransactionHistory extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $search;
    public $selectedTransaction;


    public function mount()
    {
        $this->startDate = Carbon::today()->startOfMonth()->toDateString();
        $this->endDate = Carbon::today()->endOfMonth()->toDateString();
        $this->selectedTransaction = null;
    }

    public function viewDetails($transactionId)
    {
        $this->selectedTransaction = SalesTransaction::with(['details.product', 'cashier', 'customer'])->find($transactionId);
    }

    public function closeModal()
    {
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $transactions = SalesTransaction::with(['cashier', 'customer'])
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->when($this->search, function ($query) {
                $query->where('transaction_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('cashier', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.report.transaction-history', [
            'transactions' => $transactions,
        ])->layout('layouts.app');
    }
}