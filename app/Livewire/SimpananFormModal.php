<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class SimpananFormModal extends Component
{
    public $isOpen = false;
    public $user_id;
    public $amount;
    public $transaction_date;
    public $description;

    public $users;
    public $modalTitle = 'Tambah Simpanan';

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,user_id',
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
        ];
    }

    public function mount()
    {
        // Removed role filtering to show all users
        $this->users = User::orderBy('full_name')->get();
        $this->transaction_date = now()->format('Y-m-d');
    }

    #[On('createSimpanan')]
    public function createSimpanan()
    {
        $this->resetForm();
        $this->modalTitle = 'Tambah Simpanan';
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $user = User::findOrFail($this->user_id);
            $simpananWallet = $user->getWallet('simpanan');
            if (!$simpananWallet) {
                $user->createWallet(['name' => 'Simpanan', 'slug' => 'simpanan']);
                $simpananWallet = $user->getWallet('simpanan');
            }
            
            // We only create new deposits. Editing/deleting transactions is not allowed.
            $meta = [
                'description' => $this->description,
                'transaction_date' => $this->transaction_date
            ];
            $simpananWallet->deposit($this->amount, $meta);
            session()->flash('message', 'Data simpanan berhasil ditambahkan.');
        });

        $this->closeModal();
        $this->dispatch('simpananSaved');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->user_id = null;
        $this->amount = null;
        $this->description = '';
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.simpanan-form-modal');
    }
}