<?php

namespace App\Actions\Otp;

use App\Events\Otp\OtpProvided;
use App\Models\OtpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class ProvideOtpAction
{
    public function handle(OtpRequest $request, User $admin, ?string $customerMessage = null): OtpRequest
    {
        if ($request->status === 'provided' || $request->status === 'used') {
            return $request;
        }

        $code = (string) random_int(100000, 999999);

        $request->otp_code_encrypted = Crypt::encrypt($code);
        $request->otp_expires_at = now()->addMinutes(5);
        $request->status = 'provided';
        $request->provided_by = $admin->id;
        $request->provided_at = now();
        $request->admin_note = $customerMessage;
        $request->save();

        event(new OtpProvided($request));

        return $request;
    }

    public static function decrypt(OtpRequest $request): ?string
    {
        if (! $request->otp_code_encrypted) {
            return null;
        }

        try {
            return Crypt::decrypt($request->otp_code_encrypted);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function isViewable(OtpRequest $request): bool
    {
        return $request->status === 'provided'
            && $request->otp_expires_at
            && $request->otp_expires_at->isFuture();
    }
}
