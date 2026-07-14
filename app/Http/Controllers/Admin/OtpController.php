<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Otp\ProvideOtpAction;
use App\Models\OtpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function provide(Request $request, OtpRequest $otpRequest)
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        app(ProvideOtpAction::class)->handle($otpRequest, Auth::user(), $validated['admin_note'] ?? null);

        return back()->with('success', 'OTP provided.');
    }
}
