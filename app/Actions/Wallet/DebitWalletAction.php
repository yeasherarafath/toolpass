<?php

namespace App\Actions\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class DebitWalletAction
{
    public function handle(User $user, string $currency, float $amount, ?string $note = null, ?string $refType = null, ?int $refId = null): Wallet
    {
        if ($amount <= 0) {
            throw new \RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $currency, $amount, $note, $refType, $refId) {
            $wallet = Wallet::where('user_id', $user->id)
                ->where('currency', $currency)
                ->lockForUpdate()
                ->first();

            if (! $wallet || (float) $wallet->balance < $amount) {
                throw new \RuntimeException('Insufficient wallet balance.');
            }

            $wallet->balance = (float) $wallet->balance - $amount;
            $wallet->save();

            $wallet->walletTransactions()->create([
                'type' => 'debit',
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
