<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{SalesTransaction, SalesTransactionDetail, Stock, StockMovement, UserCreditCard};
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosKasirTransactionHistory extends Component
{
    use WithPagination;

    public $transactions; // Will hold the last 10 transactions
    public $selectedTransaction;

    public $confirmingVoid = false;
    public $transactionToVoidId;

    protected $listeners = ['transactionVoided' => 'refreshTransactions']; // Listen for voided event

    public function mount()
    {
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = SalesTransaction::with(['cashier', 'customer'])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function refreshTransactions()
    {
        $this->loadTransactions();
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
        $this->dispatch('open-modal', 'confirm-void-transaction-pos-kasir'); // Unique modal ID
    }

    public function voidTransaction($transactionId)
    {
        $this->transactionToVoidId = $transactionId;

        if (!$this->transactionToVoidId) {
            session()->flash('error', 'Tidak ada transaksi yang dipilih untuk dibatalkan.');
            return;
        }

        DB::beginTransaction();
        try {
            $transaction = SalesTransaction::with('details', 'creditCard', 'cashDrawer')->find($this->transactionToVoidId);

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

            // Revert credit card balance if applicable
            if ($transaction->payment_method === 'credit_card' && $transaction->card_id && $transaction->creditCard) {
                $creditCard = $transaction->creditCard;
                $creditCard->decrement('current_balance', $transaction->total_amount);
            }

            DB::commit();
            session()->flash('success', 'Transaksi berhasil dibatalkan dan stok/limit dikembalikan.');
            $this->reset(['confirmingVoid', 'transactionToVoidId']);
            $this->dispatch('close-modal', 'confirm-void-transaction-pos-kasir');
            $this->dispatch('transactionVoided'); // Dispatch event to refresh parent if needed
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    public function cancelVoid()
    {
        $this->reset(['confirmingVoid', 'transactionToVoidId']);
        $this->dispatch('close-modal', 'confirm-void-transaction-pos-kasir');
    }

    public function render()
    {
        return view('livewire.pos-kasir-transaction-history');
    }
}