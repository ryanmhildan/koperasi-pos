<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

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
        
        $simpananWallet = $user->getWallet('simpanan');
        if (!$simpananWallet) {
            $user->createWallet(['name' => 'simpanan', 'slug' => 'simpanan']);
            $simpananWallet = $user->getWallet('simpanan');
        }

        $simpananWallet->deposit($this->amount, ['description' => $this->description]);

        $this->reset(['amount', 'description']);
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Simpanan baru berhasil ditambahkan.',
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $simpananWallet = $user->getWallet('simpanan');

        if (!$simpananWallet) {
            // Return an empty paginator instance to prevent ->links() from crashing
            $transactions = new LengthAwarePaginator([], 0, 10);
            return view('livewire.my-simpanan', [
                'transactions' => $transactions,
                'wallet' => null,
            ]);
        }

        $transactions = $simpananWallet->transactions()->latest()->paginate(10);

        return view('livewire.my-simpanan', [
            'transactions' => $transactions,
            'wallet' => $simpananWallet,
        ]);
    }
}