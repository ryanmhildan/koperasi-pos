<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Product;
use App\Models\Location;
use App\Models\GoodReceiptNote;
use App\Models\GrnDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Price;
use Illuminate\Support\Facades\DB;

class GrnCreate extends Component
{
    // Header fields
    public $location_id;
    public $receipt_date;
    public $reference_number;
    public $notes;

    // Line items
    public $items = [];

    // Product search
    public $product_search = '';
    public $searched_products = [];

    public $locations = [];

    protected $rules = [
        'location_id' => 'required|exists:locations,location_id',
        'receipt_date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->locations = Location::where('is_active', true)->get();
        $this->receipt_date = today()->format('Y-m-d');
    }

    public function updatedProductSearch()
    {
        if (strlen($this->product_search) < 2) {
            $this->searched_products = [];
            return;
        }

        $this->searched_products = Product::where('is_active', true)
            ->where(function ($query) {
                $query->where('product_name', 'like', '%' . $this->product_search . '%')
                      ->orWhere('product_code', 'like', '%' . $this->product_search . '%');
            })
            ->limit(5)
            ->get();
    }

    public function addProduct(Product $product)
    {
        if (isset($this->items[$product->product_id])) {
            $this->items[$product->product_id]['quantity']++;
        } else {
            $this->items[$product->product_id] = [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'quantity' => 1,
                'price' => 0, // Default cost price
            ];
        }
        $this->product_search = '';
        $this->searched_products = [];
    }

    public function removeItem($productId)
    {
        unset($this->items[$productId]);
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            // Create GRN Header
            $grn = GoodReceiptNote::create([
                'location_id' => $this->location_id,
                'receipt_date' => $this->receipt_date,
                'reference_number' => $this->reference_number,
            ]);

            foreach ($this->items as $item) {
                // Create GRN Detail
                $grn->details()->create([
                    'product_id' => $item['product_id'],
                    'location_id' => $this->location_id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                // --- Weighted Average Cost Calculation ---
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item['product_id'], 'location_id' => $this->location_id],
                    ['current_stock' => 0]
                );
                $price = Price::firstOrCreate(
                    ['product_id' => $item['product_id'], 'location_id' => $this->location_id],
                    ['average_price' => 0]
                );

                $current_stock = $stock->current_stock;
                $current_avg_price = $price->average_price;
                $new_qty = $item['quantity'];
                $new_price = $item['price'];
                $total_stock = $current_stock + $new_qty;

                if ($total_stock > 0) {
                    $new_avg_price = (($current_stock * $current_avg_price) + ($new_qty * $new_price)) / $total_stock;
                } else {
                    $new_avg_price = $new_price;
                }

                $price->average_price = $new_avg_price;
                $price->save();
                // --- End of Calculation ---

                // Update Stock Level
                $stock->increment('current_stock', $item['quantity']);
                $stock->touch('last_updated');

                // Create Stock Movement Record
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'location_id' => $this->location_id,
                    'movement_type' => 'in', // Corrected based on ERD
                    'quantity' => $item['quantity'],
                    'average_price' => $new_avg_price, // Store the new average cost
                    'reference_type' => 'purchase', // Corrected based on ERD
                    'reference_id' => $grn->grn_id,
                    'movement_date' => $this->receipt_date,
                    'created_by' => auth()->id(),
                ]);
            }
        });

        session()->flash('message', 'GRN berhasil dibuat.');
        return redirect()->route('pos.grn.index');
    }

    public function render()
    {
        return view('livewire.grn-create');
    }
}
