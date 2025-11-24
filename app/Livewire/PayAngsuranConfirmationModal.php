<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Livewire\Attributes\On;

class PayAngsuranConfirmationModal extends Component
{
    public $isOpen = false;
    public $pinjaman_id;
    public $amount;
    public ?Pinjaman $pinjaman = null;

    #[On('openPayAngsuranModal')]
    public function openModal($pinjaman_id, $amount)
    {
        $this->pinjaman_id = $pinjaman_id;
        $this->amount = $amount;
        $this->pinjaman = Pinjaman::find($pinjaman_id);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->pinjaman_id = null;
        $this->amount = null;
        $this->pinjaman = null;
    }

    public function confirm()
    {
        $this->dispatch('paymentConfirmed', pinjaman_id: $this->pinjaman_id, amount: $this->amount);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.pay-angsuran-confirmation-modal');
    }
}
