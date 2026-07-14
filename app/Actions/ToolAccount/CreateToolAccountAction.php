<?php

namespace App\Actions\ToolAccount;

use App\Models\ToolAccount;
use Illuminate\Support\Facades\Crypt;

class CreateToolAccountAction
{
    public function __invoke(array $data): ToolAccount
    {
        $data['login_password_encrypted'] = ($data['login_password'] ?? null)
            ? Crypt::encryptString($data['login_password'])
            : null;
        $data['two_factor_secret_encrypted'] = ($data['two_factor_secret'] ?? null)
            ? Crypt::encryptString($data['two_factor_secret'])
            : null;
        $data['backup_codes_encrypted'] = ($data['backup_codes'] ?? null)
            ? Crypt::encryptString($data['backup_codes'])
            : null;

        unset($data['login_password'], $data['two_factor_secret'], $data['backup_codes']);

        return ToolAccount::create($data);
    }
}
