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
            --primary: #059669;
            --primary-hover: #047857;
            --primary-light: #d1fae5;
            --primary-dark: #064e3b;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --dark: #0f172a;
            --dark-card: #1e293b;
            --dark-border: #334155;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
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

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
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
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
        }

        .brand-tag {
            font-size: 0.7rem;
            font-weight: 700;
            background: #d1fae5;
            color: #065f46;
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
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
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
            padding: 0.5rem 0.5rem;
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
            text-decoration: none;
            color: var(--gray-600);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            gap: 2px;
            transition: color 0.15s ease;
        }

        .mobile-nav-item svg {
            width: 20px;
            height: 20px;
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
            animation: fadeIn 0.2s ease-in;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.6rem 1.15rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(5, 150, 105, 0.2);
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
            border-color: var(--gray-400);
        }

        .btn-danger {
            background: var(--danger);
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.825rem;
            border-radius: var(--radius-sm);
        }

        .btn-lg {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            border-radius: var(--radius-md);
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
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.35rem;
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 0.9rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--gray-900);
            background: #ffffff;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        }

        .form-text {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
        }

        .form-error {
            color: var(--danger);
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.25rem;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0e7ff; color: #3730a3; }
        .badge-neutral { background: var(--gray-200); color: var(--gray-700); }

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
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1.2;
        }

        .metric-sub {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
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

        .table th {
            background: var(--gray-50);
            padding: 0.75rem 1rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            border-bottom: 1px solid var(--gray-200);
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
            color: var(--gray-600);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Navigation -->
    <header class="navbar">
        <div class="nav-container">
            <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="brand-link">
                <span class="brand-badge">G</span>
                <span>Gigy</span>
                <span class="brand-tag">AI Companion</span>
            </a>

            @auth
                <nav class="nav-links">
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('earnings.index') }}" class="nav-item {{ request()->routeIs('earnings.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Earnings
                    </a>
                    <a href="{{ route('assistant.index') }}" class="nav-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Worker Assistant
                    </a>
                    <a href="{{ route('complaints.index') }}" class="nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Complaints
                    </a>
                </nav>

                <div class="user-dropdown">
                    <div class="user-pill">
                        <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" title="Log Out">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span style="display:none; @media(min-width:768px){display:inline;}">Logout</span>
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

    <!-- Main Content -->
    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:700;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span>✕ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:700;">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <span>ℹ {{ session('info') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:700;">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    @auth
    <nav class="mobile-bottom-bar">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <a href="{{ route('earnings.index') }}" class="mobile-nav-item {{ request()->routeIs('earnings.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Earnings</span>
        </a>
        <a href="{{ route('assistant.index') }}" class="mobile-nav-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>AI Assistant</span>
        </a>
        <a href="{{ route('complaints.index') }}" class="mobile-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Complaints</span>
        </a>
    </nav>
    @endauth

    @stack('scripts')
</body>
</html>
