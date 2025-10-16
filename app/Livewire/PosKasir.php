<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Product, SalesTransaction, SalesTransactionDetail, CashDrawer, Stock, Price, Location, SellingPrice};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PosKasir extends Component
{
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $paymentMethod = 'cash';
    public $cashDrawer;
    public $products;

    // Properties from CashDrawer
    public $opening_balance = 0;
    public $location_id;
    public $locations = [];

    protected function rules()
    {
        return [
            'cart.*.quantity' => 'required|integer|min:1',
            'opening_balance' => 'required|numeric|min:0',
            'location_id' => 'required|exists:locations,location_id',
        ];
    }

    public function mount()
    {
        $this->locations = Location::where('is_active', true)->get();
        $this->loadProducts();
    }

    public function openShift()
    {
        $this->validate([
            'opening_balance' => 'required|numeric|min:0',
            'location_id' => 'required|exists:locations,location_id',
        ]);

        if ($this->cashDrawer) {
            session()->flash('error', 'Anda sudah memiliki shift yang aktif.');
            return;
        }

        $this->cashDrawer = CashDrawer::create([
            'user_id' => Auth::id(),
            'location_id' => $this->location_id,
            'opening_balance' => $this->opening_balance,
            'shift_date' => today(),
            'shift_start' => now(),
            'status' => 'open',
        ]);

        session()->flash('success', 'Shift berhasil dibuka. Selamat bekerja!');
        $this->reset('opening_balance', 'location_id');
    }

    public function closeShift()
    {
        if (!$this->cashDrawer) {
            session()->flash('error', 'Tidak ada shift yang aktif untuk ditutup.');
            return;
        }

        $totalSales = SalesTransaction::where('drawer_id', $this->cashDrawer->drawer_id)
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $closing_balance = $this->cashDrawer->opening_balance + $totalSales;

        $this->cashDrawer->update([
            'shift_end' => now(),
            'closing_balance' => $closing_balance,
            'status' => 'closed',
        ]);

        session()->flash('success', 'Shift berhasil ditutup.');
        $this->cashDrawer = null; // Refresh the active drawer status
    }

    public function loadProducts()
    {
        if (!$this->cashDrawer) {
            $this->products = collect();
            return;
        }

        $products = Product::with(['category', 'unit'])
            ->where('is_active', true)
            ->whereHas('stocks', function ($query) {
                $query->where('location_id', $this->cashDrawer->location_id)
                      ->where('current_stock', '>', 0);
            })
            ->when($this->search, function($q) {
                $q->where('product_name', 'like', '%' . $this->search . '%')
                  ->orWhere('product_code', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->limit(20)
            ->get();

        // Attach the location-specific price to each product for display
        $products->each(function ($product) {
            $product->location_selling_price = $this->getSellingPriceForProduct($product->product_id, $product->selling_price);
        });

        $this->products = $products;
    }

    private function getSellingPriceForProduct($productId, $defaultPrice)
    {
        if (!$this->cashDrawer) {
            return $defaultPrice;
        }

        $sellingPrice = SellingPrice::where('product_id', $productId)
            ->where('location_id', $this->cashDrawer->location_id)
            ->first();

        return $sellingPrice->selling_price ?? $defaultPrice;
    }

    private function checkStock($productId, $quantity)
    {
        $stock = Stock::where('product_id', $productId)
                      ->where('location_id', $this->cashDrawer->location_id)
                      ->first();

        if (!$stock || $stock->current_stock < $quantity) {
            session()->flash('error', 'Stok tidak mencukupi untuk produk ini.');
            return false;
        }

        return true;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        $currentQuantityInCart = $this->cart[$productId]['quantity'] ?? 0;

        if (!$this->checkStock($productId, $currentQuantityInCart + 1)) {
            return;
        }
        
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $price = $this->getSellingPriceForProduct($product->product_id, $product->selling_price);
            $this->cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->product_name,
                'price' => $price,
                'quantity' => 1,
            ];
        }
        
        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if (!$this->checkStock($productId, $quantity)) {
            // Revert the quantity in the cart to its previous value if stock is insufficient
            $this->cart[$productId]['quantity'] = $this->cart[$productId]['quantity'];
            return;
        }
        
        $this->cart[$productId]['quantity'] = $quantity;
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function processTransaction()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        if (!$this->cashDrawer || !$this->cashDrawer->location_id) {
            session()->flash('error', 'Tidak ada shift/lokasi aktif!');
            return;
        }

        $transaction = SalesTransaction::create([
            'transaction_number' => 'TRX-' . date('Ymd') . '-' . Str::random(6),
            'user_id' => auth()->id(),
            'cashier_id' => auth()->id(),
            'drawer_id' => $this->cashDrawer->drawer_id,
            'transaction_date' => today(),
            'transaction_time' => now()->format('H:i:s'),
            'sub_total' => $this->total,
            'discount' => 0,
            'total_amount' => $this->total,
            'payment_method' => $this->paymentMethod,
            'status' => 'completed',
        ]);

        foreach ($this->cart as $item) {
            // Fetch the cost price for the product at the drawer's location
            $costPrice = Price::where('product_id', $item['product_id'])
                              ->where('location_id', $this->cashDrawer->location_id)
                              ->value('average_price');

            SalesTransactionDetail::create([
                'transaction_id' => $transaction->transaction_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'selling_price' => $item['price'],
                'average_price' => $costPrice, // Save the cost price
                'total_price' => $item['price'] * $item['quantity'],
            ]);

            // Update stock
            $stock = Stock::where('product_id', $item['product_id'])
                          ->where('location_id', $this->cashDrawer->location_id)
                          ->first();
            if ($stock) {
                $stock->decrement('current_stock', $item['quantity']);
            }
        }

        // Update cash drawer
        $this->cashDrawer->increment('total_sales', $this->total);

        $this->reset(['cart', 'total', 'search']);
        $this->loadProducts();
        
        session()->flash('success', 'Transaksi berhasil! No: ' . $transaction->transaction_number);
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'search') {
            $this->loadProducts();
        }
    }

    public function render()
    {
        $this->cashDrawer = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        // If a drawer is found but it has no location, treat it as invalid.
        if ($this->cashDrawer && !$this->cashDrawer->location_id) {
            $this->cashDrawer = null;
            session()->flash('error', 'Shift Anda saat ini tidak memiliki lokasi. Harap tutup shift dan buka yang baru dengan memilih lokasi.');
        }

        $this->loadProducts();
        return view('livewire.pos-kasir');
    }
}