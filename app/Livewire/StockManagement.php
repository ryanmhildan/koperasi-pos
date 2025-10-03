<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Product;

class StockManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showHistoryModal = false;
    public $selectedProduct;
    public $stockMovements = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewHistory($stockId)
    {
        $stock = Stock::with(['product', 'location'])->findOrFail($stockId);
        $this->selectedProduct = $stock->product;

        $this->stockMovements = StockMovement::where('product_id', $stock->product_id)
            ->where('location_id', $stock->location_id)
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->showHistoryModal = true;
    }

    public function closeModal()
    {
        $this->showHistoryModal = false;
        $this->selectedProduct = null;
        $this->stockMovements = [];
    }

    public function render()
    {
        $stocks = Stock::with(['product', 'location'])
            ->whereHas('product', function ($query) {
                $query->where('product_name', 'like', '%' . $this->search . '%')
                      ->orWhere('product_code', 'like', '%' . $this->search . '%');
            })
            ->paginate(15);

        return view('livewire.stock-management', [
            'stocks' => $stocks,
        ]);
    }
}
