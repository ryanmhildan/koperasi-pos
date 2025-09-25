<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GoodReceiptNote;

class GrnList extends Component
{
    use WithPagination;

    public function render()
    {
        $grns = GoodReceiptNote::with('location')->latest()->paginate(15);
        return view('livewire.grn-list', ['grns' => $grns]);
    }
}
