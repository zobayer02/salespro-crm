@php
    $navSections = [
        'Sales & Inventory' => [
            ['Products', 'box', 'admin.products.index', 'admin.products.*'],
            ['Sales', 'cart', 'admin.sales.index', 'admin.sales.*'],
            ['Inventory', 'archive', 'admin.inventory.index', 'admin.inventory.*'],
            ['Branches', 'branch', 'admin.branches.index', 'admin.branches.*'],
            ['Invoices', 'invoice', 'admin.invoices.index', 'admin.invoices.*'],
        ],
        'CRM' => [
            ['Customers', 'users', 'admin.customers.index', ['admin.customers.index', 'admin.customers.create', 'admin.customers.store', 'admin.customers.show', 'admin.customers.edit', 'admin.customers.update', 'admin.customers.destroy']],
            ['Inactive Customers', 'user', 'admin.customers.inactive', 'admin.customers.inactive'],
            ['Assign Customers', 'user', 'admin.assignments.index', 'admin.assignments.*'],
            ['Re-engagements', 'invoice', 'admin.reengagements.index', 'admin.reengagements.*'],
            ['Employees', 'users', 'admin.employees.index', 'admin.employees.*'],
            ['KPI Overview', 'chart', 'admin.kpi.index', 'admin.kpi.*'],
        ],
        'Settings' => [
            ['API Integrations', 'api', 'admin.api-integrations.index', 'admin.api-integrations.*'],
            ['Profile', 'user', 'admin.profile.edit', 'admin.profile.*'],
        ],
    ];
@endphp

<aside class="sidebar">
    <button class="sidebar-toggle" type="button" aria-label="Toggle sidebar" data-sidebar-toggle>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 18 9 12l6-6"/></svg>
    </button>
    <div class="brand">
        <div class="brand-mark">
            <img src="{{ asset('favicon.png') }}" alt="SalesPro">
        </div>
        <div class="brand-name">
            SalesPro
            <span>Inventory & CRM</span>
        </div>
    </div>

    <a @class(['nav-item', 'active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}" title="Dashboard">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>
        Dashboard
    </a>

    @foreach ($navSections as $title => $items)
        <div class="nav-title">{{ $title }}</div>
        @foreach ($items as [$label, $icon, $routeName, $activePattern])
            @php
                $isActive = is_array($activePattern)
                    ? request()->routeIs(...$activePattern)
                    : ($activePattern && request()->routeIs($activePattern));
            @endphp
            <a @class(['nav-item', 'active' => $isActive]) href="{{ $routeName ? route($routeName) : '#' }}" title="{{ $label }}">
                @include('admin.partials.nav-icon', ['icon' => $icon])
                {{ $label }}
            </a>
        @endforeach
    @endforeach

</aside>
