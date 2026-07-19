<header class="topbar">
    @php
        $dashboardSearch = (string) request('dashboard_search', '');
        $dashboardRange = (string) request('range', 'last_7_days');
        $salesOverviewRange = (string) request('sales_overview_range', 'last_10_days');
        $branchSalesRange = (string) request('branch_sales_range', 'this_month');
        $topProductsRange = (string) request('top_products_range', 'this_month');
        $owner = auth()->user();
    @endphp

    <div class="top-actions">
        <form class="date-filter" method="GET" action="{{ url()->current() }}" @if (request()->routeIs('admin.dashboard')) data-dashboard-filter @endif>
            <input type="hidden" name="dashboard_search" value="{{ $dashboardSearch }}">
            <input type="hidden" name="sales_overview_range" value="{{ $dashboardRange }}">
            <input type="hidden" name="branch_sales_range" value="{{ $dashboardRange }}">
            <input type="hidden" name="top_products_range" value="{{ $dashboardRange }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M8 2v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
            <select name="range" aria-label="Dashboard date range">
                <option value="today" @selected($dashboardRange === 'today')>Today</option>
                <option value="last_7_days" @selected($dashboardRange === 'last_7_days')>Last 7 Days</option>
                <option value="last_10_days" @selected($dashboardRange === 'last_10_days')>Last 10 Days</option>
                <option value="this_month" @selected($dashboardRange === 'this_month')>This Month</option>
                <option value="this_year" @selected($dashboardRange === 'this_year')>This Year</option>
                <option value="all_time" @selected($dashboardRange === 'all_time')>All Time</option>
            </select>
        </form>

        <div class="profile-menu" data-profile-menu>
            <div class="profile">
                <div class="avatar">
                    @if ($owner->profilePhotoUrl())
                        <img src="{{ $owner->profilePhotoUrl() }}" alt="{{ $owner->name }}">
                    @else
                        {{ strtoupper(substr($owner->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <strong>{{ $owner->name }}</strong>
                    <span>{{ $owner->designation ?: 'Owner Admin' }}</span>
                </div>
            </div>
            <button class="profile-caret" type="button" aria-label="Open profile menu" data-profile-toggle>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="profile-dropdown" data-profile-dropdown>
                <a href="{{ route('admin.profile.edit') }}">
                    @include('admin.partials.nav-icon', ['icon' => 'user'])
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10 17 15 12 10 7"/><path d="M15 12H3"/><path d="M21 3v18"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
