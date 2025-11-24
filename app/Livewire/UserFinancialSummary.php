<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UserFinancialSummary extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::permission('isAnggota')
            ->where(function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('nrp', 'like', '%' . $this->search . '%');
            })
            ->withSum(['pinjaman' => fn ($query) => $query->where('status', 'active')], 'loan_amount')
            ->with('wallets')
            ->orderBy('full_name')
            ->paginate(15);

        return view('livewire.user-financial-summary', [
            'users' => $users,
        ]);
    }
}
