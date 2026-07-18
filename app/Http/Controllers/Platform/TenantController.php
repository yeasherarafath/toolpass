<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Tenancy;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::query()->with('domains')->orderBy('id')->paginate(25);

        return view('platform.admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('platform.admin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:central.tenants,id'],
            'business_name' => ['required', 'string', 'max:190'],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        $tenant = Tenant::create($data);
        $tenant->domains()->create(['domain' => $data['id'] . '.' . config('tenancy.central_domains')[0]]);

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant created.');
    }

    public function edit(Tenant $tenant)
    {
        tenancy()->initialize($tenant);
        $users = User::query()->orderBy('name')->get(['id', 'name', 'role']);
        tenancy()->end();

        return view('platform.admin.tenants.edit', compact('tenant', 'users'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:190'],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        $tenant->update($data);

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant deleted.');
    }

    /**
     * Impersonate a user inside a tenant and redirect to the tenant subdomain.
     */
    public function impersonate(Request $request, Tenant $tenant)
    {
        tenancy()->initialize($tenant);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($data['user_id']);

        $token = tenancy()->impersonate($tenant, (string) $user->getKey(), '/business');

        $domain = $tenant->domains->first()->domain ?? ($tenant->getKey() . '.' . config('tenancy.central_domains')[0]);

        tenancy()->end();

        return redirect()->away("https://{$domain}/impersonate/" . $token->token);
    }
}
