<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class OperasionalManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $selectedUser;
    public $amount;
    public $transactionType = 'deposit';
    public $description;

    protected $listeners = ['transactionProcessed' => '$refresh'];

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'transactionType' => 'required|in:deposit,withdraw',
        'description' => 'required|string|max:255',
    ];

    public function openModal(User $user)
    {
        $this->selectedUser = $user;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->selectedUser = null;
        $this->showModal = false;
        $this->reset(['amount', 'transactionType', 'description']);
    }

    public function processTransaction()
    {
        $this->validate();

        $operasionalWallet = $this->selectedUser->getOrCreateWallet('operasional');

        if ($this->transactionType === 'deposit') {
            $transaction = $operasionalWallet->deposit($this->amount, null, ['description' => $this->description]);
            $operasionalWallet->confirmTransaction($transaction);
        } else {
            if ($operasionalWallet->balance() < $this->amount) {
                $this->dispatch('swal:error', [
                    'title' => 'Gagal!',
                    'text' => 'Saldo tidak mencukupi untuk penarikan.',
                ]);
                return;
            }
            $operasionalWallet->withdraw($this->amount, null, ['description' => $this->description]);
        }

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Transaksi berhasil diproses.',
        ]);

        $this->dispatch('transactionProcessed');
        $this->closeModal();
    }

    public function render()
    {
        $users = User::role('Anggota')
            ->where(function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nrp', 'like', '%' . $this->search . '%');
            })
            ->with('wallets')
            ->paginate(10);

        return view('livewire.operasional-management', [
            'users' => $users,
        ]);
    }
}