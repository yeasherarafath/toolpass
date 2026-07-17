@extends('layouts.app')

@section('header')
    <h2 class="page-title">Business Settings</h2>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header"><h3 class="card-title">Business profile</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Business name</label>
                    <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name', $settings['business_name'] ?? '') }}" required>
                    @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="business_description" class="form-control" rows="2">{{ old('business_description', $settings['business_description'] ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="business_address" class="form-control" rows="2">{{ old('business_address', $settings['business_address'] ?? '') }}</textarea>
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
                <div class="mb-3">
                    <label class="form-label">Default currency</label>
                    <input type="text" name="default_currency" class="form-control" value="{{ old('default_currency', $settings['default_currency'] ?? 'BDT') }}">
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Branding</h3></div>
            <div class="card-body">
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
        </div>

        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Social links</h3></div>
            <div class="card-body">
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
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save settings</button>
            </div>
        </div>
    </form>
@endsection
