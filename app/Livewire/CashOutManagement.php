<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashOutTransaction;
use App\Models\UserCreditCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashOutManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $amount;
    public $description;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
    ];

    public function requestCashOut()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $creditCard = $user->creditCard;

            if (!$creditCard) {
                throw new \Exception('Anda tidak memiliki kartu kredit yang terdaftar.');
            }

            // Check if cash out limit is exceeded
            $cashOutThisMonth = $creditCard->cash_out_used_this_month ?? 0;
            if (($cashOutThisMonth + $this->amount) > $creditCard->cash_out_limit) {
                throw new \Exception('Batas penarikan tunai bulanan Anda akan terlampaui.');
            }

            // Check if current balance is sufficient
            if ($this->amount > $creditCard->current_balance) {
                throw new \Exception('Saldo Anda tidak mencukupi untuk melakukan penarikan.');
            }

            CashOutTransaction::create([
                'user_id' => $user->user_id,
                'card_id' => $creditCard->card_id,
                'amount' => $this->amount,
                'description' => $this->description,
                'transaction_date' => now(),
                'status' => 'pending', // Admin needs to approve
            ]);

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Permintaan Berhasil',
                'text' => 'Permintaan penarikan tunai Anda telah diajukan dan menunggu persetujuan admin.',
            ]);

            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Permintaan Gagal',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetInputFields()
    {
        $this->amount = '';
        $this->description = '';
    }

    public function render()
    {
        $transactions = CashOutTransaction::whereHas('creditCard.user', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->latest()
            ->paginate(10);
            
        $creditCard = Auth::user()->creditCard;

        return view('livewire.cash-out-management', [
            'transactions' => $transactions,
            'creditCard' => $creditCard
        ]);
    }
}
