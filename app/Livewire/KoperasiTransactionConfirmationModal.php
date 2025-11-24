<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class KoperasiTransactionConfirmationModal extends Component
{
    public $isOpen = false;
    public $action;
    public $amount;
    public $description;

    #[On('openKoperasiTransactionModal')]
    public function openModal($action, $amount, $description)
    {
        $this->action = $action;
        $this->amount = $amount;
        $this->description = $description;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->action = null;
        $this->amount = null;
        $this->description = null;
    }

    public function confirm()
    {
        $this->dispatch('transactionConfirmed', action: $this->action);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.koperasi-transaction-confirmation-modal');
    }
}
