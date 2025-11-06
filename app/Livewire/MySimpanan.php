<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MySimpanan extends Component
{
    use WithPagination;

    public $amount;
    public $description;

    protected $rules = [
        'amount' => 'required|numeric|min:10000',
        'description' => 'nullable|string|max:255',
    ];

    public function saveSimpanan()
    {
        $this->validate();

        $user = Auth::user();
        $simpananWallet = $user->getOrCreateWallet('simpanan');

        $transaction = $simpananWallet->deposit($this->amount, null, ['description' => $this->description]);
        $simpananWallet->confirmTransaction($transaction);

        Simpanan::create([
            'user_id' => $user->user_id,
            'amount' => $this->amount,
            'transaction_date' => now(),
            'description' => $this->description,
        ]);

        $this->reset(['amount', 'description']);
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Simpanan baru berhasil ditambahkan.',
        ]);
    }

    public function render()
    {
        $simpanan = Simpanan::where('user_id', Auth::id())->latest()->paginate(10);
        return view('livewire.my-simpanan', [
            'simpanan' => $simpanan,
        ]);
    }
}
