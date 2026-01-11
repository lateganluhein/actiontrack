@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Settings</h1>
</div>

<div class="form-container">
    <!-- Profile Information -->
    <form method="POST" action="{{ route('settings.profile.update') }}" class="settings-form">
        @csrf
        @method('PUT')

        <div class="settings-section">
            <h3 class="settings-section-title">Profile Information</h3>

            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', auth()->user()->name) }}"
                       class="form-input @error('name') is-invalid @enderror"
                       required>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', auth()->user()->email) }}"
                       class="form-input @error('email') is-invalid @enderror"
                       required>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Update Profile</button>
        </div>
    </form>

    <!-- Change Password -->
    <form method="POST" action="{{ route('settings.password.update') }}" class="settings-form" style="margin-top: 2rem;">
        @csrf
        @method('PUT')

        <div class="settings-section">
            <h3 class="settings-section-title">Change Password</h3>

            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       class="form-input @error('current_password') is-invalid @enderror"
                       required>
                @error('current_password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input @error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <small class="form-hint">Password must be at least 8 characters</small>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-input"
                       required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Change Password</button>
        </div>
    </form>

    <!-- Email Notifications -->
    <form method="POST" action="{{ route('settings.update') }}" class="settings-form" style="margin-top: 2rem;">
        @csrf
        @method('PUT')

        <div class="settings-section">
            <h3 class="settings-section-title">Email Notifications</h3>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox"
                           name="daily_summary_enabled"
                           value="1"
                           {{ old('daily_summary_enabled', $settings->daily_summary_enabled) ? 'checked' : '' }}>
                    <span>Receive daily activity summary</span>
                </label>
                <small class="form-hint">Get a summary of your activities every day</small>
            </div>

            <div class="form-group">
                <label for="daily_summary_time" class="form-label">Daily Summary Time</label>
                <input type="time"
                       id="daily_summary_time"
                       name="daily_summary_time"
                       value="{{ old('daily_summary_time', $settings->daily_summary_time?->format('H:i') ?? '07:00') }}"
                       class="form-input form-input-time @error('daily_summary_time') is-invalid @enderror">
                @error('daily_summary_time')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <small class="form-hint">Time is in South African Standard Time (SAST)</small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox"
                           name="email_notifications"
                           value="1"
                           {{ old('email_notifications', $settings->email_notifications) ? 'checked' : '' }}>
                    <span>Receive email notifications</span>
                </label>
                <small class="form-hint">Get notified about activity updates</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
        </div>
    </form>
</div>
@endsection
