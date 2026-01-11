@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<h2 class="guest-title">Welcome Back</h2>
<p class="guest-subtitle">Sign in to your account</p>

@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="guest-form">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email') }}"
               class="form-input"
               placeholder="you@example.com"
               required
               autofocus>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="Your password"
               required>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>Remember me</span>
        </label>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block">Sign In</button>
</form>

<div class="guest-links">
    <p><a href="{{ route('password.request') }}">Forgot your password?</a></p>
    <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
</div>
@endsection
