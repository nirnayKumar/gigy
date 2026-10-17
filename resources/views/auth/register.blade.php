@extends('layouts.app')

@section('title', 'Register - Join Gigy')

@section('content')
<div style="max-width: 480px; margin: 1.5rem auto;">
    <div class="card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="brand-badge" aria-hidden="true" style="margin: 0 auto 0.75rem; width: 44px; height: 44px; font-size: 1.4rem;">G</div>
            <h1 style="font-size: 1.45rem; font-weight: 800; color: var(--gray-900);">Create your Gigy Account</h1>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.25rem;">Start tracking your real net earnings and protecting your payouts.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Full Name <span aria-hidden="true">*</span></label>
                <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Ramesh Kumar" autocomplete="name" required autofocus aria-required="true" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')
                    <div id="name-error" class="form-error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address <span aria-hidden="true">*</span></label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@example.com" autocomplete="email" required aria-required="true" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <div id="email-error" class="form-error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="worker_type" class="form-label">Primary Gig Work Type</label>
                <select id="worker_type" name="worker_type" class="form-control">
                    <option value="Food Delivery (Zomato/Swiggy)" {{ old('worker_type') == 'Food Delivery (Zomato/Swiggy)' ? 'selected' : '' }}>Food Delivery (Zomato/Swiggy)</option>
                    <option value="Ride-hail Cab/Auto (Uber/Ola/Rapido)" {{ old('worker_type') == 'Ride-hail Cab/Auto (Uber/Ola/Rapido)' ? 'selected' : '' }}>Ride-hail Cab/Auto (Uber/Ola/Rapido)</option>
                    <option value="Quick Commerce (Zepto/Blinkit/Instamart)" {{ old('worker_type') == 'Quick Commerce (Zepto/Blinkit/Instamart)' ? 'selected' : '' }}>Quick Commerce (Zepto/Blinkit/Instamart)</option>
                    <option value="Logistics & Courier (Porter/Shadowfax)" {{ old('worker_type') == 'Logistics & Courier (Porter/Shadowfax)' ? 'selected' : '' }}>Logistics & Courier (Porter/Shadowfax)</option>
                    <option value="Other Informal / Freelance Work" {{ old('worker_type') == 'Other Informal / Freelance Work' ? 'selected' : '' }}>Other Informal / Freelance Work</option>
                </select>
                @error('worker_type')
                    <div class="form-error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="preferred_language" class="form-label">Preferred Language for AI Guidance</label>
                <select id="preferred_language" name="preferred_language" class="form-control">
                    <option value="English" {{ old('preferred_language') == 'English' ? 'selected' : '' }}>English</option>
                    <option value="Hindi" {{ old('preferred_language') == 'Hindi' ? 'selected' : '' }}>Hindi (हिंदी)</option>
                    <option value="Punjabi" {{ old('preferred_language') == 'Punjabi' ? 'selected' : '' }}>Punjabi (ਪੰਜਾਬੀ)</option>
                </select>
                @error('preferred_language')
                    <div class="form-error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password <span aria-hidden="true">*</span></label>
                <input id="password" type="password" name="password" class="form-control" placeholder="Minimum 8 characters" autocomplete="new-password" required aria-required="true" aria-describedby="password-help @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror>
                <div id="password-help" class="form-text">Must be at least 8 characters long.</div>
                @error('password')
                    <div id="password-error" class="form-error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password <span aria-hidden="true">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Re-enter your password" autocomplete="new-password" required aria-required="true">
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1.25rem;">
                Create Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.25rem; font-size: 0.875rem; color: var(--gray-600);">
            Already have an account? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: underline;">Log In here</a>
        </div>
    </div>
</div>
@endsection
