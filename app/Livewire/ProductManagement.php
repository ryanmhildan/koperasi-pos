<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;

class ProductManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $productId;
    
    public $product_code, $barcode, $product_name, $category_id, $unit_id;
    public $selling_price, $is_stock_item = true, $product_type = 'retail';
    public $minimum_stock = 0, $track_expiry = false, $is_active = true;

    protected $rules = [
        'product_code' => 'required|unique:products,product_code',
        'product_name' => 'required',
        'category_id' => 'required|exists:categories,category_id',
        'unit_id' => 'required|exists:units,unit_id',
        'selling_price' => 'required|numeric|min:0',
        'product_type' => 'required|in:retail,catering_package,service',
        'minimum_stock' => 'required|integer|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Product::create([
            'product_code' => $this->product_code,
            'barcode' => $this->barcode,
            'product_name' => $this->product_name,
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'selling_price' => $this->selling_price,
            'is_stock_item' => $this->is_stock_item,
            'product_type' => $this->product_type,
            'minimum_stock' => $this->minimum_stock,
            'track_expiry' => $this->track_expiry,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Produk berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $id;
        $this->product_code = $product->product_code;
        $this->barcode = $product->barcode;
        $this->product_name = $product->product_name;
        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;
        $this->selling_price = $product->selling_price;
        $this->is_stock_item = $product->is_stock_item;
        $this->product_type = $product->product_type;
        $this->minimum_stock = $product->minimum_stock;
        $this->track_expiry = $product->track_expiry;
        $this->is_active = $product->is_active;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['product_code'] = 'required|unique:products,product_code,' . $this->productId . ',product_id';
        
        $this->validate($rules);

        $product = Product::findOrFail($this->productId);
        $product->update([
            'product_code' => $this->product_code,
            'barcode' => $this->barcode,
            'product_name' => $this->product_name,
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'selling_price' => $this->selling_price,
            'is_stock_item' => $this->is_stock_item,
            'product_type' => $this->product_type,
            'minimum_stock' => $this->minimum_stock,
            'track_expiry' => $this->track_expiry,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Produk berhasil diupdate.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        session()->flash('message', 'Produk berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->product_code = '';
        $this->barcode = '';
        $this->product_name = '';
        $this->category_id = '';
        $this->unit_id = '';
        $this->selling_price = '';
        $this->is_stock_item = true;
        $this->product_type = 'retail';
        $this->minimum_stock = 0;
        $this->track_expiry = false;
        $this->is_active = true;
        $this->editMode = false;
        $this->productId = null;
    }

    public function render()
    {
        $products = Product::with(['category', 'unit'])
            ->where(function($query) {
                $query->where('product_name', 'like', '%'.$this->search.'%')
                      ->orWhere('product_code', 'like', '%'.$this->search.'%')
                      ->orWhere('barcode', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        $categories = Category::where('is_active', true)->get();
        $units = Unit::all();
        $productTypes = [
            'retail' => 'Retail',
            'catering_package' => 'Catering Package',
            'service' => 'Service'
        ];

        return view('livewire.product-management', compact('products', 'categories', 'units', 'productTypes'));
    }
}