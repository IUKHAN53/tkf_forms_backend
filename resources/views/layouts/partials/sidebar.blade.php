<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/clm_logo.svg') }}" alt="CLM Logo" class="sidebar-logo">
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
                    <button type="button" class="nav-link nav-toggle {{ request()->routeIs('admin.child-line-list.*', 'admin.fgds-community.*', 'admin.fgds-health-workers.*', 'admin.bridging-the-gap.*', 'admin.outreach-sites.*', 'admin.vaccination-records.*') ? 'active expanded' : '' }}" data-toggle="core-forms-submenu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <span>Core Forms</span>
                        <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <ul class="nav-submenu {{ request()->routeIs('admin.child-line-list.*', 'admin.fgds-community.*', 'admin.fgds-health-workers.*', 'admin.bridging-the-gap.*', 'admin.outreach-sites.*', 'admin.vaccination-records.*') ? 'open' : '' }}" id="core-forms-submenu">
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
                            <a href="{{ route('admin.vaccination-records.index') }}" class="nav-link {{ request()->routeIs('admin.vaccination-records.*') ? 'active' : '' }}">
                                Vaccination Records
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.outreach-sites.index') }}" class="nav-link {{ request()->routeIs('admin.outreach-sites.*') ? 'active' : '' }}">
                                Vaccination Sites
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        {{-- Reports Section hidden per request (Fixed Site Report menu item).
             Route admin.reports.fixed-site still exists; only the sidebar
             entry is hidden. Restore this block to bring it back.
        <div class="nav-section">
            <div class="nav-section-title">Reports</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('admin.reports.fixed-site') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v18h18"/>
                            <path d="M18 17V9"/>
                            <path d="M13 17V5"/>
                            <path d="M8 17v-3"/>
                        </svg>
                        <span>Fixed Site Report</span>
                    </a>
                </li>
            </ul>
        </div>
        --}}
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
                    <a href="{{ route('admin.community-members.index') }}" class="nav-link {{ request()->routeIs('admin.community-members.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                        <span>Community Members</span>
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
        <!-- Support Section -->
        <div class="nav-section" style="margin-top: auto;">
            <div class="nav-section-title">Support</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('debug.index') }}" class="nav-link {{ request()->routeIs('debug.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        <span>Debug Report</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>
