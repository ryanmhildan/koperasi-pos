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

    public $sellingPrices = [];
    public $locations = [];

    // Form fields for new/edit
    public $sellingPriceId;
    public $editingSellingPrice = [];

    // Properties for delete confirmation
    public $priceIdToDelete;
    public $showDeleteConfirmation = false;

    protected function rules()
    {
        return [
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
            $this->sellingPrices = SellingPrice::where('product_id', $this->selectedProduct->product_id)->with('location')->get();
        }
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
        $this->showDeleteConfirmation = false; // Hide delete confirmation if user edits another item
    }

    public function saveSellingPrice()
    {
        $this->validate([
            'editingSellingPrice.location_id' => 'required|integer',
            'editingSellingPrice.selling_price' => 'required|numeric|min:0',
            'editingSellingPrice.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $productId = $this->selectedProduct->product_id;
        $locationId = $this->editingSellingPrice['location_id'];

        // Find the corresponding cost price to save with the selling price
        $costPrice = Price::where('product_id', $productId)
                          ->where('location_id', $locationId)
                          ->first();

        SellingPrice::updateOrCreate(
            [
                'product_id' => $productId,
                'location_id' => $locationId,
            ],
            [
                'selling_price' => $this->editingSellingPrice['selling_price'],
                'discount' => empty($this->editingSellingPrice['discount']) ? 0 : $this->editingSellingPrice['discount'],
                'average_price' => $costPrice->average_price ?? 0,
            ]
        );

        session()->flash('message', 'Harga jual berhasil disimpan.');
        $this->refreshPrices();
        $this->resetEditingFields();
    }

    public function confirmDelete($id)
    {
        $this->priceIdToDelete = $id;
        $this->showDeleteConfirmation = true;
    }

    public function cancelDelete()
    {
        $this->priceIdToDelete = null;
        $this->showDeleteConfirmation = false;
    }

    public function destroyConfirmedPrice()
    {
        SellingPrice::findOrFail($this->priceIdToDelete)->delete();
        session()->flash('message', 'Harga jual berhasil dihapus.');
        $this->refreshPrices();
        $this->priceIdToDelete = null;
        $this->showDeleteConfirmation = false;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'price-management-modal');
        $this->selectedProduct = null;
        $this->sellingPrices = [];
        $this->resetEditingFields();
    }

    public function resetEditingFields()
    {
        $this->sellingPriceId = null;
        $this->editingSellingPrice = ['location_id' => '', 'selling_price' => '', 'discount' => ''];
        $this->showDeleteConfirmation = false;
        $this->priceIdToDelete = null;
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
