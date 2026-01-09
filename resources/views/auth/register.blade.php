@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<h2 class="guest-title">Create Account</h2>
<p class="guest-subtitle">Get started with ActionTrack</p>

@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="guest-form">
    @csrf

    <div class="form-group">
        <label for="name" class="form-label">Name</label>
        <input type="text"
               id="name"
               name="name"
               value="{{ old('name') }}"
               class="form-input"
               placeholder="Your name"
               required
               autofocus>
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email') }}"
               class="form-input"
               placeholder="you@example.com"
               required>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="Create a password"
               required>
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-input"
               placeholder="Confirm your password"
               required>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block">Create Account</button>
</form>

<div class="guest-links">
    <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</div>
@endsection
