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

        html, body {
            overflow-x: hidden;
            max-width: 100vw;
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            width: 100%;
        }

        .nav-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0.65rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            width: 100%;
            position: relative;
        }

        /* Brand */
        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            text-decoration: none;
            color: var(--gray-900);
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }

        .brand-badge {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 2px 6px rgba(4, 120, 87, 0.25);
            flex-shrink: 0;
        }

        .brand-tag {
            font-size: 0.675rem;
            font-weight: 700;
            background: #d1fae5;
            color: #064e3b;
            padding: 2px 6px;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: inline-block;
        }

        @media (max-width: 480px) {
            .brand-tag {
                display: none;
            }
        }

        /* Center Nav Links (Desktop & Tablet) */
        .nav-center {
            display: none;
            align-items: center;
            gap: 0.25rem;
            flex: 1;
            justify-content: center;
        }

        @media (min-width: 768px) {
            .nav-center {
                display: flex;
            }
        }

        .nav-item, .nav-dropdown-trigger {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.45rem 0.65rem;
            border-radius: var(--radius-sm);
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            min-height: 38px;
            white-space: nowrap;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            user-select: none;
        }

        .nav-item:hover, .nav-dropdown-trigger:hover {
            color: var(--gray-900);
            background: var(--gray-100);
        }

        .nav-item.active, .nav-dropdown-trigger.active {
            color: var(--primary);
            background: var(--primary-light);
            border-color: #a7f3d0;
            font-weight: 700;
        }

        .nav-item svg, .nav-dropdown-trigger svg {
            flex-shrink: 0;
        }

        .dropdown-chevron {
            transition: transform 0.2s ease;
        }

        .nav-dropdown-trigger[aria-expanded="true"] .dropdown-chevron {
            transform: rotate(180deg);
        }

        /* Dropdown Architecture */
        .nav-dropdown {
            position: relative;
            display: inline-block;
        }

        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            min-width: 220px;
            padding: 0.4rem;
            z-index: 50;
        }

        .nav-dropdown-menu.align-right {
            left: auto;
            right: 0;
        }

        .nav-dropdown-menu.show {
            display: block;
            animation: dropdownFade 0.15s ease-out;
        }

        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .nav-dropdown-section-title {
            font-size: 0.675rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            padding: 0.45rem 0.75rem 0.2rem;
        }

        .nav-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            text-decoration: none;
            border-radius: var(--radius-sm);
            transition: all 0.12s ease;
            white-space: nowrap;
        }

        .nav-dropdown-item:hover, .nav-dropdown-item:focus {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        .nav-dropdown-item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
        }

        .nav-dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.35rem 0;
        }

        /* Responsive Breakpoints: Wide Desktop vs Medium Screens */
        @media (min-width: 1140px) {
            .nav-wide-only {
                display: inline-block !important;
            }
            .nav-medium-only {
                display: none !important;
            }
        }

        @media (max-width: 1139.98px) {
            .nav-wide-only {
                display: none !important;
            }
            .nav-medium-only {
                display: inline-block !important;
            }
        }

        /* Right User Controls */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .user-pill {
            background: var(--gray-100);
            padding: 0.25rem 0.65rem 0.25rem 0.35rem;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid var(--gray-200);
        }

        .user-name-text {
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        @media (max-width: 900px) {
            .user-name-text {
                display: none;
            }
            .user-pill {
                padding: 0.2rem;
                background: transparent;
                border: none;
            }
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Mobile Hamburger Button */
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: transparent;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            color: var(--gray-700);
            cursor: pointer;
            padding: 0;
        }

        @media (min-width: 768px) {
            .mobile-menu-btn {
                display: none;
            }
        }

        /* Mobile Navigation Drawer & Backdrop */
        .mobile-drawer-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 100;
            backdrop-filter: blur(2px);
        }

        .mobile-drawer-backdrop.show {
            display: block;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 320px;
            max-width: 85vw;
            background: #ffffff;
            z-index: 101;
            display: flex;
            flex-direction: column;
            box-shadow: -4px 0 25px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            overflow-y: auto;
        }

        .mobile-drawer.show {
            transform: translateX(0);
        }

        .mobile-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .mobile-drawer-close {
            background: var(--gray-100);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-700);
            cursor: pointer;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .mobile-drawer-body {
            padding: 1rem 1.25rem;
            flex: 1;
            overflow-y: auto;
        }

        .mobile-drawer-section {
            margin-bottom: 1.25rem;
        }

        .mobile-drawer-section-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            margin-bottom: 0.4rem;
            padding-left: 0.5rem;
        }

        .mobile-drawer-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            font-size: 0.925rem;
            font-weight: 600;
            color: var(--gray-700);
            text-decoration: none;
            border-radius: var(--radius-sm);
            margin-bottom: 0.2rem;
            min-height: 44px;
        }

        .mobile-drawer-item:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        .mobile-drawer-item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
        }

        .mobile-drawer-footer {
            padding: 1.15rem 1.25rem;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
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
            padding: 0.35rem 0.5rem;
            z-index: 45;
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
            font-size: 0.725rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            gap: 2px;
            min-width: 48px;
            min-height: 48px; /* Touch target >= 44x44px */
            transition: color 0.15s ease;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .mobile-nav-item svg {
            width: 20px;
            height: 20px;
        }

        .mobile-nav-item.active {
            color: var(--primary);
            font-weight: 700;
        }

        /* Page Header Architecture */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-header-title-wrap {
            flex: 1;
            min-width: 260px;
        }

        .page-header-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.02em;
            line-height: 1.25;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .page-header-subtitle {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: 0.35rem;
            max-width: 650px;
            line-height: 1.45;
        }

        .page-header-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        @media (max-width: 540px) {
            .page-header-actions {
                width: 100%;
            }
            .page-header-actions .btn {
                flex: 1;
            }
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body>
    <!-- Accessibility: Skip to Main Content Link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Navigation Landmark -->
    <header role="banner" class="navbar">
        <div class="nav-container">
            <!-- Brand -->
            <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="brand-link" aria-label="Gigy Home">
                <span class="brand-badge" aria-hidden="true">G</span>
                <span>Gigy</span>
                <span class="brand-tag">AI Companion</span>
            </a>

            @auth
                <!-- Primary Navigation (Desktop & Tablet) -->
                <nav role="navigation" aria-label="Main Navigation" class="nav-center">
                    <!-- Workspace Destinations -->
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('earnings.index') }}" class="nav-item {{ request()->routeIs('earnings.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('earnings.*') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Earnings</span>
                    </a>
                    <a href="{{ route('analytics.index') }}" class="nav-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}" aria-current="{{ request()->routeIs('analytics.index') ? 'page' : 'false' }}">
                        <svg aria-hidden="true" focusable="false" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Analytics</span>
                    </a>

                    <!-- Wide Desktop (>=1140px): Dedicated Insights & Support Dropdowns -->
                    <div class="nav-dropdown nav-wide-only">
                        @php
                            $isInsightsActive = request()->routeIs('analytics.benchmark', 'analytics.platforms', 'analytics.complaints');
                        @endphp
                        <button type="button" class="nav-dropdown-trigger {{ $isInsightsActive ? 'active' : '' }}" aria-haspopup="true" aria-expanded="false" id="insightsDropdownBtn" onclick="toggleNavDropdown('insightsDropdownMenu', this)">
                            <svg aria-hidden="true" focusable="false" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Insights</span>
                            <svg class="dropdown-chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="nav-dropdown-menu" id="insightsDropdownMenu" role="menu" aria-labelledby="insightsDropdownBtn">
                            <a href="{{ route('analytics.benchmark') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('analytics.benchmark') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Peer Benchmark</span>
                            </a>
                            <a href="{{ route('analytics.platforms') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('analytics.platforms') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Platforms</span>
                            </a>
                            <a href="{{ route('analytics.complaints') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('analytics.complaints') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                <span>Grievance Trends</span>
                            </a>
                        </div>
                    </div>

                    <div class="nav-dropdown nav-wide-only">
                        @php
                            $isSupportActive = request()->routeIs('assistant.*', 'complaints.*');
                        @endphp
                        <button type="button" class="nav-dropdown-trigger {{ $isSupportActive ? 'active' : '' }}" aria-haspopup="true" aria-expanded="false" id="supportDropdownBtn" onclick="toggleNavDropdown('supportDropdownMenu', this)">
                            <svg aria-hidden="true" focusable="false" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Support</span>
                            <svg class="dropdown-chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="nav-dropdown-menu" id="supportDropdownMenu" role="menu" aria-labelledby="supportDropdownBtn">
                            <a href="{{ route('assistant.index') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <span>Assistant</span>
                            </a>
                            <a href="{{ route('complaints.index') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>My Disputes</span>
                            </a>
                        </div>
                    </div>

                    <!-- Medium Screens (768px-1139px): Unified "More" Dropdown -->
                    <div class="nav-dropdown nav-medium-only">
                        @php
                            $isMoreActive = request()->routeIs('analytics.benchmark', 'analytics.platforms', 'analytics.complaints', 'assistant.*', 'complaints.*');
                        @endphp
                        <button type="button" class="nav-dropdown-trigger {{ $isMoreActive ? 'active' : '' }}" aria-haspopup="true" aria-expanded="false" id="moreDropdownBtn" onclick="toggleNavDropdown('moreDropdownMenu', this)">
                            <svg aria-hidden="true" focusable="false" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            <span>More</span>
                            <svg class="dropdown-chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="nav-dropdown-menu" id="moreDropdownMenu" role="menu" aria-labelledby="moreDropdownBtn">
                            <div class="nav-dropdown-section-title">Insights</div>
                            <a href="{{ route('analytics.benchmark') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('analytics.benchmark') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Peer Benchmark</span>
                            </a>
                            <a href="{{ route('analytics.platforms') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('analytics.platforms') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Platforms</span>
                            </a>
                            <a href="{{ route('analytics.complaints') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('analytics.complaints') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                <span>Grievance Trends</span>
                            </a>

                            <div class="nav-dropdown-divider"></div>
                            <div class="nav-dropdown-section-title">Support</div>
                            <a href="{{ route('assistant.index') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <span>Assistant</span>
                            </a>
                            <a href="{{ route('complaints.index') }}" role="menuitem" class="nav-dropdown-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>My Disputes</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Right Side: Profile & Mobile Toggle -->
                <div class="nav-actions">
                    <div class="user-pill" aria-label="Logged in as {{ Auth::user()->name }}">
                        <span class="user-avatar" aria-hidden="true">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span class="user-name-text">{{ Auth::user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" aria-label="Log out of your account" style="padding: 0.4rem 0.65rem; min-height: 38px;">
                            <svg aria-hidden="true" focusable="false" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="nav-wide-only">Logout</span>
                        </button>
                    </form>

                    <!-- Mobile Hamburger Button -->
                    <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobileNavDrawer" onclick="toggleMobileDrawer(true)">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            @else
                <div class="nav-actions">
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
                </div>
            @endauth
        </div>
    </header>

    @auth
    <!-- Mobile Navigation Drawer -->
    <div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop" onclick="toggleMobileDrawer(false)" aria-hidden="true"></div>
    <div class="mobile-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Site Navigation">
        <div class="mobile-drawer-header">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="brand-badge" style="width: 32px; height: 32px; font-size: 1rem;">G</span>
                <span style="font-weight: 800; font-size: 1.15rem; color: var(--gray-900);">Gigy Navigation</span>
            </div>
            <button type="button" class="mobile-drawer-close" aria-label="Close navigation menu" onclick="toggleMobileDrawer(false)">&times;</button>
        </div>

        <div class="mobile-drawer-body">
            <!-- Workspace -->
            <div class="mobile-drawer-section">
                <div class="mobile-drawer-section-title">Workspace</div>
                <a href="{{ route('dashboard') }}" class="mobile-drawer-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('earnings.index') }}" class="mobile-drawer-item {{ request()->routeIs('earnings.*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Earnings Tracker</span>
                </a>
                <a href="{{ route('analytics.index') }}" class="mobile-drawer-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Intelligence & Analytics</span>
                </a>
            </div>

            <!-- Insights -->
            <div class="mobile-drawer-section">
                <div class="mobile-drawer-section-title">Insights & Benchmarks</div>
                <a href="{{ route('analytics.benchmark') }}" class="mobile-drawer-item {{ request()->routeIs('analytics.benchmark') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Peer Benchmark</span>
                </a>
                <a href="{{ route('analytics.platforms') }}" class="mobile-drawer-item {{ request()->routeIs('analytics.platforms') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Platforms</span>
                </a>
                <a href="{{ route('analytics.complaints') }}" class="mobile-drawer-item {{ request()->routeIs('analytics.complaints') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <span>Grievance Trends</span>
                </a>
            </div>

            <!-- Support -->
            <div class="mobile-drawer-section">
                <div class="mobile-drawer-section-title">Support & Disputes</div>
                <a href="{{ route('assistant.index') }}" class="mobile-drawer-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>AI Assistant</span>
                </a>
                <a href="{{ route('complaints.index') }}" class="mobile-drawer-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>My Disputes</span>
                </a>
            </div>
        </div>

        <div class="mobile-drawer-footer">
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.85rem;">
                <span class="user-avatar" style="width: 34px; height: 34px; font-size: 0.9rem;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--gray-900);">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-600);">{{ Auth::user()->worker_type ?? 'Gig Partner' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-secondary btn-block btn-sm" style="min-height: 40px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
    @endauth

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
        <a href="{{ route('analytics.index') }}" class="mobile-nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('analytics.*') ? 'page' : 'false' }}">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Analytics</span>
        </a>
        <a href="{{ route('assistant.index') }}" class="mobile-nav-item {{ request()->routeIs('assistant.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('assistant.*') ? 'page' : 'false' }}">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Assistant</span>
        </a>
        <button type="button" class="mobile-nav-item" onclick="toggleMobileDrawer(true)" aria-label="Open all navigation options">
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span>Menu</span>
        </button>
    </nav>
    @endauth

    <script>
        // Navigation Dropdown Handler
        function toggleNavDropdown(menuId, btn) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            const isCurrentlyOpen = menu.classList.contains('show');

            // Close all other dropdowns first
            closeAllNavDropdowns();

            if (!isCurrentlyOpen) {
                menu.classList.add('show');
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        function closeAllNavDropdowns() {
            document.querySelectorAll('.nav-dropdown-menu.show').forEach(m => {
                m.classList.remove('show');
            });
            document.querySelectorAll('.nav-dropdown-trigger[aria-expanded="true"]').forEach(b => {
                b.setAttribute('aria-expanded', 'false');
            });
        }

        // Mobile Drawer Handler
        function toggleMobileDrawer(open) {
            const drawer = document.getElementById('mobileNavDrawer');
            const backdrop = document.getElementById('mobileDrawerBackdrop');
            const btn = document.getElementById('mobileMenuBtn');

            if (!drawer || !backdrop) return;

            if (open) {
                drawer.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
                if (btn) btn.setAttribute('aria-expanded', 'true');
                const closeBtn = drawer.querySelector('.mobile-drawer-close');
                if (closeBtn) closeBtn.focus();
            } else {
                drawer.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
                if (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                    btn.focus();
                }
            }
        }

        // Global Event Listeners: Click outside & Escape Key
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.nav-dropdown')) {
                closeAllNavDropdowns();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllNavDropdowns();
                const drawer = document.getElementById('mobileNavDrawer');
                if (drawer && drawer.classList.contains('show')) {
                    toggleMobileDrawer(false);
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
