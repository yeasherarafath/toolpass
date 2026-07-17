<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::query()->with('tenant')->orderBy('name')->paginate(25);

        return view('platform.admin.owners.index', compact('owners'));
    }

    public function create()
    {
        $tenants = Tenant::getCached(null, 'id', ['*'], 3600);

        return view('platform.admin.owners.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:central.owners,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'business_name' => ['nullable', 'string', 'max:190'],
            'tenant_id' => ['nullable', 'exists:central.tenants,id'],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();

        Owner::create($data);

        return redirect()->route('admin.owners.index')->with('status', 'Owner created.');
    }

    public function edit(Owner $owner)
    {
        $tenants = Tenant::getCached(null, 'id', ['*'], 3600);

        return view('platform.admin.owners.edit', compact('owner', 'tenants'));
    }

    public function update(Request $request, Owner $owner)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:central.owners,email,' . $owner->getKey()],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'business_name' => ['nullable', 'string', 'max:190'],
            'tenant_id' => ['nullable', 'exists:central.tenants,id'],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $owner->update($data);

        return redirect()->route('admin.owners.index')->with('status', 'Owner updated.');
    }

    public function destroy(Owner $owner)
    {
        $owner->delete();

        return redirect()->route('admin.owners.index')->with('status', 'Owner deleted.');
    }
}
