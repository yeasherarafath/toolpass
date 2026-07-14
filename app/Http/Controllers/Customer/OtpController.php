<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Actions\Otp\ProvideOtpAction;
use App\Actions\Otp\RequestOtpAction;
use App\Models\OtpRequest;
use App\Models\UserToolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function request(Request $request)
    {
        $validated = $request->validate([
            'user_tool_access_id' => ['required', 'exists:user_tool_accesses,id'],
            'customer_message' => ['nullable', 'string', 'max:500'],
        ]);

        $access = UserToolAccess::where('user_id', Auth::id())
            ->findOrFail($validated['user_tool_access_id']);

        app(RequestOtpAction::class)->handle(Auth::user(), [
            'user_tool_access_id' => $access->id,
            'tool_id' => $access->tool_id,
            'tool_account_id' => $access->tool_account_id,
            'customer_message' => $validated['customer_message'] ?? null,
        ]);

        return back()->with('success', 'OTP request submitted.');
    }

    public function show(OtpRequest $otpRequest)
    {
        if ($otpRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $code = ProvideOtpAction::isViewable($otpRequest)
            ? ProvideOtpAction::decrypt($otpRequest)
            : null;

        return view('customer.otp.show', compact('otpRequest', 'code'));
    }
}
