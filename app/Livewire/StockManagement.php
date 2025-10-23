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
    public $selectedStock;
    public $viewingStockId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewHistory($stockId)
    {
        $this->viewingStockId = $stockId;
        $this->selectedStock = Stock::with(['product', 'location'])->findOrFail($stockId);
        $this->resetPage('movementsPage');
        $this->dispatch('open-modal', 'stock-history-modal');
    }

    public function closeModal()
    {
        $this->viewingStockId = null;
        $this->selectedStock = null;
        $this->dispatch('close-modal', 'stock-history-modal');
    }

    public function render()
    {
        $stocks = Stock::with(['product', 'location'])
            ->whereHas('product', function ($query) {
                $query->where('product_name', 'like', '%' . $this->search . '%')
                      ->orWhere('product_code', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $stockMovements = [];
        if ($this->viewingStockId) {
            $stockMovements = StockMovement::where('product_id', $this->selectedStock->product_id)
                ->where('location_id', $this->selectedStock->location_id)
                ->orderBy('movement_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'movementsPage');
        }

        return view('livewire.stock-management', [
            'stocks' => $stocks,
            'stockMovements' => $stockMovements,
        ]);
    }
}