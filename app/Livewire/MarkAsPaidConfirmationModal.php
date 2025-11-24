<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Angsuran;
use Livewire\Attributes\On;

class MarkAsPaidConfirmationModal extends Component
{
    public $isOpen = false;
    public $angsuranId;
    public $angsuran;

    #[On('openMarkAsPaidModal')]
    public function openModal($angsuranId)
    {
        $this->angsuranId = $angsuranId;
        $this->angsuran = Angsuran::with('pinjaman.user')->find($angsuranId);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->angsuran = null;
        $this->angsuranId = null;
    }

    public function markAsPaid()
    {
        $angsuran = Angsuran::findOrFail($this->angsuranId);

        if ($angsuran->status === 'paid') {
            session()->flash('error', 'Angsuran ini sudah lunas.');
            $this->closeModal();
            $this->dispatch('installmentPaymentFailed');
            return;
        }

        $angsuran->status = 'paid';
        $angsuran->payment_date = now();
        $angsuran->save();

        if($angsuran->pinjaman) {
            $angsuran->pinjaman->recalculateRemainingBalance();
        }

        session()->flash('message', 'Angsuran berhasil ditandai sebagai lunas.');
        
        $this->closeModal();
        $this->dispatch('installmentPaid', pinjamanId: $angsuran->pinjaman_id);
    }

    public function render()
    {
        return view('livewire.mark-as-paid-confirmation-modal');
    }
}
