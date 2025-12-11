<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Community Lead Engagement</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    
    <style>
.layout {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: var(--nav-width);
    background-color: var(--color-bg-paper);
    border-right: 1px solid var(--color-border);
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    z-index: 100;
}

.sidebar-header {
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--color-border);
}

.logo {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.logo-text {
    font-size: 20px;
    font-weight: 700;
    color: var(--color-text-primary);
}

.sidebar-nav {
    flex: 1;
    padding: var(--spacing-md);
    overflow-y: auto;
}

.sidebar-nav ul {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.nav-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    color: var(--color-text-secondary);
    font-weight: 600;
    transition: all 0.2s;
}

.nav-link:hover {
    background-color: var(--color-bg-neutral);
    color: var(--color-text-primary);
}

.nav-link.active {
    background-color: var(--color-primary-lighter);
    color: var(--color-primary-dark);
}

.nav-link svg {
    width: 24px;
    height: 24px;
}

/* Submenu styles */
.nav-group {
    list-style: none;
}

.nav-toggle {
    width: 100%;
    justify-content: flex-start;
    cursor: pointer;
    border: none;
    background: none;
}

.nav-toggle .chevron {
    margin-left: auto;
    transition: transform 0.2s ease;
    width: 16px;
    height: 16px;
}

.nav-toggle.expanded .chevron,
.nav-toggle.active .chevron {
    transform: rotate(180deg);
}

.nav-submenu {
    display: none;
    padding-left: 16px;
    margin-top: 4px;
    flex-direction: column;
    gap: 2px;
}

.nav-submenu.open {
    display: flex;
}

.nav-link.sub-link {
    padding: 8px 12px 8px 24px;
    font-size: 14px;
}

.main-wrapper {
    flex: 1;
    margin-left: var(--nav-width);
    display: flex;
    flex-direction: column;
}

.header {
    height: var(--header-height);
    background-color: var(--color-bg-paper);
    border-bottom: 1px solid var(--color-border);
    position: sticky;
    top: 0;
    z-index: 90;
}

.header-content {
    height: 100%;
    padding: 0 var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-text-primary);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--color-primary-main);
    color: var(--color-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    cursor: pointer;
}

.user-menu { position: relative; }

.dropdown {
    position: absolute;
    right: 0;
    top: 48px;
    background: var(--color-bg-paper);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-dropdown);
    border-radius: var(--radius-md);
    min-width: 220px;
    padding: var(--spacing-sm);
    display: grid;
    gap: var(--spacing-xs);
    z-index: 120;
}

.dropdown-header {
    display: flex;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm);
    border-bottom: 1px solid var(--color-border);
    margin-bottom: var(--spacing-xs);
}

.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--color-primary-lighter);
    color: var(--color-primary-darker);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.dropdown-item {
    width: 100%;
    text-align: left;
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    color: var(--color-text-primary);
    background: transparent;
    border: none;
    cursor: pointer;
    display: block;
}

.dropdown-item:hover {
    background: var(--color-bg-neutral);
}

[data-user-menu] .dropdown[hidden] { display: none; }

.main-content {
    flex: 1;
    padding: var(--spacing-lg);
    background-color: var(--color-bg-neutral);
}

.alert {
    padding: var(--spacing-md);
    border-radius: var(--radius-sm);
    margin-bottom: var(--spacing-lg);
    font-weight: 600;
}

.alert-success {
    background-color: var(--color-success-lighter);
    color: var(--color-success-darker);
    border: 1px solid var(--color-success-light);
}

.alert-error {
    background-color: var(--color-error-lighter);
    color: var(--color-error-darker);
    border: 1px solid var(--color-error-light);
}
</style>

    @stack('styles')
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/epi-logo.png') }}" alt="EPI Logo" style="width: 36px; height: 36px; object-fit: contain;">
                    <span class="logo-text">Community Lead Engagement</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Core Forms Dropdown -->
                    <li class="nav-group">
                        <button type="button" class="nav-link nav-toggle {{ request()->routeIs('admin.area-mappings.*', 'admin.draft-lists.*', 'admin.religious-leaders.*', 'admin.community-barriers.*', 'admin.healthcare-barriers.*') ? 'active' : '' }}" data-toggle="core-forms">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span>Core Forms</span>
                            <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <ul class="nav-submenu {{ request()->routeIs('admin.area-mappings.*', 'admin.draft-lists.*', 'admin.religious-leaders.*', 'admin.community-barriers.*', 'admin.healthcare-barriers.*') ? 'open' : '' }}" id="core-forms">
                            <li>
                                <a href="{{ route('admin.area-mappings.index') }}" class="nav-link sub-link {{ request()->routeIs('admin.area-mappings.*') ? 'active' : '' }}">
                                    <span>Area Mappings</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.draft-lists.index') }}" class="nav-link sub-link {{ request()->routeIs('admin.draft-lists.*') ? 'active' : '' }}">
                                    <span>Draft Lists</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.religious-leaders.index') }}" class="nav-link sub-link {{ request()->routeIs('admin.religious-leaders.*') ? 'active' : '' }}">
                                    <span>Religious Leaders</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.community-barriers.index') }}" class="nav-link sub-link {{ request()->routeIs('admin.community-barriers.*') ? 'active' : '' }}">
                                    <span>Community Barriers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.healthcare-barriers.index') }}" class="nav-link sub-link {{ request()->routeIs('admin.healthcare-barriers.*') ? 'active' : '' }}">
                                    <span>Healthcare Barriers</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('admin.forms.index') }}" class="nav-link {{ request()->routeIs('admin.forms.*') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <span>Dynamic Forms</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.submissions.index') }}" class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>Submissions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="4 17 10 11 4 5"></polyline>
                                <line x1="12" y1="19" x2="20" y2="19"></line>
                            </svg>
                            <span>Activity Logs</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-wrapper">
            <!-- Header -->
            <header class="header">
                <div class="header-content">
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    
                    <div class="header-actions">
                        <div class="user-menu" data-user-menu>
                            <button class="user-avatar" type="button" aria-label="Profile menu">
                                <span>{{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 1)) }}</span>
                            </button>
                            <div class="dropdown" hidden>
                                <div class="dropdown-header">
                                    <div class="avatar-sm">{{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 1)) }}</div>
                                    <div>
                                        <div class="text-primary" style="font-weight:700;">{{ auth()->user()->name ?? 'Admin' }}</div>
                                        <div class="text-secondary" style="font-size:12px;">{{ auth()->user()->email ?? '' }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.index') }}" class="dropdown-item">Manage Users</a>
                                <a href="{{ route('admin.logs.index') }}" class="dropdown-item">Activity Logs</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="main-content">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // User menu dropdown
            const menus = document.querySelectorAll('[data-user-menu]');
            menus.forEach((menu) => {
                const button = menu.querySelector('.user-avatar');
                const dropdown = menu.querySelector('.dropdown');
                if (!button || !dropdown) return;
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.hidden = !dropdown.hidden;
                });
                document.addEventListener('click', (evt) => {
                    if (!menu.contains(evt.target)) {
                        dropdown.hidden = true;
                    }
                });
            });

            // Sidebar submenu toggle
            const toggleButtons = document.querySelectorAll('.nav-toggle');
            toggleButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-toggle');
                    const submenu = document.getElementById(targetId);
                    if (submenu) {
                        submenu.classList.toggle('open');
                        btn.classList.toggle('expanded');
                    }
                });
            });
        });
    </script>
</body>
</html>
