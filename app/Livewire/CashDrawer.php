<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashDrawer as CashDrawerModel;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class CashDrawer extends Component
{
    public $activeDrawer;
    public $opening_balance = 0;
    public $location_id;
    public $locations = [];

    protected $rules = [
        'opening_balance' => 'required|numeric|min:0',
        'location_id' => 'required|exists:locations,location_id',
    ];

    public function mount()
    {
        $this->locations = Location::where('is_active', true)->get();
        $this->loadActiveDrawer();
    }

    public function loadActiveDrawer()
    {
        $this->activeDrawer = CashDrawerModel::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();
    }

    public function openShift()
    {
        $this->validate();

        if ($this->activeDrawer) {
            session()->flash('error', 'Anda sudah memiliki shift yang aktif.');
            return;
        }

        CashDrawerModel::create([
            'user_id' => Auth::id(),
            'location_id' => $this->location_id,
            'opening_balance' => $this->opening_balance,
            'shift_date' => today(),
            'shift_start' => now(),
            'status' => 'open',
        ]);

        session()->flash('success', 'Shift berhasil dibuka. Selamat bekerja!');
        return redirect()->route('pos.kasir');
    }

    public function render()
    {
        return view('livewire.cash-drawer');
    }
}
