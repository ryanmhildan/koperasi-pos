<?php

namespace App\Livewire;

use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementManagement extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $stockMovements = StockMovement::with(['product', 'location', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('product_name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('location', function ($q) {
                    $q->where('location_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.stock-movement-management', [
            'stockMovements' => $stockMovements,
        ]);
    }
}