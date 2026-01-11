@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<h2 class="guest-title">Reset Password</h2>
<p class="guest-subtitle">Enter your new password</p>

@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}" class="guest-form">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email', $email) }}"
               class="form-input"
               readonly>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="Enter new password"
               required
               autofocus>
        <small class="form-hint">Password must be at least 8 characters</small>
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-input"
               placeholder="Confirm new password"
               required>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block">Reset Password</button>
</form>

<div class="guest-links">
    <p>Remember your password? <a href="{{ route('login') }}">Back to Login</a></p>
</div>
@endsection
