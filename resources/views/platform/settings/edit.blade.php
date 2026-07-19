@extends('layouts.god')

@section('title', 'Platform Settings')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Platform Settings</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
    </div>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item"><a href="#tab-branding" class="nav-link active" data-bs-toggle="tab">Branding</a></li>
                    <li class="nav-item"><a href="#tab-registration" class="nav-link" data-bs-toggle="tab">Registration</a></li>
                    <li class="nav-item"><a href="#tab-general" class="nav-link" data-bs-toggle="tab">General</a></li>
                    <li class="nav-item"><a href="#tab-mail" class="nav-link" data-bs-toggle="tab">Mail</a></li>
                    <li class="nav-item"><a href="#tab-sms" class="nav-link" data-bs-toggle="tab">SMS</a></li>
                    <li class="nav-item"><a href="#tab-access" class="nav-link" data-bs-toggle="tab">Access</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    {{-- Branding --}}
                    <div class="tab-pane active show" id="tab-branding">
                        <div class="mb-3">
                            <label class="form-label">Site name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="site_description" class="form-control" rows="2">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="site_keywords" class="form-control" value="{{ old('site_keywords', $settings['site_keywords'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Footer text</label>
                            <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Support email</label>
                                <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $settings['support_email'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Support phone</label>
                                <input type="text" name="support_phone" class="form-control" value="{{ old('support_phone', $settings['support_phone'] ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo</label>
                                @if (!empty($settings['logo_path']))
                                    <div class="mb-2"><img src="{{ Storage::url($settings['logo_path']) }}" alt="logo" style="max-height:48px"></div>
                                @endif
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Favicon</label>
                                @if (!empty($settings['favicon_path']))
                                    <div class="mb-2"><img src="{{ Storage::url($settings['favicon_path']) }}" alt="favicon" style="max-height:32px"></div>
                                @endif
                                <input type="file" name="favicon" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    {{-- Registration --}}
                    <div class="tab-pane" id="tab-registration">
                        <label class="form-check">
                            <input type="hidden" name="allow_owner_registration" value="0">
                            <input type="checkbox" name="allow_owner_registration" value="1" class="form-check-input" {{ ($settings['allow_owner_registration'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="form-check-label">Allow owner self-registration</span>
                        </label>
                        <label class="form-check">
                            <input type="hidden" name="require_email_verification" value="0">
                            <input type="checkbox" name="require_email_verification" value="1" class="form-check-input" {{ ($settings['require_email_verification'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="form-check-label">Require email verification</span>
                        </label>
                        <label class="form-check mb-3">
                            <input type="hidden" name="require_admin_approval" value="0">
                            <input type="checkbox" name="require_admin_approval" value="1" class="form-check-input" {{ ($settings['require_admin_approval'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="form-check-label">Require admin approval</span>
                        </label>
                        <div class="mb-3">
                            <label class="form-label">Default plan slug</label>
                            <input type="text" name="default_plan_slug" class="form-control" value="{{ old('default_plan_slug', $settings['default_plan_slug'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tenant domain suffix</label>
                            <input type="text" name="tenant_domain_suffix" class="form-control" value="{{ old('tenant_domain_suffix', $settings['tenant_domain_suffix'] ?? '') }}">
                        </div>
                    </div>

                    {{-- General --}}
                    <div class="tab-pane" id="tab-general">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Default currency</label>
                                <input type="text" name="default_currency" class="form-control" value="{{ old('default_currency', $settings['default_currency'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Default timezone</label>
                                <input type="text" name="default_timezone" class="form-control" value="{{ old('default_timezone', $settings['default_timezone'] ?? '') }}">
                            </div>
                        </div>
                        <label class="form-check mb-3">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1" class="form-check-input" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="form-check-label">Maintenance mode</span>
                        </label>
                        <div class="mb-3">
                            <label class="form-label">Facebook URL</label>
                            <input type="text" name="social_facebook" class="form-control" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">X (Twitter) URL</label>
                            <input type="text" name="social_x" class="form-control" value="{{ old('social_x', $settings['social_x'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instagram URL</label>
                            <input type="text" name="social_instagram" class="form-control" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}">
                        </div>
                    </div>

                    {{-- Mail --}}
                    <div class="tab-pane" id="tab-mail">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">From name</label>
                                <input type="text" name="mail.from_name" class="form-control" value="{{ old('mail.from_name', $settings['mail.from_name'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">From address</label>
                                <input type="email" name="mail.from_address" class="form-control" value="{{ old('mail.from_address', $settings['mail.from_address'] ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Host</label>
                                <input type="text" name="mail.host" class="form-control" value="{{ old('mail.host', $settings['mail.host'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Port</label>
                                <input type="text" name="mail.port" class="form-control" value="{{ old('mail.port', $settings['mail.port'] ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="mail.username" class="form-control" value="{{ old('mail.username', $settings['mail.username'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="mail.password" class="form-control" placeholder="{{ !empty($settings['mail.password']) ? '•••••• (set)' : '' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Encryption</label>
                            <input type="text" name="mail.encryption" class="form-control" value="{{ old('mail.encryption', $settings['mail.encryption'] ?? '') }}">
                        </div>
                    </div>

                    {{-- SMS --}}
                    <div class="tab-pane" id="tab-sms">
                        <div class="mb-3">
                            <label class="form-label">Provider</label>
                            <input type="text" name="sms.provider" class="form-control" value="{{ old('sms.provider', $settings['sms.provider'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API key</label>
                            <input type="password" name="sms.api_key" class="form-control" placeholder="{{ !empty($settings['sms.api_key']) ? '•••••• (set)' : '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sender ID</label>
                            <input type="text" name="sms.sender_id" class="form-control" value="{{ old('sms.sender_id', $settings['sms.sender_id'] ?? '') }}">
                        </div>
                    </div>

                    {{-- Access --}}
                    <div class="tab-pane" id="tab-access">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Admin login path</label>
                                <div class="input-group">
                                    <span class="input-group-text">/</span>
                                    <input type="text" name="admin_path" class="form-control" value="{{ old('admin_path', $settings['admin_path'] ?? 'yatpmin') }}">
                                </div>
                                <div class="form-hint">Changing this updates the admin login URL. Route cache is cleared on save.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Owner login path</label>
                                <div class="input-group">
                                    <span class="input-group-text">/</span>
                                    <input type="text" name="owner_path" class="form-control" value="{{ old('owner_path', $settings['owner_path'] ?? 'business') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reserved subdomains</label>
                            <textarea name="reserved_slugs" class="form-control" rows="3">{{ old('reserved_slugs', $settings['reserved_slugs'] ?? '') }}</textarea>
                            <div class="form-hint">Comma-separated subdomains that cannot be used by businesses.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save settings</button>
            </div>
        </div>
    </form>
@endsection
