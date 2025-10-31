<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Product, SalesTransaction, SalesTransactionDetail, CashDrawer, Stock, Price, Location, SellingPrice, StockMovement};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PosKasir extends Component
{
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $cashReceived = 0;
    public $change = 0;
    public $cashDrawer;
    public $products;
    public $customer_search = '';
    public $searched_customers = [];
    public $selected_customer = null;
    public $customer_credit_info = null;

    public $confirmingTransaction = false;
    public $transactionType = '';
    public $confirmingCloseShift = false;
    public $showTransactionHistoryModal = false; // New property

    // Properties from CashDrawer
    public $opening_balance = 0;
    public $location_id;
    public $locations = [];

    protected $listeners = ['transactionVoided' => '$refresh']; // Listen for voided event from child component

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
    }

    public function openTransactionHistoryModal()
    {
        $this->showTransactionHistoryModal = true;
        $this->dispatch('open-modal', 'transaction-history-modal');
    }

    public function closeTransactionHistoryModal()
    {
        $this->showTransactionHistoryModal = false;
        $this->dispatch('close-modal', 'transaction-history-modal');
    }

    public function updatedCustomerSearch($value)
    {
        if (strlen($value) < 2) {
            $this->searched_customers = [];
            return;
        }

        $this->searched_customers = \App\Models\User::where(function ($query) use ($value) {
            $query->where('full_name', 'like', '%'.$value.'%')
                  ->orWhere('nrp', 'like', '%'.$value.'%');
        })
        ->whereHas('roles', function ($q) {
            $q->where('name', 'Anggota');
        })
        ->limit(5)
        ->get();
    }

    public function selectCustomer($userId)
    {
        $this->selected_customer = \App\Models\User::with('creditCards')->find($userId);
        $this->customer_search = '';
        $this->searched_customers = [];

        $card = $this->selected_customer->creditCards->where('is_active', true)->first();
        if ($card) {
            $availableCredit = $card->credit_limit - $card->current_balance;
            $this->customer_credit_info = 'Sisa Limit: Rp ' . number_format($availableCredit, 0, ',', '.');
        } else {
            $this->customer_credit_info = 'Tidak ada kartu kredit aktif.';
        }
    }

    public function clearCustomer()
    {
        $this->selected_customer = null;
        $this->customer_credit_info = null;
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
        $this->loadProducts();
    }

    public function confirmCloseShift()
    {
        $this->confirmingCloseShift = true;
        $this->dispatch('open-modal', 'confirm-close-shift');
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
        $this->confirmingCloseShift = false;
        $this->dispatch('close-modal', 'confirm-close-shift');
    }

    public function loadProducts()
    {
        if (!$this->cashDrawer) {
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

        // Convert the Eloquent collection to a plain array to prevent hydration issues
        $this->products = $products->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'category_name' => $product->category->name ?? '',
                'location_selling_price' => $this->getSellingPriceForProduct($product->product_id, $product->selling_price),
                'barcode' => $product->barcode, // Ensure barcode is included for keyboard navigation
                'product_code' => $product->product_code, // Ensure product_code is included
            ];
        })->toArray();
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

    public function addToCart($productId, $price = null)
    {
        $product = Product::find($productId);
        $currentQuantityInCart = $this->cart[$productId]['quantity'] ?? 0;

        if (!$this->checkStock($productId, $currentQuantityInCart + 1)) {
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            // If price is not passed, fetch it as a fallback for safety.
            $finalPrice = $price ?? $this->getSellingPriceForProduct($product->product_id, $product->selling_price);
            $this->cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->product_name,
                'price' => $finalPrice,
                'quantity' => 1,
            ];
        }

        $this->calculateTotal();
    }

    public function increaseQuantity($productId)
    {
        if (isset($this->cart[$productId])) {
            $newQuantity = $this->cart[$productId]['quantity'] + 1;
            
            if (!$this->checkStock($productId, $newQuantity)) {
                return;
            }
            
            $this->cart[$productId]['quantity'] = $newQuantity;
            $this->calculateTotal();
        }
    }

    public function decreaseQuantity($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']--;
            if ($this->cart[$productId]['quantity'] <= 0) {
                unset($this->cart[$productId]);
            }
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        $this->calculateChange();
    }

    public function updatedCashReceived($value)
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $cash = floatval($this->cashReceived);
        if ($cash > 0) {
            $this->change = $cash - $this->total;
        } else {
            $this->change = 0;
        }
    }

    public function confirmTransaction($type)
    {
        $this->transactionType = $type;
        $this->confirmingTransaction = true;
        $this->dispatch('open-modal', 'confirm-transaction');
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

        // Credit Card specific logic
        if ($this->transactionType === 'credit_card') {
            if (!$this->selected_customer) {
                session()->flash('error', 'Pilih pelanggan untuk transaksi kartu kredit.');
                return;
            }

            $card = $this->selected_customer->creditCards->where('is_active', true)->first();

            if (!$card) {
                session()->flash('error', 'Pelanggan tidak memiliki kartu kredit yang aktif.');
                return;
            }

            $availableCredit = $card->credit_limit - $card->current_balance;
            if ($this->total > $availableCredit) {
                session()->flash('error', 'Limit kredit pelanggan tidak mencukupi. Sisa limit: Rp ' . number_format($availableCredit, 0, ',', '.'));
                return;
            }
        }

        $transaction = SalesTransaction::create([
            'transaction_number' => 'TRX-' . date('Ymd') . '-' . Str::random(6),
            'user_id' => $this->selected_customer->user_id ?? null, // Use selected customer if available, otherwise null
            'cashier_id' => auth()->id(),
            'drawer_id' => $this->cashDrawer->drawer_id,
            'transaction_date' => today(),
            'transaction_time' => now()->format('H:i:s'),
            'sub_total' => $this->total,
            'discount' => 0,
            'total_amount' => $this->total,
            'payment_method' => $this->transactionType,
            'status' => 'completed',
            'card_id' => ($this->transactionType === 'credit_card' && isset($card)) ? $card->card_id : null,
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

                // Create Stock Movement Record for sales
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'location_id' => $this->cashDrawer->location_id,
                    'movement_type' => 'out',
                    'quantity' => $item['quantity'],
                    'average_price' => $costPrice, // The cost price at the time of sale
                    'reference_type' => 'sales',
                    'reference_id' => $transaction->transaction_id,
                    'movement_date' => today(),
                    'created_by' => auth()->id(),
                ]);
            }
        }

        // Update cash drawer only for cash transactions
        if ($this->transactionType === 'cash') {
            $this->cashDrawer->increment('total_sales', $this->total);
        } elseif ($this->transactionType === 'credit_card' && isset($card)) {
            $card->increment('current_balance', $this->total);
        }

        $this->reset(['cart', 'total', 'search', 'cashReceived', 'change', 'selected_customer']);
        $this->loadProducts();
        
        session()->flash('success', 'Transaksi berhasil! No: ' . $transaction->transaction_number);
        $this->confirmingTransaction = false;
        $this->dispatch('close-modal', 'confirm-transaction');
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

        if ($this->products === null && $this->cashDrawer) {
            $this->loadProducts();
        }

        return view('livewire.pos-kasir');
    }
}