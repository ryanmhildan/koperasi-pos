<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Product, SalesTransaction, SalesTransactionDetail, CashDrawer, Stock};
use Illuminate\Support\Str;

class PosKasir extends Component
{
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $paymentMethod = 'cash';
    public $cashDrawer;
    public $products;

    protected $rules = [
        'cart.*.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->cashDrawer = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();
            
        if (!$this->cashDrawer) {
            session()->flash('error', 'Silakan buka shift terlebih dahulu!');
        }
        
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $this->products = Product::with(['category', 'unit', 'stocks'])
            ->where('is_active', true)
            ->when($this->search, function($q) {
                $q->where('product_name', 'like', '%' . $this->search . '%')
                  ->orWhere('product_code', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->limit(20)
            ->get();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->product_name,
                'price' => $product->selling_price,
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

        if (!$this->cashDrawer) {
            session()->flash('error', 'Tidak ada shift aktif!');
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
            SalesTransactionDetail::create([
                'transaction_id' => $transaction->transaction_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'selling_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
            ]);

            // Update stock
            $stock = Stock::where('product_id', $item['product_id'])->first();
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
        return view('livewire.pos-kasir');
    }
}