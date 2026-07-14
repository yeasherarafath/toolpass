<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function admin()
    {
        return view('admin.dashboard');
    }

    public function staff()
    {
        return view('staff.dashboard');
    }

    public function customer()
    {
        return view('customer.dashboard');
    }
}
