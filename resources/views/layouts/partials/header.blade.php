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
