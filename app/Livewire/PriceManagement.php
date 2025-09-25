<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Price;
use App\Models\SellingPrice;
use App\Models\Location;

class PriceManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showPriceModal = false;
    public $selectedProduct;

    public $costPrices = [];
    public $sellingPrices = [];
    public $locations = [];

    // Form fields for new/edit
    public $editingCostPrice = [];
    public $editingSellingPrice = [];

    protected $rules = [
        'editingCostPrice.location_id' => 'required',
        'editingCostPrice.average_price' => 'required|numeric|min:0',
        'editingSellingPrice.location_id' => 'required',
        'editingSellingPrice.selling_price' => 'required|numeric|min:0',
        'editingSellingPrice.discount' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->locations = Location::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function managePrices($productId)
    {
        $this->selectedProduct = Product::findOrFail($productId);
        $this->costPrices = Price::where('product_id', $productId)->with('location')->get();
        $this->sellingPrices = SellingPrice::where('product_id', $productId)->with('location')->get();
        $this->resetEditingFields();
        $this->showPriceModal = true;
    }

    public function saveCostPrice()
    {
        $this->validate(['editingCostPrice.location_id', 'editingCostPrice.average_price']);

        Price::updateOrCreate(
            [
                'product_id' => $this->selectedProduct->product_id,
                'location_id' => $this->editingCostPrice['location_id'],
            ],
            [
                'average_price' => $this->editingCostPrice['average_price'],
            ]
        );

        session()->flash('message', 'Harga pokok berhasil disimpan.');
        $this->managePrices($this->selectedProduct->product_id); // Refresh data
    }

    public function saveSellingPrice()
    {
        $this->validate(['editingSellingPrice.location_id', 'editingSellingPrice.selling_price', 'editingSellingPrice.discount']);

        SellingPrice::updateOrCreate(
            [
                'product_id' => $this->selectedProduct->product_id,
                'location_id' => $this->editingSellingPrice['location_id'],
            ],
            [
                'selling_price' => $this->editingSellingPrice['selling_price'],
                'discount' => $this->editingSellingPrice['discount'] ?? 0,
            ]
        );

        session()->flash('message', 'Harga jual berhasil disimpan.');
        $this->managePrices($this->selectedProduct->product_id); // Refresh data
    }

    public function closeModal()
    {
        $this->showPriceModal = false;
        $this->selectedProduct = null;
        $this->costPrices = [];
        $this->sellingPrices = [];
        $this->resetEditingFields();
    }

    private function resetEditingFields()
    {
        $this->editingCostPrice = ['location_id' => '', 'average_price' => ''];
        $this->editingSellingPrice = ['location_id' => '', 'selling_price' => '', 'discount' => ''];
    }

    public function render()
    {
        $products = Product::where('product_name', 'like', '%' . $this->search . '%')
            ->orWhere('product_code', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.price-management', [
            'products' => $products,
        ]);
    }
}
