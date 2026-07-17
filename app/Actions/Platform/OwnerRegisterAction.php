<?php

namespace App\Actions\Platform;

use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OwnerRegisterAction
{
    protected const DEFAULT_RESERVED = 'www,app,admin,api,mail,ftp,central,platform,dashboard,staff,superadmin,yatpmin,business';

    public function __construct(protected Settings $settings)
    {
    }

    /**
     * @return array<int, string>
     */
    protected function reservedSlugs(): array
    {
        $raw = (string) $this->settings->get('reserved_slugs', self::DEFAULT_RESERVED);

        if (trim($raw) === '') {
            $raw = self::DEFAULT_RESERVED;
        }

        return collect(preg_split('/[\s,]+/', $raw))
            ->map(fn ($s) => Str::lower(trim($s)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array{name:string,email:string,password:string,business_name:string,slug:string}  $data
     */
    public function __invoke(array $data): Owner
    {
        if (! $this->settings->bool('allow_owner_registration')) {
            throw ValidationException::withMessages([
                'email' => 'Registration is currently disabled.',
            ]);
        }

        $slug = Str::slug($data['slug']);

        $this->guardSlug($slug);

        $suffix = $this->settings->get('tenant_domain_suffix', env('CENTRAL_DOMAIN', 'toolpass.test'));
        $domain = $slug . '.' . $suffix;

        $requireApproval = $this->settings->bool('require_admin_approval');
        $requireVerification = $this->settings->bool('require_email_verification');

        $plan = Plan::where('slug', $this->settings->get('default_plan_slug', 'starter'))->first();

        return DB::connection('central')->transaction(function () use ($data, $slug, $domain, $requireApproval, $requireVerification, $plan) {
            $tenant = Tenant::create([
                'id' => $slug,
                'business_name' => $data['business_name'],
                'status' => 'active',
            ]);

            $tenant->domains()->create(['domain' => $domain]);

            $owner = Owner::create([
                'tenant_id' => $tenant->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'business_name' => $data['business_name'],
                'status' => $requireApproval ? 'pending' : 'active',
                'email_verified_at' => $requireVerification ? null : now(),
            ]);

            if ($plan) {
                Subscription::create([
                    'owner_id' => $owner->id,
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'amount' => $plan->price,
                    'currency' => $plan->currency,
                ]);
            }

            return $owner;
        });
    }

    protected function guardSlug(string $slug): void
    {
        if (strlen($slug) < 3 || in_array($slug, $this->reservedSlugs(), true)) {
            throw ValidationException::withMessages([
                'slug' => 'This subdomain is not available.',
            ]);
        }

        if (Tenant::whereKey($slug)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'This subdomain is already taken.',
            ]);
        }
    }
}
