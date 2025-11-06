<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Simpanan;

class WalletMigrateSimpanan extends Command
{
    protected $signature = 'wallet:migrate-simpanan';
    protected $description = 'Migrate existing simpanan data to the wallet system';

    public function handle()
    {
        $this->info('Starting simpanan migration...');

        $users = User::all();

        foreach ($users as $user) {
            $totalSimpanan = Simpanan::where('user_id', $user->user_id)->sum('amount');

            if ($totalSimpanan > 0) {
                $simpananWallet = $user->getOrCreateWallet('simpanan');

                if ($simpananWallet->balance() == 0) {
                    $transaction = $simpananWallet->deposit($totalSimpanan, null, ['description' => 'Migrasi saldo simpanan lama']);
                    $simpananWallet->confirmTransaction($transaction);
                    $this->info("Migrated {$totalSimpanan} for user {$user->full_name}");
                } else {
                    $this->line("Skipping user {$user->full_name}, wallet already has a balance.");
                }
            }
        }

        $this->info('Simpanan migration completed.');
    }
}