<?php

namespace App\Actions\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class CreditWalletAction
{
    public function handle(User $user, string $currency, float $amount, ?string $note = null, ?string $refType = null, ?int $refId = null): Wallet
    {
        return DB::transaction(function () use ($user, $currency, $amount, $note, $refType, $refId) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id, 'currency' => $currency],
                ['balance' => 0, 'locked_balance' => 0]
            );

            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
            $wallet->balance = (float) $wallet->balance + $amount;
            $wallet->save();

            $wallet->walletTransactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'note' => $note,
            ]);

            return $wallet;
        });
    }
}
