@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Settings</h1>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('settings.update') }}" class="settings-form">
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

        <div class="settings-section">
            <h3 class="settings-section-title">Account Information</h3>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Name</span>
                    <span class="detail-value">{{ auth()->user()->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ auth()->user()->email }}</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
        </div>
    </form>
</div>
@endsection
