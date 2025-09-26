<?php
// app/Livewire/Dashboard.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\SalesTransaction;
use App\Models\Stock;
use App\Models\Product;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard');
    }
}