<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Community Led Engagement</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Leaflet.js for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <!-- Flatpickr for Date Pickers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @vite(['resources/css/admin.css', 'resources/js/app.js'])

    <style>
/* ===== Modern Admin Layout Styles ===== */

:root {
    /* Primary Colors - Modern Green */
    --primary-50: #ecfdf5;
    --primary-100: #d1fae5;
    --primary-200: #a7f3d0;
    --primary-300: #6ee7b7;
    --primary-400: #34d399;
    --primary-500: #10b981;
    --primary-600: #059669;
    --primary-700: #047857;
    --primary-800: #065f46;
    --primary-900: #064e3b;

    /* Neutral Colors */
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;

    /* Semantic Colors */
    --success: #10b981;
    --success-light: #d1fae5;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --error: #ef4444;
    --error-light: #fee2e2;
    --info: #3b82f6;
    --info-light: #dbeafe;

    /* Layout */
    --sidebar-width: 280px;
    --sidebar-collapsed-width: 80px;
    --header-height: 72px;
    --footer-height: 56px;

    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

    /* Border Radius */
    --radius-sm: 6px;
    --radius: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;

    /* Transitions */
    --transition-fast: 150ms ease;
    --transition: 200ms ease;
    --transition-slow: 300ms ease;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    height: 100%;
    scroll-behavior: smooth;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: var(--gray-100);
    color: var(--gray-800);
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ===== Layout Structure ===== */
.admin-layout {
    display: flex;
    min-height: 100vh;
}

/* ===== Sidebar ===== */
.sidebar {
    width: var(--sidebar-width);
    background: white;
    position: fixed;
    height: 100vh;
    left: 0;
    top: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: all var(--transition-slow);
    box-shadow: var(--shadow-md);
    border-right: 1px solid var(--gray-200);
}

.sidebar-header {
    padding: 24px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.sidebar-logo {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    object-fit: contain;
    background: white;
    padding: 4px;
    border: 1px solid var(--gray-200);
}

.sidebar-brand {
    flex: 1;
}

.sidebar-brand h1 {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.3;
}

.sidebar-brand span {
    font-size: 11px;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

/* Navigation */
.sidebar-nav {
    flex: 1;
    padding: 16px 12px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--gray-300) transparent;
}

.sidebar-nav::-webkit-scrollbar {
    width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 3px;
}

.nav-section {
    margin-bottom: 24px;
}

.nav-section-title {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 0 12px;
    margin-bottom: 8px;
}

.nav-menu {
    list-style: none;
}

.nav-item {
    margin-bottom: 4px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: var(--radius);
    color: var(--gray-600);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all var(--transition);
    position: relative;
}

.nav-link:hover {
    background: var(--gray-100);
    color: var(--gray-900);
}

.nav-link.active {
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 24px;
    background: white;
    border-radius: 0 3px 3px 0;
}

.nav-link svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    opacity: 0.7;
}

.nav-link:hover svg {
    opacity: 1;
}

.nav-link.active svg {
    opacity: 1;
}

/* Collapsible Submenu */
.nav-group .nav-toggle {
    width: 100%;
    justify-content: flex-start;
    cursor: pointer;
    border: none;
    background: transparent;
    font-family: inherit;
    color: var(--gray-600);
}

.nav-group .nav-toggle:hover {
    background: var(--gray-100);
    color: var(--gray-900);
}

.nav-group .nav-toggle.expanded {
    color: var(--gray-900);
}

.nav-toggle .nav-chevron {
    margin-left: auto;
    transition: transform var(--transition);
    width: 16px;
    height: 16px;
    color: var(--gray-400);
}

.nav-toggle:hover .nav-chevron {
    color: var(--gray-600);
}

.nav-toggle.expanded .nav-chevron {
    transform: rotate(180deg);
    color: var(--gray-600);
}

.nav-submenu {
    display: none;
    padding-left: 20px;
    margin-top: 4px;
}

.nav-submenu.open {
    display: block;
}

.nav-submenu .nav-link {
    padding: 10px 14px 10px 12px;
    font-size: 13px;
}

.nav-submenu .nav-link::before {
    content: '';
    width: 6px;
    height: 6px;
    background: var(--gray-300);
    border-radius: 50%;
    margin-right: 10px;
    flex-shrink: 0;
    transition: all var(--transition);
}

.nav-submenu .nav-link:hover::before {
    background: var(--primary-500);
}

.nav-submenu .nav-link.active::before {
    background: white;
}

/* Nested Submenu */
.nav-nested-submenu {
    display: none;
    padding-left: 16px;
    margin-top: 4px;
}

.nav-nested-submenu.open {
    display: block;
}

.nav-nested-toggle {
    font-size: 13px !important;
    padding: 10px 14px !important;
}

/* ===== Main Content Area ===== */
.main-wrapper {
    flex: 1;
    margin-left: var(--sidebar-width);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    transition: margin-left var(--transition-slow);
}

/* ===== Header ===== */
.header {
    height: var(--header-height);
    background: white;
    border-bottom: 1px solid var(--gray-200);
    position: sticky;
    top: 0;
    z-index: 900;
    box-shadow: var(--shadow-sm);
}

.header-content {
    height: 100%;
    padding: 0 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.mobile-menu-btn {
    display: none;
    width: 40px;
    height: 40px;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
    background: white;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
}

.mobile-menu-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
}

.header-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-500);
}

.header-breadcrumb a {
    color: var(--gray-500);
    text-decoration: none;
    transition: color var(--transition);
}

.header-breadcrumb a:hover {
    color: var(--primary-600);
}

.header-breadcrumb .current {
    color: var(--gray-800);
    font-weight: 600;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    letter-spacing: -0.5px;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

/* Header Buttons */
.header-btn {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-md);
    border: none;
    background: var(--gray-100);
    color: var(--gray-600);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
    position: relative;
}

.header-btn:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

.header-btn .badge-dot {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    background: var(--error);
    border-radius: 50%;
    border: 2px solid white;
}

/* User Menu */
.user-menu {
    position: relative;
}

.user-menu-trigger {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 6px 12px 6px 6px;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    background: white;
    cursor: pointer;
    transition: all var(--transition);
}

.user-menu-trigger:hover {
    border-color: var(--gray-300);
    box-shadow: var(--shadow-sm);
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: var(--radius);
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.user-info {
    text-align: left;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-800);
    line-height: 1.2;
}

.user-role {
    font-size: 12px;
    color: var(--gray-500);
}

.user-menu-trigger svg {
    width: 16px;
    height: 16px;
    color: var(--gray-400);
}

/* Dropdown Menu */
.dropdown-menu {
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    min-width: 220px;
    padding: 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all var(--transition);
    z-index: 1000;
}

.dropdown-menu.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    padding: 12px;
    border-bottom: 1px solid var(--gray-100);
    margin-bottom: 8px;
}

.dropdown-user {
    display: flex;
    gap: 12px;
    align-items: center;
}

.dropdown-user .user-avatar {
    width: 40px;
    height: 40px;
}

.dropdown-user-info h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-800);
}

.dropdown-user-info p {
    font-size: 12px;
    color: var(--gray-500);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: var(--radius);
    color: var(--gray-700);
    text-decoration: none;
    font-size: 14px;
    transition: all var(--transition);
    border: none;
    background: none;
    width: 100%;
    cursor: pointer;
}

.dropdown-item:hover {
    background: var(--gray-100);
    color: var(--gray-900);
}

.dropdown-item svg {
    width: 18px;
    height: 18px;
    opacity: 0.7;
}

.dropdown-divider {
    height: 1px;
    background: var(--gray-100);
    margin: 8px 0;
}

.dropdown-item.danger {
    color: var(--error);
}

.dropdown-item.danger:hover {
    background: var(--error-light);
}

/* ===== Main Content ===== */
.main-content {
    flex: 1;
    padding: 32px;
    background: var(--gray-100);
}

/* ===== Footer ===== */
.footer {
    height: var(--footer-height);
    background: white;
    border-top: 1px solid var(--gray-200);
    padding: 0 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.footer-left {
    font-size: 13px;
    color: var(--gray-500);
}

.footer-left a {
    color: var(--primary-600);
    text-decoration: none;
    font-weight: 500;
}

.footer-left a:hover {
    text-decoration: underline;
}

.footer-right {
    display: flex;
    gap: 24px;
    font-size: 13px;
}

.footer-link {
    color: var(--gray-500);
    text-decoration: none;
    transition: color var(--transition);
}

.footer-link:hover {
    color: var(--gray-700);
}

/* ===== Alert Messages ===== */
.alert {
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 500;
}

.alert-success {
    background: var(--success-light);
    color: var(--primary-800);
    border: 1px solid var(--primary-200);
}

.alert-error {
    background: var(--error-light);
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-warning {
    background: var(--warning-light);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.alert-info {
    background: var(--info-light);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.alert-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity var(--transition);
}

.alert-close:hover {
    opacity: 1;
}

/* ===== Flatpickr Custom Styles ===== */
.flatpickr-calendar {
    font-family: 'Inter', sans-serif;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--gray-200);
}

.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background: var(--primary-600);
    border-color: var(--primary-600);
}

.flatpickr-day.today {
    border-color: var(--primary-400);
}

.flatpickr-day:hover {
    background: var(--primary-100);
    border-color: var(--primary-100);
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.open {
        transform: translateX(0);
    }

    .main-wrapper {
        margin-left: 0;
    }

    .mobile-menu-btn {
        display: flex;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .sidebar-overlay.active {
        display: block;
    }
}

@media (max-width: 768px) {
    .header-content {
        padding: 0 16px;
    }

    .main-content {
        padding: 16px;
    }

    .footer {
        flex-direction: column;
        height: auto;
        padding: 16px;
        gap: 12px;
        text-align: center;
    }

    .footer-right {
        gap: 16px;
    }

    .user-info {
        display: none;
    }

    .page-title {
        font-size: 20px;
    }
}
</style>

    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/epi-logo.png') }}" alt="EPI Logo" class="sidebar-logo">
                <div class="sidebar-brand">
                    <h1>Community Led Engagement</h1>
                    <span>Admin Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Core Forms Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Data Collection</div>
                    <ul class="nav-menu">
                        <li class="nav-item nav-group">
                            <button type="button" class="nav-link nav-toggle {{ request()->routeIs('admin.child-line-list.*', 'admin.fgds-community.*', 'admin.fgds-health-workers.*', 'admin.bridging-the-gap.*', 'admin.outreach-sites.*') ? 'active expanded' : '' }}" data-toggle="core-forms-submenu">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 11l3 3L22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                <span>Core Forms</span>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                            <ul class="nav-submenu {{ request()->routeIs('admin.child-line-list.*', 'admin.fgds-community.*', 'admin.fgds-health-workers.*', 'admin.bridging-the-gap.*', 'admin.outreach-sites.*') ? 'open' : '' }}" id="core-forms-submenu">
                                <li class="nav-item">
                                    <a href="{{ route('admin.child-line-list.index') }}" class="nav-link {{ request()->routeIs('admin.child-line-list.*') ? 'active' : '' }}">
                                        Child Line List
                                    </a>
                                </li>
                                <!-- Exploring Immunization Barriers -->
                                <li class="nav-item nav-group">
                                    <button type="button" class="nav-link nav-toggle nav-nested-toggle {{ request()->routeIs('admin.fgds-community.*', 'admin.fgds-health-workers.*') ? 'active expanded' : '' }}" data-toggle="barriers-submenu">
                                        <span>Exploring Barriers</span>
                                        <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"/>
                                        </svg>
                                    </button>
                                    <ul class="nav-nested-submenu {{ request()->routeIs('admin.fgds-community.*', 'admin.fgds-health-workers.*') ? 'open' : '' }}" id="barriers-submenu">
                                        <li class="nav-item">
                                            <a href="{{ route('admin.fgds-community.index') }}" class="nav-link {{ request()->routeIs('admin.fgds-community.*') ? 'active' : '' }}">
                                                FGDs-Community
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.fgds-health-workers.index') }}" class="nav-link {{ request()->routeIs('admin.fgds-health-workers.*') ? 'active' : '' }}">
                                                FGDs-Health Workers
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.bridging-the-gap.index') }}" class="nav-link {{ request()->routeIs('admin.bridging-the-gap.*') ? 'active' : '' }}">
                                        Bridging The Gap
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.outreach-sites.index') }}" class="nav-link {{ request()->routeIs('admin.outreach-sites.*') ? 'active' : '' }}">
                                        Vaccination Sites
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.forms.index') }}" class="nav-link {{ request()->routeIs('admin.forms.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="12" y1="18" x2="12" y2="12"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                <span>Dynamic Forms</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.submissions.index') }}" class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                <span>Submissions</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Administration Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Administration</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="4 17 10 11 4 5"/>
                                    <line x1="12" y1="19" x2="20" y2="19"/>
                                </svg>
                                <span>Activity Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="main-wrapper">
            <!-- Header -->
            <header class="header">
                <div class="header-content">
                    <div class="header-left">
                        <button class="mobile-menu-btn" id="mobileMenuBtn" type="button" aria-label="Toggle menu">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <line x1="3" y1="18" x2="21" y2="18"/>
                            </svg>
                        </button>
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="header-right">
                        <div class="user-menu" id="userMenu">
                            <button class="user-menu-trigger" type="button" aria-label="User menu">
                                <div class="user-avatar">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                                    <div class="user-role">Administrator</div>
                                </div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu" id="userDropdown">
                                <div class="dropdown-header">
                                    <div class="dropdown-user">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 1)) }}
                                        </div>
                                        <div class="dropdown-user-info">
                                            <h4>{{ auth()->user()->name ?? 'Admin' }}</h4>
                                            <p>{{ auth()->user()->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.index') }}" class="dropdown-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                    Manage Users
                                </a>
                                <a href="{{ route('admin.logs.index') }}" class="dropdown-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="4 17 10 11 4 5"/>
                                        <line x1="12" y1="19" x2="20" y2="19"/>
                                    </svg>
                                    Activity Logs
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <polyline points="16 17 21 12 16 7"/>
                                            <line x1="21" y1="12" x2="9" y2="12"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="main-content">
                @if (session('success'))
                    <div class="alert alert-success">
                        <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="footer">
                <div class="footer-left">
                    &copy; {{ date('Y') }} <a href="#">Community Led Engagement</a>. All rights reserved.
                </div>
                <div class="footer-right">
                    <span>Version 1.0.0</span>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                    sidebarOverlay.classList.toggle('active');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('active');
                });
            }

            // User dropdown menu
            const userMenu = document.getElementById('userMenu');
            const userDropdown = document.getElementById('userDropdown');

            if (userMenu && userDropdown) {
                const trigger = userMenu.querySelector('.user-menu-trigger');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userDropdown.classList.toggle('open');
                });

                document.addEventListener('click', (e) => {
                    if (!userMenu.contains(e.target)) {
                        userDropdown.classList.remove('open');
                    }
                });
            }

            // Sidebar submenu toggle
            const toggleButtons = document.querySelectorAll('.nav-toggle');
            toggleButtons.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetId = btn.getAttribute('data-toggle');
                    const submenu = document.getElementById(targetId);
                    if (submenu) {
                        submenu.classList.toggle('open');
                        btn.classList.toggle('expanded');
                    }
                });
            });

            // Initialize Flatpickr on all date inputs
            if (typeof flatpickr !== 'undefined') {
                flatpickr('input[type="date"]', {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    altInput: true,
                    altFormat: 'M d, Y',
                    animate: true
                });
            }

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
