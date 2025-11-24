<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\{SalesTransaction, SalesTransactionDetail, Stock, StockMovement};
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade

class TransactionHistory extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $search;
    public $selectedTransaction;
    public $userId; // New property

    public $confirmingVoid = false; // New property for confirmation modal
    public $transactionToVoidId; // New property to store the ID of the transaction to be voided


    public function mount()
    {
        $this->startDate = Carbon::today()->startOfMonth()->toDateString();
        $this->endDate = Carbon::today()->endOfMonth()->toDateString();
        $this->selectedTransaction = null;
        // Optionally, if this component is always for the logged-in user, you can set it here:
        // $this->userId = auth()->id();
    }

    public function viewDetails($transactionId)
    {
        $this->selectedTransaction = SalesTransaction::with(['details.product', 'cashier', 'customer'])->find($transactionId);
    }

    public function closeModal()
    {
        $this->selectedTransaction = null;
    }

    public function confirmVoid($transactionId)
    {
        $this->confirmingVoid = true;
        $this->transactionToVoidId = $transactionId;
        $this->dispatch('open-modal', 'confirm-void-transaction'); // Assuming a modal for confirmation
    }

    public function voidTransaction($transactionId)
    {
        $this->transactionToVoidId = $transactionId; // Set the property from the passed parameter

        if (!$this->transactionToVoidId) {
            session()->flash('error', 'Tidak ada transaksi yang dipilih untuk dibatalkan.');
            return;
        }

        DB::beginTransaction();
        try {
            $transaction = SalesTransaction::with('details', 'customer', 'cashDrawer')->find($this->transactionToVoidId);

            if (!$transaction) {
                session()->flash('error', 'Transaksi tidak ditemukan.');
                DB::rollBack();
                return;
            }

            if ($transaction->status === 'voided') {
                session()->flash('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
                DB::rollBack();
                return;
            }

            // Update transaction status
            $transaction->status = 'voided';
            $transaction->save();

            // Revert stock
            foreach ($transaction->details as $detail) {
                $stock = Stock::where('product_id', $detail->product_id)
                              ->where('location_id', $transaction->cashDrawer->location_id ?? null)
                              ->first();

                if ($stock) {
                    $stock->increment('current_stock', $detail->quantity);

                    StockMovement::create([
                        'product_id' => $detail->product_id,
                        'location_id' => $transaction->cashDrawer->location_id ?? null,
                        'movement_type' => 'in',
                        'quantity' => $detail->quantity,
                        'average_price' => $detail->average_price,
                        'reference_type' => 'void_sales',
                        'reference_id' => $transaction->transaction_id,
                        'movement_date' => today(),
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            // Revert wallet balance if applicable
            if ($transaction->payment_method === 'wallet' && $transaction->customer) {
                $simpananWallet = $transaction->customer->getWallet('simpanan');
                if ($simpananWallet) {
                    $simpananWallet->deposit($transaction->total_amount, ['description' => 'Pembatalan pembelian di POS', 'reference_id' => $transaction->transaction_id]);
                }
            }

            DB::commit();
            session()->flash('success', 'Transaksi berhasil dibatalkan dan stok/saldo dikembalikan.');
            $this->reset(['confirmingVoid', 'transactionToVoidId']);
            $this->dispatch('close-modal', 'confirm-void-transaction');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    public function cancelVoid()
    {
        $this->reset(['confirmingVoid', 'transactionToVoidId']);
        $this->dispatch('close-modal', 'confirm-void-transaction');
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
            // Add this new condition
            ->when($this->userId, function ($query) {
                $query->where('user_id', $this->userId);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.report.transaction-history', [
            'transactions' => $transactions,
        ])->layout('layouts.app');
    }
}