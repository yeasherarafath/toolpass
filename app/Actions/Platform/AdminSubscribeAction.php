<?php

namespace App\Actions\Platform;

use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * GOD-mode subscribe flow: attach a plan/subscription to an existing owner
 * (or provision a brand-new owner + tenant) on behalf of the platform.
 */
class AdminSubscribeAction
{
    /**
     * @param  array{
     *   owner_id?: int|null,
     *   name?: string,
     *   email?: string,
     *   password?: string,
     *   business_name?: string,
     *   slug?: string,
     *   plan_id: int,
     *   status?: string,
     *   starts_at?: string|null,
     *   ends_at?: string|null,
     *   amount?: float|null,
     * }  $data
     */
    public function __invoke(array $data): Subscription
    {
        $plan = Plan::findOrFail($data['plan_id']);

        return DB::connection('central')->transaction(function () use ($data, $plan) {
            $owner = isset($data['owner_id'])
                ? Owner::findOrFail($data['owner_id'])
                : $this->provisionOwner($data);

            $tenant = $owner->tenant;

            if (! $tenant) {
                throw ValidationException::withMessages([
                    'owner_id' => 'The selected owner has no tenant.',
                ]);
            }

            return Subscription::create([
                'owner_id' => $owner->id,
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => $data['status'] ?? 'active',
                'starts_at' => $data['starts_at'] ?? now(),
                'ends_at' => $data['ends_at'] ?? null,
                'amount' => $data['amount'] ?? $plan->price,
                'currency' => $plan->currency,
            ]);
        });
    }

    protected function provisionOwner(array $data): Owner
    {
        if (empty($data['slug']) || empty($data['email']) || empty($data['password'])) {
            throw ValidationException::withMessages([
                'email' => 'Name, email, password and subdomain are required to create a new owner.',
            ]);
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $data['slug']));

        if (Tenant::whereKey($slug)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'This subdomain is already taken.',
            ]);
        }

        $suffix = config('tenancy.central_domains')[0] ?? 'toolpass.test';
        $domain = $slug . '.' . $suffix;

        $tenant = Tenant::create([
            'id' => $slug,
            'business_name' => $data['business_name'] ?? $data['name'],
            'status' => 'active',
        ]);
        $tenant->domains()->create(['domain' => $domain]);

        return Owner::create([
            'tenant_id' => $tenant->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'business_name' => $data['business_name'] ?? $data['name'],
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
