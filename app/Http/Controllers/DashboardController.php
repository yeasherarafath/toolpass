<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Actions\Dashboard\ResolveAdminWidgets;
use App\Actions\Dashboard\ResolveCustomerWidgets;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function admin()
    {
        $widgets = app(ResolveAdminWidgets::class)->handle();

        return view('admin.dashboard', compact('widgets'));
    }

    public function staff()
    {
        $widgets = app(ResolveAdminWidgets::class)->handle();

        return view('staff.dashboard', compact('widgets'));
    }

    public function customer()
    {
        $widgets = app(ResolveCustomerWidgets::class)->handle(Auth::id());

        return view('customer.dashboard', compact('widgets'));
    }
}
