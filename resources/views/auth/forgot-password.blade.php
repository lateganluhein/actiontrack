@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<h2 class="guest-title">Forgot Password</h2>
<p class="guest-subtitle">Enter your email to receive a password reset link</p>

@if (session('success'))
    <div class="alert alert-success">
        <p>{{ session('success') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="guest-form">
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

    <button type="submit" class="btn btn-primary btn-lg btn-block">Send Reset Link</button>
</form>

<div class="guest-links">
    <p>Remember your password? <a href="{{ route('login') }}">Back to Login</a></p>
</div>
@endsection
