<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\User;

class PinjamanManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $pinjamanId;
    
    public $user_id, $loan_amount, $interest_rate, $tenor_months;
    public $loan_type, $loan_purpose, $loan_date, $status = 'active', $is_blocked = false;

    protected $rules = [
        'user_id' => 'required|exists:users,user_id',
        'loan_amount' => 'required|numeric|min:0',
        'interest_rate' => 'required|numeric|min:0',
        'tenor_months' => 'required|integer|min:1',
        'loan_type' => 'required|in:regular,emergency,business',
        'loan_purpose' => 'required|string',
        'loan_date' => 'required|date',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->loan_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $monthlyPayment = $this->calculateMonthlyPayment();
        
        $pinjaman = Pinjaman::create([
            'user_id' => $this->user_id,
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'tenor_months' => $this->tenor_months,
            'loan_type' => $this->loan_type,
            'loan_purpose' => $this->loan_purpose,
            'loan_date' => $this->loan_date,
            'status' => $this->status,
            'is_blocked' => $this->is_blocked,
            'remaining_balance' => $this->loan_amount,
        ]);

        // Generate angsuran schedule
        $this->generateAngsuranSchedule($pinjaman, $monthlyPayment);

        session()->flash('message', 'Pinjaman berhasil ditambahkan.');
        $this->closeModal();
    }

    private function calculateMonthlyPayment()
    {
        $principal = $this->loan_amount;
        $rate = $this->interest_rate / 100 / 12; // Monthly rate
        $months = $this->tenor_months;
        
        if ($rate > 0) {
            return $principal * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
        } else {
            return $principal / $months; // If no interest
        }
    }

    private function generateAngsuranSchedule($pinjaman, $monthlyPayment)
    {
        $dueDate = \Carbon\Carbon::parse($pinjaman->loan_date);
        
        for ($i = 1; $i <= $pinjaman->tenor_months; $i++) {
            $dueDate->addMonth();
            
            Angsuran::create([
                'pinjaman_id' => $pinjaman->pinjaman_id,
                'amount' => $monthlyPayment,
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => 'pending',
            ]);
        }
    }

    public function edit($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $this->pinjamanId = $id;
        $this->user_id = $pinjaman->user_id;
        $this->loan_amount = $pinjaman->loan_amount;
        $this->interest_rate = $pinjaman->interest_rate;
        $this->tenor_months = $pinjaman->tenor_months;
        $this->loan_type = $pinjaman->loan_type;
        $this->loan_purpose = $pinjaman->loan_purpose;
        $this->loan_date = $pinjaman->loan_date->format('Y-m-d');
        $this->status = $pinjaman->status;
        $this->is_blocked = $pinjaman->is_blocked;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $pinjaman = Pinjaman::findOrFail($this->pinjamanId);
        $pinjaman->update([
            'user_id' => $this->user_id,
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'tenor_months' => $this->tenor_months,
            'loan_type' => $this->loan_type,
            'loan_purpose' => $this->loan_purpose,
            'loan_date' => $this->loan_date,
            'status' => $this->status,
            'is_blocked' => $this->is_blocked,
        ]);

        session()->flash('message', 'Pinjaman berhasil diupdate.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Pinjaman::find($id)->delete();
        session()->flash('message', 'Pinjaman berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->user_id = '';
        $this->loan_amount = '';
        $this->interest_rate = '';
        $this->tenor_months = '';
        $this->loan_type = '';
        $this->loan_purpose = '';
        $this->loan_date = '';
        $this->status = 'active';
        $this->is_blocked = false;
        $this->editMode = false;
        $this->pinjamanId = null;
    }

    public function render()
    {
        $pinjaman = Pinjaman::with('user')
            ->whereHas('user', function($query) {
                $query->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%');
            })
            ->orWhere('loan_purpose', 'like', '%'.$this->search.'%')
            ->orderBy('loan_date', 'desc')
            ->paginate(10);

        $users = User::role('Anggota')->get();
        $loanTypes = ['regular' => 'Regular', 'emergency' => 'Emergency', 'business' => 'Business'];

        return view('livewire.pinjaman-management', compact('pinjaman', 'users', 'loanTypes'));
    }
}