<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GoodReceiptNote;

class GrnList extends Component
{
    use WithPagination;

    public $selectedGrn;

    public function viewDetails($grnId)
    {
        $this->selectedGrn = GoodReceiptNote::with('details.product', 'location')->findOrFail($grnId);
        $this->dispatch('open-modal', 'grn-details-modal');
    }

    public function closeModal()
    {
        $this->selectedGrn = null;
        $this->dispatch('close-modal', 'grn-details-modal');
    }

    public function render()
    {
        $grns = GoodReceiptNote::with('location')->latest()->paginate(15);
        return view('livewire.grn-list', ['grns' => $grns]);
    }
}
