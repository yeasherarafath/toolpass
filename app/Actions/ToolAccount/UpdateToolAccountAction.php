<?php

namespace App\Actions\ToolAccount;

use App\Models\ToolAccount;
use Illuminate\Support\Facades\Crypt;

class UpdateToolAccountAction
{
    public function __invoke(ToolAccount $account, array $data): ToolAccount
    {
        if (($data['login_password'] ?? null)) {
            $data['login_password_encrypted'] = Crypt::encryptString($data['login_password']);
        }
        if (($data['two_factor_secret'] ?? null)) {
            $data['two_factor_secret_encrypted'] = Crypt::encryptString($data['two_factor_secret']);
        }
        if (($data['backup_codes'] ?? null)) {
            $data['backup_codes_encrypted'] = Crypt::encryptString($data['backup_codes']);
        }

        unset($data['login_password'], $data['two_factor_secret'], $data['backup_codes']);

        $account->update($data);

        return $account;
    }
}
