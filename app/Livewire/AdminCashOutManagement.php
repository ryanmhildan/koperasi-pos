<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashOutTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminCashOutManagement extends Component
{
    use WithPagination;

    public $statusFilter = 'pending';
    public $search = '';

    public function approve(CashOutTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return;
        }

        $user = $transaction->user;
        $simpananWallet = $user->getWallet('simpanan');

        if ($simpananWallet->balance < $transaction->amount) {
            $this->reject($transaction, 'Saldo tidak mencukupi');
            return;
        }

        $simpananWallet->withdraw($transaction->amount, [
            'description' => 'Penarikan tunai disetujui: ' . $transaction->notes,
            'reference_id' => $transaction->cash_out_id,
        ]);

        $transaction->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
        ]);
        
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Permintaan cash out berhasil disetujui.',
        ]);
    }

    public function reject(CashOutTransaction $transaction, $reason = null)
    {
        if ($transaction->status !== 'pending') {
            return;
        }

        $transaction->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
            'notes' => $reason ?? 'Ditolak oleh admin',
        ]);

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Permintaan cash out berhasil ditolak.',
        ]);
    }

    public function render()
    {
        $cashouts = CashOutTransaction::with('user', 'processor')
            ->where(function ($query) {
                $query->whereHas('user', function($q) {
                    $q->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin-cash-out-management', [
            'cashouts' => $cashouts,
        ]);
    }
}
