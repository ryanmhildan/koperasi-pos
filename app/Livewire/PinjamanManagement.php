<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pinjaman;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class PinjamanManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';
    public $loanTypeFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function create()
    {
        $this->dispatch('createPinjaman');
    }

    public function edit($id)
    {
        $this->dispatch('editPinjaman', id: $id);
    }

    public function requestApproval($pinjamanId)
    {
        $this->dispatch('openApprovalModal', pinjamanId: $pinjamanId);
    }
    
    public function requestDeletion($pinjamanId)
    {
        $this->dispatch('openDeleteModal', pinjamanId: $pinjamanId);
    }

    #[On('pinjamanSaved')]
    #[On('loanApproved')]
    #[On('loanDeleted')]
    #[On('loanDeletionFailed')] // Listen for failure to show session message
    public function render()
    {
        $pinjaman = Pinjaman::with('user')
            ->where(function ($query) {
                $query->whereHas('user', function($q) {
                    $q->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%');
                })
                ->orWhere('loan_purpose', 'like', '%'.$this->search.'%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->loanTypeFilter, function ($query) {
                $query->where('loan_type', $this->loanTypeFilter);
            })
            ->orderBy('loan_date', 'desc')
            ->paginate(10);

        $loanTypes = ['regular' => 'Regular', 'emergency' => 'Emergency', 'business' => 'Business'];

        return view('livewire.pinjaman-management', compact('pinjaman', 'loanTypes'));
    }
}