# DEVELOPER / AI EXECUTION ROADMAP
**Project:** Tool Subscription Access Management Platform (Laravel + MySQL)
**Source of truth:** `business details.txt` (logic), `db.txt` (schema), `future feature.txt` (Add-Now + Future), this roadmap (sequence).
**Architecture contract:**
- Command ops -> `app/Actions/{Domain}/XxxAction.php` (single responsibility, `public function handle(...)`).
- Automatic reactions -> `app/Observers/XxxObserver.php` registered in `AppServiceProvider::boot()`.
- Cross-cutting -> Observer dispatches Event (`app/Events/*`) -> Listener (`app/Listeners/*`).
- Shared logic -> `app/Actions/Concerns/*.php` traits (`LogsActivity`, `SyncsAccountSlots`, `NotifiesUser`, `GeneratesReference`, `ResolvesOrderReadiness`, `HandlesOtpRateLimit`, `AppliesCoupon`).
- All models: `SoftDeletes`, `$fillable`, `$casts` (JSON->`array`, dates->`datetime`), relationships matching FKs in `db.txt`.

**Global rules (apply to every task):**
1. Mirror `db.txt` columns/indexes/comments exactly in migrations.
2. No destructive ALTERs; Add-Now uses appended block only.
3. Encrypt at rest: `login_password_encrypted`, `two_factor_secret_encrypted`, `backup_codes_encrypted`, `otp_code_encrypted` via `Crypt::encrypt()`.
4. Never expose secrets to customers (2FA secret, backup codes, raw password).
5. Every money field `DECIMAL(12,2)`; `currency CHAR(3)`.
6. Every state transition logs via `LogsActivity` trait -> `activity_logs`.
7. Idempotency: guards so observers/events never double-process.

---

## M0 -- FOUNDATION

### T0.1 Project init
- **Files:** repo root, `.env`, `composer.json`.
- **Steps:**
  1. `composer create-project laravel/laravel .` (Laravel 11).
  2. `.env`: `DB_DATABASE=tool_subscription_platform`, `DB_CHARSET=utf8mb4`, `DB_COLLATION=utf8mb4_unicode_ci`, `QUEUE_CONNECTION=database`, `APP_LOCALE=en`, add `APP_CURRENCY=BDT`.
  3. `php artisan migrate:install`.
- **Why:** baseline. **Exceptions:** Laragon/Win path; use `php artisan serve` for dev.
- **Done-when:** app boots, DB connects.

### T0.2 Migrations from db.txt (CRITICAL)
- **Files:** `database/migrations/2026_*.php` -- one per table, plus `add_now_schema.php`.
- **Steps:** For each table in `db.txt` write `Schema::create('table', fn(Blueprint $t) => {...})`:
  - `users`: cols per db.txt L24-50; `$t->softDeletes()`; indexes `role`,`status`,`phone`.
  - `tool_categories`, `tools` (FK `category_id` SET NULL), `tool_accounts` (FKs `tool_id` CASCADE, `created_by/updated_by` SET NULL), `packages`, `package_tools` (unique `(package_id,tool_id)`), `package_custom_fields` (unique `(package_id,name)`), `orders` (FKs `user_id` CASCADE, `package_id` RESTRICT, `required_info_reviewed_by` SET NULL, `renewed_from_order_id` SET NULL), `order_custom_field_values`, `payments`, `user_tool_accesses`, `user_tool_devices`, `device_reset_requests`, `otp_requests`, `admin_tasks`, `support_tickets`, `support_ticket_messages`, `announcements`, `activity_logs`.
  - Add-Now migration: `currencies`,`translations`,`wallets`,`wallet_transactions`,`coupons`,`coupon_usages`,`notifications`,`notification_templates`,`reviews`,`package_contents`,`financial_summaries`,`jobs`,`failed_jobs`,`job_batches`; then ALTER `packages` (currency,is_trial,trial_days,meta_title,meta_description), `payments` (currency), `orders` (currency,wallet_amount,paid_via_wallet,is_trial,converted_from_trial_order_id,coupon_id,coupon_code) + FKs (`converted_from`->orders, `coupon`->coupons).
- **Why:** db.txt is single source of truth. **Exceptions:** create `coupons` BEFORE `orders` ALTER (FK order). Use `charset/collation` on table. `options`/`metadata`/`variables` -> `json()`.
- **Done-when:** `migrate` runs clean on empty DB; `SHOW TABLES` = all 30+ tables.

### T0.3 Models + Observers + Concerns scaffold
- **Files:** `app/Models/*.php` (30+), `app/Providers/AppServiceProvider.php`, `app/Actions/Concerns/*.php`.
- **Steps:** `make:model` (skip migration). Define relationships by FK. Register observers: `User::observe(UserObserver::class)` etc. Create empty Concern traits.
- **Why:** powers Actions/Observers. **Exceptions:** `users` one model 3 roles; never hard-delete admin.
- **Done-when:** `php artisan tinker` -> `App\Models\Order::first()` works.

### T0.4 Factories + Seeders
- **Files:** `database/factories/*.php`, `database/seeders/*.php`, `DatabaseSeeder.php`.
- **Steps:** Factory each model with realistic data; seeder creates 1 admin, 2 staff, 5 customers, categories (SEO/AI/Design), tools (ChatGPT c, Canva invite, Ahrefs manual), tool accounts, SEO Bundle (mixed) with custom fields (invite_email). Passwords via `Hash::make('password')`.
- **Why:** every later task needs data. **Exceptions:** no real credentials; encrypt fake passwords.

### T0.5 Layout + auth + role middleware
- **Files:** `app/Http/Middleware/EnsureRole.php`, `routes/web.php`, `resources/views/layouts/*`.
- **Steps:** `EnsureRole` checks `auth()->user()->role`; route groups `admin.`, `staff.`, `customer.`; basic Blade layouts.
- **Why:** gate areas. **Exceptions:** staff partial-permission deferred (T1.2 note).

---

## M1 -- AUTH & USERS

### T1.1 Auth flows
- **Files:** `app/Actions/Auth/*` (or use Breeze), `app/Http/Controllers/Auth/*`.
- **Steps:** customer self-register (`role=customer`); admin via `php artisan tinker`/`make:filament-user` style seeder; login/logout; `email_verified_at` optional.
- **Validation:** email unique, password min 8.
- **Exceptions:** phone nullable unique-ignore; banned/suspended blocked at login.

### T1.2 User management (admin)
- **Files:** `app/Actions/Users/{CreateUserAction,UpdateUserAction,SuspendUserAction}.php`, `UserController`.
- **Observer:** `UserObserver` (on saved -> `LogsActivity`).
- **Exceptions:** cannot suspend/ban self; admin deletion forbidden (soft delete only); staff scoped later.

---

## M2 -- CATALOG

### T2.1 Categories
- **Action:** `CategoryActions/CreateCategoryAction`, `UpdateCategoryAction`, `DeleteCategoryAction`.
- **Fields:** name, slug(unique), description, status, sort_order.
- **Exceptions:** inactive hides from storefront; deleting category SET NULL on tools.

### T2.2 Tools
- **Action:** `ToolActions/*`. **Cols:** category_id, name, slug(unique), website_url, logo, description, status, access_type(credential|invite|instruction|manual|external), otp_required, otp_type, otp_note, device_restriction_enabled, device_limit_type, default_max_devices, device_policy_note.
- **Exceptions:** changing `access_type` on tool with active accesses must NOT retro-change `user_tool_accesses` (they captured their own delivery_status at creation).

### T2.3 Tool Accounts (encryption)
- **Action:** `ToolAccountActions/*` using `SyncsAccountSlots`.
- **Encrypt:** login_password, two_factor_secret, backup_codes via `Crypt`.
- **Observer:** `ToolAccountObserver` -> on `used_slots` change, if `>=max_users` set `status='full'`; if `<max_users` and was full, set `active`.
- **Exceptions:** `status` enum active|expired|full|disabled|issue; renewal_date/expires_at drive M3.6 expiry; never log decrypted.

### T2.4 Packages + tools + custom fields
- **Action:** `PackageActions/{CreatePackageAction,UpdatePackageAction}`, `CreatePackageToolAction`, `CreateCustomFieldAction`.
- **Cols packages:** name, slug(unique), type(single|bundle), delivery_type(auto|manual|mixed), description, price, duration_days, status, sort_order, is_featured, + Add-Now (currency,is_trial,trial_days,meta_title,meta_description).
- **Custom field:** label, name(machine), type(text|email|number|textarea|url|select|checkbox|radio|date|file), options(JSON), is_required, validation_rules(e.g. `required|email`), sort_order.
- **Exceptions:** bundle needs >=1 `package_tools`; removing a tool from bundle does NOT delete historical accesses; `name` unique per package; file type stores `file_path`.

### T2.5 Storefront listing
- **Controller:** `PackageController@index` (customer) -- active packages; show price+currency; single vs bundle badge.
- **Exceptions:** inactive hidden; respect `visible_to` later for announcements only.

---

## M3 -- PURCHASE PIPELINE (CORE)

### T3.1 Create order
- **Action:** `Orders/CreateOrderAction` (uses `GeneratesReference`, `ResolvesOrderReadiness`).
- **Logic:**
  ```
  $order = Order::create([
    'user_id'=>$user->id,'package_id'=>$pkg->id,
    'order_number'=>GenerateReference::order(),
    'amount'=>$pkg->price,'discount_amount'=>0,
    'payable_amount'=>$pkg->price,'currency'=>$pkg->currency,
    'payment_status'=>'pending','order_status'=>'pending',
    'required_info_status'=>$pkg->hasCustomFields()?'pending':'not_required',
  ]);
  ```
- **Observer:** none yet (waits for pay+info).
- **Exceptions:** wallet/coupon applied in M6 (T6.3/T6.5) -- keep `payable_amount` adjustable pre-payment only.

### T3.2 Required info
- **Action:** `Orders/ReviewRequiredInfoAction`.
- **Flow:** customer POST to `order_custom_field_values` (validate per `validation_rules`). status `submitted`. Admin `approved`/`rejected` (reason). On `approved` -> `MarkOrderReadyAction`.
- **Observer:** `OrderObserver` watches `required_info_status` -> if `approved` and payment paid -> `CreateOrderAccessesAction`.
- **Exceptions:** reject -> back to `pending`; re-submit overwrites values; file re-upload handled.

### T3.3 Payment
- **Action:** `Payments/VerifyPaymentAction`, `RejectPaymentAction`.
- **Logic:** create `payments` (order_id,user_id,amount,method,transaction_id,sender_number,screenshot,status=pending). On verify: payment `verified`, order `payment_status='paid'`, `verified_by`. On reject: payment `rejected`, order `failed`, reason.
- **Observer:** `PaymentObserver` on `verified` -> `MarkOrderReadyAction`.
- **Exceptions:** multiple payments allowed but reconcile Sum amount vs `payable_amount`; overpay -> credit wallet (T6.3); fake screenshot -> reject.

### T3.4 Readiness + Access creation (AUTO)
- **Action:** `Orders/MarkOrderReadyAction` + `Access/CreateOrderAccessesAction`.
- **Readiness:** `ResolvesOrderReadiness::isReady($order)` = `payment_status=paid` AND `required_info_status IN (approved,not_required)`.
- **CreateOrderAccessesAction:**
  ```
  foreach ($order->package->tools as $tool) {
    if ($order->accesses()->where('tool_id',$tool->id)->exists()) continue; // idempotency
    $access = UserToolAccess::create([
      'user_id','order_id','tool_id',
      'source'=>'order','status'=>'active',
      'delivery_status'=>$tool->access_type in [credential,instruction]?'delivered':'pending',
      'starts_at'=>now(),'expires_at'=>now()->addDays($pkg->duration_days),
    ]);
    if ($tool->access_type in [invite,manual])
        AdminTask::create([type=>$tool->access_type==='invite'?'invite_user':'manual_delivery', ...]);
    if ($tool->access_type==='invite') $access->update(['customer_email_for_invite'=>...from custom fields]);
  }
  ```
- **Observer:** `UserToolAccessObserver` on `created` -> `SyncsAccountSlots::increment($access)` (picks an available `tool_account` if `max_users` allows, else NULL for pure invite) + dispatch `AccessDelivered` event.
- **Why:** the repetitive core the architecture exists for. **Exceptions:** idempotency guard; per-tool status independent (mixed delivery); never create if not ready; if no available account for manual tool, access stays `pending` + `full` task.

### T3.5 Delivery completion
- **Action:** `Access/DeliverAccessAction`.
- **Logic:** admin completes invite/manual -> set `delivered_at`, `delivery_status='delivered'`, complete linked `admin_task` (find by `user_tool_access_id`+type). For invite: record `invited_at`.
- **Exceptions:** require `customer_email_for_invite` present; credential tools auto-delivered at T3.4 (no task).

### T3.6 Expiry + renewal
- **Console:** `php artisan make:command AccessExpire` (daily via scheduler).
- **Logic:** where `expires_at<=now()` and `status='active'` -> `status='expired'`; order `order_status='expired'`; `renewed_from` chain. `UserToolAccessObserver` on `expired`/`revoked` -> `SyncsAccountSlots::decrement` + `AccessExpired` event.
- **Action:** `Renewal/CreateRenewalAction` -> new order `renewed_from_order_id`, extends access (guard overlapping active access -- fix drawback #2).
- **Exceptions:** slot freed only after expiry; renewal before expiry extends not duplicates.

---

## M4 -- OPERATIONAL MODULES

### T4.1 OTP/2FA
- **Action:** `Otp/RequestOtpAction` (uses `HandlesOtpRateLimit`), `ProvideOtpAction`.
- **Observer:** `OtpRequestObserver` on `created` -> `admin_task` type `provide_otp`.
- **Events:** `OtpProvided` -> `SendNotification` + `WriteActivityLog`.
- **Logic:** provide -> `otp_code_encrypted=encrypt($code)`, `otp_expires_at=now()->addMinutes(5)`, status `provided`. Customer views decrypted only while not expired.
- **Exceptions:** short expiry; never show 2FA secret/backup; rate-limit N/min per user; lifecycle pending->processing->provided->used/expired/revoked/cancelled/rejected.

### T4.2 Devices
- **Action:** `Device/ApproveDeviceAction`, `ResetDeviceAction`.
- **Logic:** on access use detect device (name,type,browser,os,ip,fingerprint). If over `max_devices` -> `pending_approval` + `device_reset`/`approve_device` task. Approve -> `active`; reset -> old `removed`, decrement `used_devices`.
- **Observer:** `DeviceResetRequestObserver` -> `device_reset` task.
- **Exceptions:** `allow_device_reset` flag; `device_reset_interval_days` cooldown; removing decrements account `used_devices`.

### T4.3 Admin tasks
- **Action:** `AdminTaskActions/*`. Auto-created by observers above. Types: verify_payment, review_required_info, invite_user, manual_delivery, provide_otp, provide_2fa, approve_device, device_reset, renewal, support. Priorities low|medium|high|urgent. `assigned_to` nullable.
- **Exceptions:** dedupe (don't create if open task exists for same ref); completing invite task triggers `DeliverAccessAction`.

### T4.4 Support
- **Action:** `Support/CreateTicketAction`, `ReplyTicketAction`. Link order/tool/access. `support_ticket_messages` with `is_staff_reply`.
- **Exceptions:** ticket openable post-expiry (dispute); assign staff.

### T4.5 Announcements + Logs
- **Action:** `AnnouncementActions/*` (visible_to all|customers|staff|admins). `WriteActivityLog` listener captures all key actions via `LogsActivity`.

---

## M5 -- DASHBOARD

### T5.1 Admin dashboard
- **Controller:** `AdminDashboardController`. Widgets using indexed columns: pending_payments (`payments.status=pending`), ready_for_delivery (`orders` ready & accesses pending), pending_otp (`otp_requests.status=pending`), device_approvals, expiring_accesses (`user_tool_accesses.expires_at` < +7d & active), full_accounts (`tool_accounts.status=full`), open_tickets, revenue (Sum payable paid).
- **Exceptions:** cache counts; read replica if scaled.

### T5.2 Customer dashboard
- **Controller:** `CustomerDashboardController`. Shows `user_tool_accesses` for user: delivered credentials (decrypted note), pending separately, OTP request btn, device reset btn, tickets, announcements.

---

## M6 -- ADD-NOW FEATURES (order matters)

**T6.1 Multi-currency** -- use `currency` cols (db.txt done); `Currencies` seeder (BDT default rate 1); convert in views; gateway reconciliation currency-aware. Exception: legacy orders BDT.

**T6.2 i18n** -- `translations` table; `locale` middleware + `TranslatesModel` helper; bn/en for package/announcement text.

**T6.3 Wallet** -- `Wallet/CreditWalletAction`,`DebitWalletAction` (lock `balance` row). Order can pay via wallet: set `wallet_amount`,`paid_via_wallet`; recompute `payable_amount`. Observer wallet change -> `WalletChanged` -> financial+log. Exceptions: insufficient blocked; refund credits wallet.

**T6.4 Trial-to-paid** -- `is_trial`/`trial_days`; `CreateRenewalAction` converts trial->paid; reminder job pre-expiry. Exceptions: trial may still need manual delivery; no double charge.

**T6.5 Coupons** -- `Coupon/ValidateCouponAction` (min_amount,usage_limit,expiry,once-per-user), `ApplyCouponAction` (uses `AppliesCoupon` trait; reduce `payable_amount`, record `coupon_id`, increment `used_count`, insert `coupon_usages`). Exceptions: percent cap `max_discount`.

**T6.6 Notifications** -- `NotifiesUser` trait dispatches; `notifications` rows queued; worker sends email/SMS via `notification_templates`. Exceptions: template vars; retry failed.

**T6.7 Reviews** -- `Reviews` only by customers with delivered access; moderation `status`. Public on package page.

**T6.8 Package contents/SEO** -- `PackageContents` sections includes/faq/highlights/seo; `meta_*` cols; storefront render.

**T6.9 Financial summaries** -- `FinancialSummaries` nightly job; `UpdateFinancialSummary` listener increments on events. Exception: idempotent date+currency.

**T6.10 Queue jobs** -- move OTP expiry, access expiry, renewal reminders, notifications, financial summary to queue. Exception: idempotent; monitor `failed_jobs`.

---

## M7 -- FUTURE (phased, schema added when started)
T7.1 affiliate (`affiliates`,`commissions`) . T7.2 auto-billing (`payment_methods`,`subscriptions`) . T7.3 tiers (`plan_tiers`,`order_addons`) . T7.4 B2B seats (`orders.quantity`,`team_members`) . T7.5 staff marketplace (`staff_payouts`,`staff_slas`) . T7.6 API (`api_tokens`,`webhooks`) . T7.7 vendor model (`vendors`) -- strategic ToS-risk fix. Each follows same Action/Observer pattern.

## M8 -- QUALITY/SECURITY/DEPLOY
T8.1 Pest tests (Actions, flows, observer wiring) . T8.2 security (key mgmt, OTP rate-limit, policies, secret masking, log tamper-proof) . T8.3 deploy (queue worker, scheduler `access:expire`+financial, HTTPS, encrypted backups).

---

## CROSS-CUTTING CHECKLISTS (use every task)
- [ ] Migration matches db.txt cols/indexes/comments
- [ ] Model has `$fillable`,`$casts`,`SoftDeletes`, relationships
- [ ] Observer registered in `AppServiceProvider`
- [ ] State change uses `LogsActivity`
- [ ] Money `DECIMAL(12,2)` + `currency`
- [ ] Secrets encrypted, never sent to customer
- [ ] Idempotency guard on observers/events
- [ ] Factory/seeder covers entity
- [ ] Acceptance criteria met before "Done"
