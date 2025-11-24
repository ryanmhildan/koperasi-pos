<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pinjaman;
use Livewire\Attributes\On;

class DeleteConfirmationModal extends Component
{
    public $isOpen = false;
    public $pinjamanId;
    public $pinjaman;

    #[On('openDeleteModal')]
    public function openModal($pinjamanId)
    {
        $this->pinjamanId = $pinjamanId;
        $this->pinjaman = Pinjaman::with('user')->find($pinjamanId);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->pinjaman = null;
        $this->pinjamanId = null;
    }

    public function delete()
    {
        $pinjaman = Pinjaman::findOrFail($this->pinjamanId);

        // This logic is from the original PinjamanManagement component.
        // It only allows deletion of 'pending' loans.
        if ($pinjaman->status !== 'pending') {
            session()->flash('error', 'Hanya pinjaman dengan status "Pending" yang dapat dihapus.');
            $this->closeModal();
            $this->dispatch('loanDeletionFailed');
            return;
        }
        
        // No need to handle wallet logic since pending loans are not disbursed.
        $pinjaman->angsuran()->delete();
        $pinjaman->delete();
        
        session()->flash('message', 'Pinjaman berhasil dihapus.');
        
        $this->closeModal();
        $this->dispatch('loanDeleted');
    }

    public function render()
    {
        return view('livewire.delete-confirmation-modal');
    }
}
