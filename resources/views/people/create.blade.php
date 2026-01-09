@extends('layouts.app')

@section('title', 'Add Person')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Person</h1>
    <a href="{{ route('people.index') }}" class="btn btn-secondary">Cancel</a>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('people.store') }}" class="person-form">
        @csrf

        <!-- Name Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="first_name" class="form-label required">First Name</label>
                <input type="text"
                       id="first_name"
                       name="first_name"
                       value="{{ old('first_name') }}"
                       class="form-input @error('first_name') is-invalid @enderror"
                       placeholder="First name"
                       required
                       autofocus>
                @error('first_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label required">Last Name</label>
                <input type="text"
                       id="last_name"
                       name="last_name"
                       value="{{ old('last_name') }}"
                       class="form-input @error('last_name') is-invalid @enderror"
                       placeholder="Last name"
                       required>
                @error('last_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Company -->
        <div class="form-group">
            <label for="company" class="form-label">Company</label>
            <input type="text"
                   id="company"
                   name="company"
                   value="{{ old('company') }}"
                   class="form-input @error('company') is-invalid @enderror"
                   placeholder="Company name">
            @error('company')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="email_primary" class="form-label">Primary Email</label>
                <input type="email"
                       id="email_primary"
                       name="email_primary"
                       value="{{ old('email_primary') }}"
                       class="form-input @error('email_primary') is-invalid @enderror"
                       placeholder="primary@email.com">
                @error('email_primary')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email_secondary" class="form-label">Secondary Email</label>
                <input type="email"
                       id="email_secondary"
                       name="email_secondary"
                       value="{{ old('email_secondary') }}"
                       class="form-input @error('email_secondary') is-invalid @enderror"
                       placeholder="secondary@email.com">
                @error('email_secondary')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Phone Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="phone_primary" class="form-label">Primary Phone</label>
                <input type="tel"
                       id="phone_primary"
                       name="phone_primary"
                       value="{{ old('phone_primary') }}"
                       class="form-input @error('phone_primary') is-invalid @enderror"
                       placeholder="+27 XX XXX XXXX">
                @error('phone_primary')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone_secondary" class="form-label">Secondary Phone</label>
                <input type="tel"
                       id="phone_secondary"
                       name="phone_secondary"
                       value="{{ old('phone_secondary') }}"
                       class="form-input @error('phone_secondary') is-invalid @enderror"
                       placeholder="+27 XX XXX XXXX">
                @error('phone_secondary')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Add Person</button>
            <a href="{{ route('people.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
