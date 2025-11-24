<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MyPinjaman extends Component
{
    use WithPagination;

    public $loan_amount;
    public $tenor_months;
    public $loan_purpose;
    public $loan_type = 'biasa';

    protected $rules = [
        'loan_amount' => 'required|numeric|min:100000',
        'tenor_months' => 'required|integer|min:1',
        'loan_purpose' => 'required|string|max:255',
        'loan_type' => 'required|in:biasa,khusus,darurat',
    ];

    public function requestPinjaman()
    {
        $this->validate();

        $loanTypeMapping = [
            'biasa' => 'regular',
            'khusus' => 'business',
            'darurat' => 'emergency',
        ];

        Pinjaman::create([
            'user_id' => Auth::id(),
            'loan_amount' => $this->loan_amount,
            'interest_rate' => 1.5, // Default interest rate
            'tenor_months' => $this->tenor_months,
            'loan_type' => $loanTypeMapping[$this->loan_type],
            'loan_purpose' => $this->loan_purpose,
            'loan_date' => now(),
            'status' => 'pending',
            'remaining_balance' => $this->loan_amount,
        ]);

        $this->reset(['loan_amount', 'tenor_months', 'loan_purpose', 'loan_type']);
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pengajuan pinjaman berhasil dikirim. Mohon tunggu persetujuan dari admin.',
        ]);
    }

    public function render()
    {
        $pinjaman = Pinjaman::where('user_id', Auth::id())->latest()->paginate(10);
        return view('livewire.my-pinjaman', [
            'pinjaman' => $pinjaman,
        ]);
    }
}
