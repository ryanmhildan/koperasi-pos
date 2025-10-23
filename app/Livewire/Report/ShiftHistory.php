<?php

namespace App\Livewire\Report;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashDrawer;
use App\Models\User;
use Carbon\Carbon;

class ShiftHistory extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $selectedUserId;
    public $users;

    public function mount()
    {
        $this->startDate = Carbon::today()->startOfMonth()->toDateString();
        $this->endDate = Carbon::today()->endOfMonth()->toDateString();
        $this->users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Kasir', 'Admin']);
        })->get();
    }

    public function render()
    {
        $shifts = CashDrawer::with('user')
            ->whereBetween('shift_date', [$this->startDate, $this->endDate])
            ->when($this->selectedUserId, function ($query) {
                $query->where('user_id', $this->selectedUserId);
            })
            ->latest('shift_start')
            ->paginate(10);

        return view('livewire.report.shift-history', [
            'shifts' => $shifts,
        ])->layout('layouts.app');
    }
}