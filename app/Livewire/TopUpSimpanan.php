<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class TopUpSimpanan extends Component
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
        'description' => 'nullable|string|max:255',
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
        $this->reset(['amount', 'description']);
    }

    public function processTransaction()
    {
        $this->validate();

        try {
            $simpananWallet = $this->selectedUser->getWallet('simpanan');

            if (!$simpananWallet) {
                $this->selectedUser->createWallet(['name' => 'simpanan', 'slug' => 'simpanan']);
                $simpananWallet = $this->selectedUser->getWallet('simpanan');
            }

            $simpananWallet->deposit($this->amount, ['description' => $this->description]);

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Transaksi berhasil diproses.',
            ]);

            $this->dispatch('transactionProcessed');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Terjadi Error!',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $users = User::where(function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nrp', 'like', '%' . $this->search . '%');
            })
            ->with('wallets')
            ->paginate(10);

        return view('livewire.top-up-simpanan', [
            'users' => $users,
        ]);
    }
}