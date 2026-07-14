<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Device\ApproveDeviceAction;
use App\Actions\Device\ResetDeviceAction;
use App\Models\UserToolDevice;
use App\Models\DeviceResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function approve(UserToolDevice $device)
    {
        app(ApproveDeviceAction::class)->handle($device, Auth::id());

        return back()->with('success', 'Device approved.');
    }

    public function completeReset(DeviceResetRequest $request)
    {
        app(ResetDeviceAction::class)->handle($request, Auth::id());

        return back()->with('success', 'Device reset completed.');
    }
}
