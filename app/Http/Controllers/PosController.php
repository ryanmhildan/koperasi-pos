<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|kasir']);
    }

    public function kasir()
    {
        $activeDrawer = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        return view('pos.kasir', compact('activeDrawer'));
    }

    public function products()
    {
        return view('pos.products');
    }

    public function stock()
    {
        return view('pos.stock');
    }

    public function grn()
    {
        return view('pos.grn');
    }
}