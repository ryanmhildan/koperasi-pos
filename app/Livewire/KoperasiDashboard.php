<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Bavix\Wallet\Interfaces\Wallet;
use Livewire\WithPagination;

class KoperasiDashboard extends Component
{
    use WithPagination;

    public $totalKoperasiBalance;
    protected $paginationTheme = 'tailwind';

    // Deposit properties
    public $depositAmount;
    public $depositDescription;

    // Withdraw properties
    public $withdrawAmount;
    public $withdrawDescription;

    protected $listeners = ['transactionConfirmed' => 'handleTransaction'];

    protected function rules()
    {
        return [
            'depositAmount' => 'required|numeric|min:1',
            'depositDescription' => 'required|string|max:255',
            'withdrawAmount' => 'required|numeric|min:1',
            'withdrawDescription' => 'required|string|max:255',
        ];
    }
    
    public function mount()
    {
        $this->refreshBalance();
    }

    private function refreshBalance()
    {
        $koperasiWallet = $this->getKoperasiWallet();
        $this->totalKoperasiBalance = $koperasiWallet ? $koperasiWallet->balance : 0;
    }

    private function getKoperasiWallet(): ?Wallet
    {
        $koperasiUser = User::where('username', 'koperasi')->first();
        if ($koperasiUser) {
            return $koperasiUser->getWallet('utama');
        }
        return null;
    }

    public function confirmDeposit()
    {
        $this->validate([
            'depositAmount' => 'required|numeric|min:1',
            'depositDescription' => 'required|string|max:255',
        ]);
        $this->dispatch('openKoperasiTransactionModal', 
            action: 'deposit', 
            amount: $this->depositAmount, 
            description: $this->depositDescription
        );
    }

    public function confirmWithdraw()
    {
        $this->validate([
            'withdrawAmount' => 'required|numeric|min:1',
            'withdrawDescription' => 'required|string|max:255',
        ]);
        $this->dispatch('openKoperasiTransactionModal', 
            action: 'withdraw', 
            amount: $this->withdrawAmount, 
            description: $this->withdrawDescription
        );
    }

    public function handleTransaction($action)
    {
        if ($action === 'deposit') {
            $this->executeDeposit();
        } elseif ($action === 'withdraw') {
            $this->executeWithdraw();
        }
    }

    public function executeDeposit()
    {
        try {
            DB::transaction(function () {
                $koperasiWallet = $this->getKoperasiWallet();
                if (!$koperasiWallet) {
                    throw new \Exception('Wallet utama koperasi tidak ditemukan.');
                }
                
                $meta = [
                    'description' => $this->depositDescription,
                    'action_by_user_id' => Auth::id(),
                    'action_by_user_name' => Auth::user()->full_name,
                ];

                $koperasiWallet->deposit($this->depositAmount, $meta);
                
                session()->flash('message', 'Deposit berhasil.');
                $this->reset(['depositAmount', 'depositDescription']);
                $this->refreshBalance();
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal melakukan deposit: ' . $e->getMessage());
        }
    }

    public function executeWithdraw()
    {
        try {
            DB::transaction(function () {
                $koperasiWallet = $this->getKoperasiWallet();
                if (!$koperasiWallet) {
                    throw new \Exception('Wallet utama koperasi tidak ditemukan.');
                }

                if ($koperasiWallet->balance < $this->withdrawAmount) {
                    throw new \Exception('Saldo tidak mencukupi untuk melakukan penarikan.');
                }

                $meta = [
                    'description' => $this->withdrawDescription,
                    'action_by_user_id' => Auth::id(),
                    'action_by_user_name' => Auth::user()->full_name,
                ];

                $koperasiWallet->withdraw($this->withdrawAmount, $meta);

                session()->flash('message', 'Penarikan berhasil.');
                $this->reset(['withdrawAmount', 'withdrawDescription']);
                $this->refreshBalance();
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal melakukan penarikan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $transactions = collect();
        $koperasiWallet = $this->getKoperasiWallet();
        if ($koperasiWallet) {
            $transactions = $koperasiWallet->transactions()->latest()->paginate(10);
        }

        return view('livewire.koperasi-dashboard', [
            'transactions' => $transactions
        ]);
    }
}
