@extends('layouts.app')

@section('title', 'Login - Access Gigy')

@section('content')
<div style="max-width: 440px; margin: 2rem auto;">
    <div class="card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="brand-badge" style="margin: 0 auto 0.75rem; width: 44px; height: 44px; font-size: 1.4rem;">G</div>
            <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--gray-900);">Welcome Back</h2>
            <p style="font-size: 0.85rem; color: var(--gray-600); margin-top: 0.25rem;">Log in to access your earnings, AI assistant, and complaints.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="••••••••" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: var(--gray-700); cursor: pointer;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Log In to Gigy
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.25rem; font-size: 0.85rem; color: var(--gray-600);">
            Don't have an account yet? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Register here</a>
        </div>
    </div>
</div>
@endsection
