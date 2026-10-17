<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gigy') }} - @yield('title', 'AI Companion for Gig Workers')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* WCAG 2.2 AA Contrast Compliant Palette (>= 4.5:1 for normal text, >= 3:1 for large/graphical) */
            --primary: #047857;
            --primary-hover: #065f46;
            --primary-light: #ecfdf5;
            --primary-dark: #064e3b;
            --primary-focus: #047857;
            --accent: #b45309;
            --accent-light: #fef3c7;
            --danger: #b91c1c;
            --danger-hover: #991b1b;
            --danger-light: #fef2f2;
            --dark: #0f172a;
            --dark-card: #1e293b;
            --dark-border: #334155;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-600: #334155; /* High contrast: 7.0:1 */
            --gray-700: #1e293b; /* High contrast: 10.5:1 */
            --gray-800: #0f172a;
            --gray-900: #020617;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Accessibility: Respect user motion preferences */
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 75px; /* space for mobile bottom bar */
        }

        @media (min-width: 768px) {
            body {
                padding-bottom: 0;
            }
        }

        /* Accessibility: Skip to Main Content Link */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 1rem;
            background: #047857;
            color: #ffffff;
            padding: 0.5rem 1rem;
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: var(--radius-sm);
            z-index: 100;
            text-decoration: none;
            transition: top 0.2s ease;
        }

        .skip-link:focus {
            top: 1rem;
            outline: 3px solid #0f172a;
            outline-offset: 2px;
        }

        /* Accessibility: Focus Visible outlines */
        :focus-visible {
            outline: 3px solid #047857 !important;
            outline-offset: 2px !important;
        }

        /* Top Navbar */
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: var(--gray-900);
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.02em;
        }

        .brand-badge {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: #ffffff;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.3);
        }

        .brand-tag {
            font-size: 0.7rem;
            font-weight: 700;
            background: #d1fae5;
            color: #064e3b;
            padding: 2px 7px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .nav-links {
            display: none;
            gap: 1.5rem;
            align-items: center;
        }

        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }
        }

        .nav-item {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.925rem;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            min-height: 40px;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--primary);
            background: var(--primary-light);
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-pill {
            background: var(--gray-100);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Mobile Bottom Bar */
        .mobile-bottom-bar {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--gray-200);
            padding: 0.4rem 0.5rem;
            z-index: 50;
            justify-content: space-around;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 768px) {
            .mobile-bottom-bar {
                display: none;
            }
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-600);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            gap: 2px;
            min-width: 48px;
            min-height: 48px; /* Touch target >= 44x44px */
            transition: color 0.15s ease;
        }

        .mobile-nav-item svg {
            width: 22px;
            height: 22px;
        }

        .mobile-nav-item.active {
            color: var(--primary);
        }

        /* Layout Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 1.25rem;
            width: 100%;
            flex: 1;
        }

        /* Alerts & Flash Messages */
        .alert {
            padding: 0.85rem 1.15rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.25rem;
            font-size: 0.925rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: #ecfdf5;
            color: #064e3b;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #7f1d1d;
            border: 1px solid #fecaca;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
        }

        /* Buttons (WCAG AA Touch target >= 44px) */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            border: 1px solid transparent;
            min-height: 44px;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(4, 120, 87, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--gray-700);
            border-color: var(--gray-300);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-600);
        }

        .btn-danger {
            background: var(--danger);
            color: #ffffff;
        }

        .btn-danger:hover {
            background: var(--danger-hover);
        }

        .btn-sm {
            padding: 0.4rem 0.85rem;
            font-size: 0.825rem;
            border-radius: var(--radius-sm);
            min-height: 38px;
        }

        .btn-lg {
            padding: 0.85rem 1.6rem;
            font-size: 1rem;
            border-radius: var(--radius-md);
            min-height: 48px;
        }

        .btn-block {
            width: 100%;
        }

        /* Cards */
        .card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-100);
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 0.35rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.95rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--gray-900);
            background: #ffffff;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-md);
            min-height: 44px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.2);
        }

        .form-control[aria-invalid="true"] {
            border-color: var(--danger);
            background: #fffafa;
        }

        .form-text {
            font-size: 0.775rem;
            color: var(--gray-600);
            margin-top: 0.3rem;
            line-height: 1.4;
        }

        .form-error {
            color: var(--danger);
            font-size: 0.825rem;
            font-weight: 600;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Badges with WCAG AA Contrast */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 800;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-success { background: #d1fae5; color: #064e3b; }
        .badge-warning { background: #fef3c7; color: #78350f; }
        .badge-danger { background: #fee2e2; color: #7f1d1d; }
        .badge-info { background: #e0e7ff; color: #1e1b4b; }
        .badge-neutral { background: #e2e8f0; color: #0f172a; }

        /* Currency INR formatting helpers */
        .currency {
            font-feature-settings: 'tnum';
            font-variant-numeric: tabular-nums;
            font-weight: 700;
        }

        /* Metric Grid */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .metric-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.15rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }

        .metric-label {
            font-size: 0.775rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1.2;
        }

        .metric-sub {
            font-size: 0.775rem;
            color: var(--gray-600);
            margin-top: 0.3rem;
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            text-align: left;
        }

        .table caption {
            text-align: left;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gray-600);
            padding: 0.5rem 0;
        }

        .table th {
            background: var(--gray-50);
            padding: 0.75rem 1rem;
            font-weight: 700;
            font-size: 0.775rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-700);
            border-bottom: 2px solid var(--gray-200);
        }

        .table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-800);
            vertical-align: middle;
        }

        .table tr:hover td {
            background: #f8fafc;
        }

        /* Empty states */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--gray-600);
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 1rem;
            background: var(--gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-700);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Accessibility: Skip to Main Content Link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Navigation Landmark -->
    <header role="banner" class="navbar">
        <div class="nav-container">
            <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="brand-link" aria-label="Gigy Home">
                <span class="brand-badge" aria-hidden="true">G</span>
                <span>Gigy</span>
                <span class="brand-tag">AI Companion</span>
            </a>

            @auth
                <nav role="navigation" aria-label="Main Navigation" class="nav-links">
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('earnings.index') }}" class="nav-item {{ request()->routeIs('earnings.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('earnings.*') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Earnings
                    </a>
                    <a href="{{ route('assistant.index') }}" class="nav-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('assistant.*') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Worker Assistant
                    </a>
                    <a href="{{ route('complaints.index') }}" class="nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('complaints.*') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Complaints
                    </a>
                </nav>

                <div class="user-dropdown">
                    <div class="user-pill" aria-label="Logged in as {{ Auth::user()->name }}">
                        <span class="user-avatar" aria-hidden="true">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" aria-label="Log out of your account">
                            <svg aria-hidden="true" focusable="false" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @else
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Landmark -->
    <main id="main-content" role="main" class="main-container" tabindex="-1">
        @if(session('success'))
            <div class="alert alert-success" role="alert" aria-live="polite">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" aria-label="Close success alert" style="background:none;border:none;cursor:pointer;font-weight:700;font-size:1.1rem;color:inherit;padding:0.25rem 0.5rem;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" role="alert" aria-live="assertive">
                <span>✕ {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" aria-label="Close error alert" style="background:none;border:none;cursor:pointer;font-weight:700;font-size:1.1rem;color:inherit;padding:0.25rem 0.5rem;">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info" role="alert" aria-live="polite">
                <span>ℹ {{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.remove()" aria-label="Close info alert" style="background:none;border:none;cursor:pointer;font-weight:700;font-size:1.1rem;color:inherit;padding:0.25rem 0.5rem;">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Landmark -->
    @auth
    <nav role="navigation" aria-label="Mobile Navigation" class="mobile-bottom-bar">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <a href="{{ route('earnings.index') }}" class="mobile-nav-item {{ request()->routeIs('earnings.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('earnings.*') ? 'page' : 'false' }}">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Earnings</span>
        </a>
        <a href="{{ route('assistant.index') }}" class="mobile-nav-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('assistant.*') ? 'page' : 'false' }}">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Assistant</span>
        </a>
        <a href="{{ route('complaints.index') }}" class="mobile-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('complaints.*') ? 'page' : 'false' }}">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Complaints</span>
        </a>
    </nav>
    @endauth

    @stack('scripts')
</body>
</html>
