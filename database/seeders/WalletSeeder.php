<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletCredited;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $walletService = app(WalletService::class);

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please create users first.');
            return;
        }

        foreach ($users as $user) {
            // Determine funding based on role
            $amount = $user->hasRole('superadmin') || $user->hasRole('administrator')
                ? 50000
                : 100000;

            // Use quiet method — credits wallet but does NOT send email
            $tx = $walletService->creditUserQuietly(
                $user,
                $amount,
                'Initial funding via WalletSeeder'
            );


            Mail::to($user->email)->send(
                new WalletCredited(
                    user: $user,
                    amount: $amount,
                    balance: $tx->balance_after,
                    reason: 'Welcome! Your wallet has been funded.'
                )
            );


            $this->command->info("Credited ₦" . number_format($amount) . " to {$user->name}'s wallet ({$user->email}) → New balance: ₦" . number_format($tx->balance_after));
        }

        $this->command->info('All users have been successfully funded!');
    }
}
