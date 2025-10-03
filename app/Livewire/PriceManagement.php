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
    public $selectedProduct;

    public $costPrices = [];
    public $sellingPrices = [];
    public $locations = [];

    // Form fields for new/edit
    public $costPriceId;
    public $editingCostPrice = [];
    public $sellingPriceId;
    public $editingSellingPrice = [];

    protected function rules()
    {
        return [
            'editingCostPrice.location_id' => 'required|integer',
            'editingCostPrice.average_price' => 'required|numeric|min:0',
            'editingSellingPrice.location_id' => 'required|integer',
            'editingSellingPrice.selling_price' => 'required|numeric|min:0',
            'editingSellingPrice.discount' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function mount()
    {
        $this->locations = Location::all();
        $this->resetEditingFields();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function managePrices($productId)
    {
        $this->selectedProduct = Product::findOrFail($productId);
        $this->refreshPrices();
        $this->resetEditingFields();
        $this->dispatch('open-modal', 'price-management-modal');
    }

    public function refreshPrices()
    {
        if ($this->selectedProduct) {
            $this->costPrices = Price::where('product_id', $this->selectedProduct->product_id)->with('location')->get();
            $this->sellingPrices = SellingPrice::where('product_id', $this->selectedProduct->product_id)->with('location')->get();
        }
    }

    public function editCostPrice($id)
    {
        $this->costPriceId = $id;
        $price = Price::findOrFail($id);
        $this->editingCostPrice = [
            'location_id' => $price->location_id,
            'average_price' => $price->average_price,
        ];
    }

    public function saveCostPrice()
    {
        $this->validate([
            'editingCostPrice.location_id' => 'required|integer',
            'editingCostPrice.average_price' => 'required|numeric|min:0',
        ]);

        Price::updateOrCreate(
            [
                'id' => $this->costPriceId,
                'product_id' => $this->selectedProduct->product_id,
            ],
            [
                'location_id' => $this->editingCostPrice['location_id'],
                'average_price' => $this->editingCostPrice['average_price'],
            ]
        );

        session()->flash('message', 'Harga pokok berhasil disimpan.');
        $this->refreshPrices();
        $this->resetEditingFields();
    }
    
    public function deleteCostPrice($id)
    {
        Price::findOrFail($id)->delete();
        session()->flash('message', 'Harga pokok berhasil dihapus.');
        $this->refreshPrices();
    }

    public function editSellingPrice($id)
    {
        $this->sellingPriceId = $id;
        $price = SellingPrice::findOrFail($id);
        $this->editingSellingPrice = [
            'location_id' => $price->location_id,
            'selling_price' => $price->selling_price,
            'discount' => $price->discount,
        ];
    }

    public function saveSellingPrice()
    {
        $this->validate([
            'editingSellingPrice.location_id' => 'required|integer',
            'editingSellingPrice.selling_price' => 'required|numeric|min:0',
            'editingSellingPrice.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        SellingPrice::updateOrCreate(
            [
                'id' => $this->sellingPriceId,
                'product_id' => $this->selectedProduct->product_id,
            ],
            [
                'location_id' => $this->editingSellingPrice['location_id'],
                'selling_price' => $this->editingSellingPrice['selling_price'],
                'discount' => $this->editingSellingPrice['discount'] ?? 0,
            ]
        );

        session()->flash('message', 'Harga jual berhasil disimpan.');
        $this->refreshPrices();
        $this->resetEditingFields();
    }

    public function deleteSellingPrice($id)
    {
        SellingPrice::findOrFail($id)->delete();
        session()->flash('message', 'Harga jual berhasil dihapus.');
        $this->refreshPrices();
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'price-management-modal');
        $this->selectedProduct = null;
        $this->costPrices = [];
        $this->sellingPrices = [];
        $this->resetEditingFields();
    }

    public function resetEditingFields()
    {
        $this->costPriceId = null;
        $this->editingCostPrice = ['location_id' => '', 'average_price' => ''];
        $this->sellingPriceId = null;
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