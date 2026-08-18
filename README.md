# ToolPass

**Multi-tenant SaaS platform for digital tool subscription access management.**

ToolPass lets a platform operator run and manage many independent businesses, each of which sells access to premium digital tools (ChatGPT, Canva, Ahrefs, Semrush, Moz, and more) — as single-tool products or multi-tool bundles — while handling customers, orders, manual payment verification, access delivery, OTP/2FA, device limits, support tickets, and renewals from one dashboard.

> **What it is not:** ToolPass is not a plain e-commerce shop. It is an *access management system* for digital tool subscriptions — it tracks who has access to which tool, through which account, delivered how, and expiring when.

## Contents

- [How it works](#how-it-works)
- [Core features](#core-features)
- [Architecture](#architecture)
- [Tech stack](#tech-stack)
- [Project structure](#project-structure)
- [Requirements](#requirements)
- [Local setup (Laragon)](#local-setup-laragon)
- [Demo credentials](#demo-credentials)
- [Common commands](#common-commands)
- [Testing](#testing)
- [Scheduler & maintenance](#scheduler--maintenance)
- [Documentation & roadmap](#documentation--roadmap)
- [License](#license)

---

## How it works

The application is split into three layers. The **central platform** is a SaaS control plane; every **tenant** is one independent business with its own subdomain and its own database.

```
┌────────────────────────────────────────────────────────────────┐
│ CENTRAL PLATFORM (single domain, central DB)                    │
│                                                                │
│   GOD Admin panel  /yatpmin  → admins, roles, owners, plans,   │
│                                subscriptions, tenants,          │
│                                impersonation, settings, cache   │
│                                                                │
│   Owner panel      /business → owner registration, dashboard,  │
│                                plan-gated business settings    │
│                                                                │
│   Landing page     /          → public SaaS marketing page      │
└───────────────┬────────────────────────────────────────────────┘
                │ creates & manages tenants (DB per tenant)
┌───────────────▼────────────────────────────────────────────────┐
│ TENANT — one business per subdomain, e.g. demo.group-by-tools.test │
│                                                                │
│   Storefront     /store      → public package listing & detail  │
│   Business Admin /business   → users, tools, accounts,          │
│                                packages, orders, payments,      │
│                                OTP/2FA, devices, tasks,         │
│                                support, announcements, reviews  │
│   Staff          /staff      → staff dashboard                  │
│   Customer       /customer   → dashboard, orders, OTP requests, │
│                                device resets, support tickets   │
└─────────────────────────────────────────────────────────────────┘
```

Key architectural facts:

- **Database per tenant.** The central database stores SaaS data (`tenants`, `domains`, `admins`, `owners`, `plans`, `subscriptions`, `platform_settings`, spatie roles/permissions, ...). Each tenant has its own MySQL database named `toolpass_{tenant_id}` containing the full business schema (34 tables).
- **Subdomain tenancy.** Tenants are resolved by subdomain (`stancl/tenancy` v3, `InitializeTenancyBySubdomain` middleware).
- **Two separate admin surfaces.** Platform GOD admins (guard `admin`) and business owners (guard `owner`) live on the central domain and are authenticated against central tables. Tenant users (business admins, staff, customers) authenticate against the tenant database.
- **Configurable admin URLs.** The GOD admin path and owner path are stored in `platform_settings` (`admin_path`, default `yatpmin`) and `owner_path` (default `business`). Visit `/{admin_path}/login` to reach the platform panels.
- **Impersonation.** GOD admins can impersonate tenant users via a signed token route (`/impersonate/{token}`) in the tenant context.

## Core features

### Catalog management

- **Tool categories** — SEO, AI, Design, Developer, etc., with status and sort order.
- **Tools** — each tool has an *access type* that drives delivery behavior:
  - `credential` — customer receives login credentials immediately
  - `invite` — admin must invite the customer by email (e.g. Canva Team)
  - `instruction` — customer receives an instruction/guide
  - `manual` — admin must process delivery manually
  - `external` — access handled outside the platform, tracked only
  - Tools also carry OTP/2FA flags and device-restriction policies.
- **Tool accounts** — owned subscriptions/licenses with encrypted credentials (`login_password`, `2FA secret`, `backup codes` are `Crypt::encrypt`ed), slot tracking (`max_users` / `used_slots`), device limits, renewal/expiry dates, and auto `full` status when all slots are taken.
- **Packages** — single-tool or multi-tool bundles with `auto` / `manual` / `mixed` delivery types, price, duration, featured flag, and per-package **custom product fields** (text, email, number, textarea, URL, select, checkbox, radio, date, file) so the business can collect invite emails, keywords, website URLs, etc. before processing an order.

### Purchase pipeline (core)

1. Customer picks a package and an order is created (`ORD-xxxxx`, reference generated).
2. If the package has custom fields, the customer submits the required information; the business admin approves or rejects it.
3. The customer pays manually (bKash, Nagad, Rocket, bank transfer, card) and submits transaction ID + screenshot; the business admin verifies or rejects the payment.
4. When the order is **ready** (`payment_status = paid` **and** `required_info_status ∈ {approved, not_required}`), the system automatically creates one `user_tool_access` per tool in the package.
5. Credential/instruction tools are delivered instantly; invite/manual tools create an **admin task** (e.g. *invite Rahim to Canva Team*) that staff completes to mark delivery done.
6. Slot accounting is automatic: assigning an access increments the account's `used_slots`, expiry/revocation decrements it.

### Operations

- **OTP / 2FA requests** — customers request OTP for a tool login; an admin task is created; the admin provides the code (stored encrypted, auto-expires, rate-limited, never exposes 2FA secrets or backup codes to customers).
- **Device management** — device registration, approval, blocking, removal, and customer device-reset requests with cooldown intervals.
- **Admin task queue** — auto-created tasks for payment verification, info review, invites, manual delivery, OTP, device approval/reset, renewals, and support.
- **Support tickets** — customers open tickets (linkable to orders/tools/accesses), staff reply and close.
- **Announcements** — info / warning / maintenance / success, visible to all/customers/staff/admins.
- **Activity logs** — every important state change is audited via the `LogsActivity` trait.

### Add-now feature set

- Multi-currency (`currencies` table, BDT default, currency-aware orders/payments)
- Wallets with credit/debit, locked balance, and wallet-backed order payments
- Coupons / promotions (usage limits, min amount, once-per-user, max discount caps)
- Notifications (email/SMS via `notifications` + `notification_templates`, queued)
- Reviews & testimonials with moderation
- Package contents & SEO (`includes`, `faq`, `highlights`, `seo` sections, `meta_title`, `meta_description`)
- Financial summaries (nightly per-currency aggregates)
- Trial-to-paid packages (`is_trial`, `trial_days`, renewal conversion)
- Renewals with `renewed_from_order_id` chains

## Architecture

The codebase follows a deliberate, testable pattern:

- **Action pattern** — every command-side operation is a single-responsibility class in `app/Actions/{Domain}/XxxAction.php` exposing `public function handle(...)` (e.g. `VerifyPaymentAction`, `CreateOrderAccessesAction`, `ProvideOtpAction`).
- **Model observers** — automatic side-effects live in `app/Observers/` (slot sync on access create/expire, account `full` status, admin-task spawning, order readiness) and are registered in `AppServiceProvider`.
- **Events → Listeners** — cross-cutting concerns (notifications, activity logs, financial summaries) are decoupled: observers dispatch domain events (`AccessDelivered`, `OtpProvided`, `PaymentVerified`, ...) which `app/Listeners/` handles.
- **Shared concern traits** — `app/Actions/Concerns/`: `LogsActivity`, `SyncsAccountSlots`, `NotifiesUser`, `GeneratesReference`, `ResolvesOrderReadiness`, `AppliesCoupon`, `HandlesOtpRateLimit`, `GeneratesUniqueSlug`.
- **Caching** — structured cache-key system (`app/Enum/CacheKeyEnum`), a `ModelCache` trait, `app/Services/CachePatternService`, and self-healing `cached_collection()` / settings caches. The GOD admin can clear caches by group/sub-module from the panel.
- **Settings** — `app/Services/Settings.php` (central `platform_settings`) and `TenantSettings.php` (per-tenant `settings` table), both cached and observed for direct mutations.
- **Global helpers** — `app/Helper.php` registers functions like `setting()`, `admin_path()`, `owner_path()`, `site_name()`, `mask()`, `initials()`.
- **RBAC** — spatie/laravel-permission on the central side (`super_admin`, `platform_staff` roles; guard `admin`) with a self-healing permission cache provider (`SuperAdminPermissionProvider`).

## Tech stack

| Layer     | Choice |
|-----------|--------|
| Framework | Laravel 13 (PHP ^8.3) |
| Multi-tenancy | stancl/tenancy ^3.10 (database-per-tenant, subdomain) |
| RBAC | spatie/laravel-permission ^8.3 |
| Database | MySQL (central + per-tenant databases) |
| UI kit | Tabler (Bootstrap-based) via `@tabler/core` + `@tabler/icons-webfont`, built with Vite |
| Frontend build | Vite 8 + laravel-vite-plugin |
| Testing | PHPUnit 12 |
| Dev tooling | Laravel Pail (logs), Debugbar, Laravel Pint |

## Project structure

```
app/
├── Actions/                # Command-side business operations (action pattern)
│   ├── Access/ Orders/ Payments/ Otp/ Device/ Coupon/ Wallet/ ...
│   └── Concerns/           # Shared traits (LogsActivity, SyncsAccountSlots, ...)
├── Events/  Listeners/     # Decoupled cross-cutting side-effects
├── Observers/              # Automatic reactions to model changes
├── Enum/                   # CacheKeyEnum
├── Models/                 # Eloquent models (central + tenant)
├── Policies/               # e.g. ToolAccountPolicy
├── Providers/              # AppServiceProvider, TenancyServiceProvider, ...
├── Services/               # Settings, TenantSettings, CachePatternService
├── Traits/                 # ModelCache
├── Http/Controllers/       # Platform/*, Admin/*, Customer/*, Auth/*
│   └── Middleware/         # EnsureRole, Authenticate
└── Helper.php              # Global helper functions (autoloaded)

routes/
├── web.php                 # Central platform routes (landing, admin.*, owner.*)
├── tenant.php              # Tenant routes (storefront, business.*, staff.*, customer.*)
└── console.php             # Scheduler (access:expire daily)

database/
├── migrations/             # Central migrations (tenants, domains, admins, plans, ...)
├── migrations/tenant/      # Per-tenant schema (34 tables: core, add-now, auth, settings)
├── factories/  seeders/    # PlatformSeeder + TenantDatabaseSeeder demo data
resources/views/            # Blade layouts (god, owner, app, customer, guest) + panels
tests/                      # PHPUnit Feature + Unit suites (TenantTestCase aware)
```

## Requirements

- PHP **^8.3** (with `pdo_mysql`, `bcrypt`, `openssl`, `fileinfo`, `zip`)
- Composer 2
- Node.js 18+ and npm
- MySQL 8.x (or MariaDB)
- [Laragon](https://laragon.org) recommended for local development on Windows

## Local setup (Laragon)

### 1. Clone & install dependencies

```bash
git clone <repo-url> group-by-tools
cd group-by-tools

composer install
npm install
```

### 2. Configure the environment

The stock `.env.example` is the vanilla Laravel one — it does **not** contain the tenancy variables. Copy it and add them:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```dotenv
APP_NAME="ToolPass"
APP_URL=http://group-by-tools.test

# Central platform database (MySQL)
DB_CONNECTION=central
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toolpass_central
DB_USERNAME=root
DB_PASSWORD=

# Used to build tenant subdomains (must match your hosts/vhost setup)
CENTRAL_DOMAIN=group-by-tools.test

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

> Note: `DB_CONNECTION=central` uses the `central` MySQL connection from `config/database.php`. Tenant databases are created automatically by stancl/tenancy as `toolpass_{tenant_id}` on the same MySQL server.

### 3. Create the central database

Create `toolpass_central` (Laragon → Menu → MySQL → Create database, or via phpMyAdmin / a SQL client).

### 4. Migrate & seed the central database

```bash
php artisan migrate --seed
```

This installs all central tables and runs `PlatformSeeder` (roles, permissions, super admin, starter/pro plans, platform settings).

### 5. Point your domains at the app

Add these entries to the Windows hosts file (`C:\Windows\System32\drivers\etc\hosts`):

```
127.0.0.1  group-by-tools.test
127.0.0.1  demo.group-by-tools.test
```

In Laragon, add a wildcard vhost pointing to the project's `public/` folder so every `*.group-by-tools.test` subdomain resolves to the app:

```
DocumentRoot: G:\laragon\www\group-by-tools\public
ServerName:   group-by-tools.test
ServerAlias:  *.group-by-tools.test
```

(`config/tenancy.php` already whitelists `group-by-tools.test`, `localhost`, and `127.0.0.1` as central domains.)

### 6. Build the frontend & start the dev stack

```bash
npm run build
```

Or run everything at once (server + queue worker + logs + Vite hot reload):

```bash
composer dev
```

### 7. Create your first tenant business

1. Open `http://group-by-tools.test` → the landing page. Log in to the **GOD admin panel** at `http://group-by-tools.test/yatpmin/login` (see [demo credentials](#demo-credentials)).
2. Use **Subscribe** to register a new business owner + tenant (subdomain, plan, database) in one go — or create the tenant under **Tenants** and register the owner separately.
3. Run the tenant migrations and seed the demo data:

```bash
php artisan tenants:migrate
php artisan tenants:seed
```

4. Visit `http://demo.group-by-tools.test` — the storefront is live. Log in as the business admin (demo credentials below) to manage the business from the `/business` area.

> Tenant DB creation uses the MySQL manager, so the MySQL user in `.env` needs privileges to create databases.

## Demo credentials

| Role | URL | Email | Password |
|------|-----|-------|----------|
| Platform super admin | `/{admin_path}/login` (default `/yatpmin/login`) | `superadmin@toolpass.test` | `password` |
| Tenant business admin | tenant subdomain `/business` | `admin@example.com` | `password` |
| Tenant staff (×2) | tenant subdomain `/staff` | from seeder | `password` |
| Tenant customers (×5) | tenant subdomain `/customer` | from seeder | `password` |

The tenant seeder also creates 3 tool categories, 5 tools (ChatGPT, Canva Team, Ahrefs, Moz, Semrush), tool accounts, the **SEO Bundle - 30 Days** package (with custom fields `invite_email`, `full_name`), and 10 sample orders.

## Common commands

```bash
composer dev                 # server + queue + pail logs + Vite concurrently
composer test                # run the PHPUnit suite
php artisan serve            # (central only; subdomains need the vhost setup)
php artisan pail             # tail the Laravel log
php artisan queue:work       # process queued jobs
php artisan tenants:migrate  # migrate all tenant databases
php artisan tenants:seed     # seed all tenant databases
php artisan access:expire    # expire overdue accesses (also runs daily via scheduler)
php artisan list --help      # ...and everything standard Laravel
```

## Testing

```bash
composer test
```

The suite uses PHPUnit and covers both platform and tenant behavior:

- `tests/Feature/` — auth, platform admin, RBAC & permission cache, tenant CRUD, packages/tools/accounts, order pipeline, storefront (incl. caching), dashboards, cache management, add-now features, security & deploy checks
- `tests/Unit/` — order pipeline, operational logic, listeners, helpers
- `tests/TenantTestCase.php` + `tests/Concerns/InteractsWithTenancy.php` — base classes for tenant-scoped tests

## Scheduler & maintenance

`routes/console.php` schedules `access:expire` daily. On production, register the scheduler and run a queue worker:

```bash
php artisan schedule:run      # every minute via cron (cron: * * * * * php /path/to/artisan schedule:run)
php artisan queue:work --tries=3
```

Additional operational notes:

- **Cache management** — the GOD admin can inspect and clear caches (group / sub-module / all) under *Cache* in the panel.
- **Secrets** — tool credentials, 2FA secrets, backup codes, and OTP codes are encrypted at rest with Laravel's `Crypt`; never expose them to customers.
- **Money** — all money columns are `DECIMAL(12,2)` and carry a `currency CHAR(3)`.
- **Audit** — every state transition is appended to `activity_logs` via the `LogsActivity` trait.

## Documentation & roadmap

The repository contains design/spec files that act as the source of truth for the domain:

- [`business details.txt`](business%20details.txt) — full business description, roles, flows, and business rules
- [`db.txt`](db.txt) — complete SQL schema (columns, indexes, comments) for all tenant tables
- [`roadmap.md`](roadmap.md) — developer/AI execution roadmap (milestones M0–M8)
- [`future feature.txt`](future%20feature.txt) — architecture decisions + add-now/future feature plans (affiliates, auto-billing, plan tiers, B2B seats, staff marketplace, API, vendor model)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). This application is a proprietary project built on top of it.
