@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Dashboard'])

@section('content')
    <section class="content">
        <div class="dashboard-heading">
            <div>
                <h2>Dashboard</h2>
                <p>Monitor sales, inventory, CRM follow-up and branch performance.</p>
            </div>
            <div class="dashboard-actions">
                <a class="primary-button" href="{{ route('admin.sales.create') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    New Sale
                </a>
            </div>
        </div>

        <div class="cards">
            @foreach ($stats as [$label, $value, $change, $icon, $tone, $warning])
                @php
                    $cardUrl = match ($label) {
                        'Total Sales', 'Total Orders' => route('admin.sales.index'),
                        'Total Products' => route('admin.products.index'),
                        'Total Customers' => route('admin.customers.index'),
                        'Inactive Customers' => route('admin.customers.inactive'),
                        'Total Branches' => route('admin.branches.index'),
                        default => null,
                    };
                    $cardTag = $cardUrl ? 'a' : 'article';
                @endphp

                <{{ $cardTag }} class="card stat-card {{ $tone }}-metric" @if ($cardUrl) href="{{ $cardUrl }}" @endif>
                    <div class="stat-icon {{ $tone }}">
                        @include('admin.partials.nav-icon', ['icon' => $icon])
                    </div>
                    <div class="stat-body">
                        <span>{{ $label }}</span>
                        <strong>{{ $value }}</strong>
                        <small @class(['warning' => $warning])>{{ $change }}</small>
                    </div>
                </{{ $cardTag }}>
            @endforeach
        </div>

        <div class="analytics-grid">
            <section class="panel sales-overview-panel">
                <div class="panel-header">
                    <h2>Sales Overview</h2>
                    <form class="panel-filter-form" method="GET" action="{{ route('admin.dashboard') }}" data-dashboard-filter data-dashboard-target=".sales-overview-panel">
                        <input type="hidden" name="range" value="{{ request('range', 'last_7_days') }}">
                        <input type="hidden" name="dashboard_search" value="{{ request('dashboard_search') }}">
                        <input type="hidden" name="branch_sales_range" value="{{ request('branch_sales_range', 'this_month') }}">
                        <input type="hidden" name="top_products_range" value="{{ request('top_products_range', 'this_month') }}">
                        <select class="filter-select" name="sales_overview_range" aria-label="Sales overview range">
                            <option value="today" @selected($salesOverview['selected'] === 'today')>Today</option>
                            <option value="last_7_days" @selected($salesOverview['selected'] === 'last_7_days')>Last 7 Days</option>
                            <option value="last_10_days" @selected($salesOverview['selected'] === 'last_10_days')>Last 10 Days</option>
                            <option value="this_month" @selected($salesOverview['selected'] === 'this_month')>This Month</option>
                            <option value="this_year" @selected($salesOverview['selected'] === 'this_year')>This Year</option>
                            <option value="all_time" @selected($salesOverview['selected'] === 'all_time')>All Time</option>
                        </select>
                    </form>
                </div>
                <div class="chart sales-chart" data-sales-chart>
                    <svg viewBox="0 0 700 285" preserveAspectRatio="xMidYMid meet">
                        <defs>
                            <linearGradient id="areaFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#4f46e5" stop-opacity=".24"/>
                                <stop offset="100%" stop-color="#4f46e5" stop-opacity=".03"/>
                            </linearGradient>
                        </defs>
                        @foreach ($salesOverview['ticks'] as $tick)
                            <line x1="{{ $salesOverview['plot']['left'] }}" y1="{{ $tick['y'] }}" x2="{{ $salesOverview['plot']['right'] }}" y2="{{ $tick['y'] }}" stroke="#dfe7f2" @if ($tick['value'] > 0) stroke-dasharray="5 5" @endif/>
                            <text x="14" y="{{ $tick['y'] + 4 }}" class="axis-label">{{ $tick['label'] }}</text>
                        @endforeach
                        <path d="{{ $salesOverview['areaPath'] }}" fill="url(#areaFill)"/>
                        <path class="sales-chart-line" d="{{ $salesOverview['linePath'] }}" fill="none" stroke="#4f46e5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path
                            class="sales-chart-line-hit"
                            d="{{ $salesOverview['linePath'] }}"
                            data-sales-points='@json($salesOverview['points'])'
                        />
                        <g fill="#4f46e5" stroke="#fff" stroke-width="3">
                            @foreach ($salesOverview['points'] as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5"/>
                            @endforeach
                        </g>
                        @foreach ($salesOverview['labels'] as $point)
                            <text x="{{ $point['x'] }}" y="264" class="axis-label axis-label-x">{{ $point['label'] }}</text>
                        @endforeach
                    </svg>
                    <div class="sales-chart-tooltip" data-sales-tooltip>
                        <strong></strong>
                        <span></span>
                    </div>
                </div>
            </section>

            <section class="panel branch-sales-panel">
                <div class="panel-header">
                    <h2>Sales by Branch</h2>
                    <form class="panel-filter-form" method="GET" action="{{ route('admin.dashboard') }}" data-dashboard-filter data-dashboard-target=".branch-sales-panel">
                        <input type="hidden" name="range" value="{{ request('range', 'last_7_days') }}">
                        <input type="hidden" name="dashboard_search" value="{{ request('dashboard_search') }}">
                        <input type="hidden" name="sales_overview_range" value="{{ request('sales_overview_range', 'last_10_days') }}">
                        <input type="hidden" name="top_products_range" value="{{ request('top_products_range', 'this_month') }}">
                        <select class="filter-select" name="branch_sales_range" aria-label="Sales by branch range">
                            <option value="today" @selected($branchSalesFilter['selected'] === 'today')>Today</option>
                            <option value="this_month" @selected($branchSalesFilter['selected'] === 'this_month')>This Month</option>
                            <option value="last_7_days" @selected($branchSalesFilter['selected'] === 'last_7_days')>Last 7 Days</option>
                            <option value="last_10_days" @selected($branchSalesFilter['selected'] === 'last_10_days')>Last 10 Days</option>
                            <option value="this_year" @selected($branchSalesFilter['selected'] === 'this_year')>This Year</option>
                            <option value="all_time" @selected($branchSalesFilter['selected'] === 'all_time')>All Time</option>
                        </select>
                    </form>
                </div>
                    <div class="branch-layout">
                        @php
                            $donutCircumference = 263.89;
                            $donutOffset = 0;
                            $branchSlices = collect($branchSales)
                                ->filter(fn ($branch) => $branch['slice'] > 0)
                                ->values()
                                ->map(function ($branch) use (&$donutOffset, $donutCircumference) {
                                    $branch['dash'] = ($branch['slice'] / 100) * $donutCircumference;
                                    $branch['gap'] = $donutCircumference - $branch['dash'];
                                    $branch['dash_offset'] = -$donutOffset;
                                    $donutOffset += $branch['dash'];

                                    return $branch;
                                });
                            $topSlice = $branchSlices->first();
                        @endphp
                        <div class="branch-donut" data-branch-donut>
                            <svg viewBox="0 0 120 120" aria-label="Sales by branch chart">
                                <circle class="donut-track" cx="60" cy="60" r="42"></circle>
                                @foreach ($branchSlices as $branch)
                                    <circle
                                        class="donut-slice"
                                        cx="60"
                                        cy="60"
                                        r="42"
                                        stroke="{{ $branch['color'] }}"
                                        stroke-dasharray="{{ $branch['dash'] }} {{ $branch['gap'] }}"
                                        stroke-dashoffset="{{ $branch['dash_offset'] }}"
                                        data-branch-name="{{ $branch['name'] }}"
                                        data-branch-percent="{{ $branch['percent'] }}%"
                                        data-branch-amount="Tk {{ number_format($branch['amount'], 2) }}"
                                    ></circle>
                                @endforeach
                            </svg>
                            <div class="branch-donut-hole"></div>
                            <div class="branch-tooltip" data-branch-tooltip>
                                <strong></strong>
                                <span></span>
                            </div>
                        </div>
                    <div class="legend">
                        @forelse ($branchSales as $branch)
                            <div class="legend-item"><span class="dot" style="background:{{ $branch['color'] }}"></span><div><strong>{{ $branch['name'] }}</strong>{{ $branch['percent'] }}% (Tk {{ number_format($branch['amount'], 2) }})</div></div>
                        @empty
                            <div class="empty-state">No branch sales yet.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="panel top-products-panel">
                <div class="panel-header">
                    <h2>Top Selling Products</h2>
                    <form class="panel-filter-form" method="GET" action="{{ route('admin.dashboard') }}" data-dashboard-filter data-dashboard-target=".top-products-panel">
                        <input type="hidden" name="range" value="{{ request('range', 'last_7_days') }}">
                        <input type="hidden" name="dashboard_search" value="{{ request('dashboard_search') }}">
                        <input type="hidden" name="sales_overview_range" value="{{ request('sales_overview_range', 'last_10_days') }}">
                        <input type="hidden" name="branch_sales_range" value="{{ request('branch_sales_range', 'this_month') }}">
                        <select class="filter-select" name="top_products_range" aria-label="Top selling products range">
                            <option value="today" @selected($topProductsFilter['selected'] === 'today')>Today</option>
                            <option value="this_month" @selected($topProductsFilter['selected'] === 'this_month')>This Month</option>
                            <option value="last_7_days" @selected($topProductsFilter['selected'] === 'last_7_days')>Last 7 Days</option>
                            <option value="last_10_days" @selected($topProductsFilter['selected'] === 'last_10_days')>Last 10 Days</option>
                            <option value="this_year" @selected($topProductsFilter['selected'] === 'this_year')>This Year</option>
                            <option value="all_time" @selected($topProductsFilter['selected'] === 'all_time')>All Time</option>
                        </select>
                    </form>
                </div>
                <div class="product-list">
                    @forelse ($products as [$name, $sku, $count, $letter])
                        <div class="product-row">
                            <div class="rank">{{ $loop->iteration }}</div>
                            <div class="product-img">{{ $letter }}</div>
                            <div class="product-info">
                                <strong>{{ $name }}</strong>
                                <span>SKU: {{ $sku }}</span>
                            </div>
                            <div class="sold">
                                <strong>{{ $count }}</strong>
                                Sold
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No selling products yet.</div>
                    @endforelse
                </div>
            </section>

            <section class="panel inactive-customers-panel">
                <div class="panel-header">
                    <h2>Inactive Customers <span style="font-size:13px;font-weight:700;color:#475569">(Need Attention)</span></h2>
                    <a class="filter-button" href="{{ route('admin.customers.inactive') }}">View All</a>
                </div>
                <div class="dashboard-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Last Purchase</th>
                                <th>Days Inactive</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inactiveCustomers as [$customer, $lastPurchase, $days, $customerId, $customerEmail])
                                <tr>
                                    <td>{{ $customer }}</td>
                                    <td>{{ $lastPurchase }}</td>
                                    <td>{{ $days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">No inactive customers found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <script>
            (() => {
                const dashboardUrl = @json(route('admin.dashboard'));

                window.initDashboardInteractions = () => {
                    window.enhanceSalesProFilters?.();

                    document.querySelectorAll('[data-sales-chart]:not([data-bound])').forEach((chart) => {
                        chart.dataset.bound = 'true';
                        const tooltip = chart.querySelector('[data-sales-tooltip]');
                        const title = tooltip.querySelector('strong');
                        const meta = tooltip.querySelector('span');

                        chart.querySelectorAll('.sales-chart-line-hit').forEach((line) => {
                            const points = JSON.parse(line.dataset.salesPoints || '[]');

                            const showTooltip = (event) => {
                                const svg = line.ownerSVGElement;
                                const point = svg.createSVGPoint();
                                point.x = event.clientX;
                                point.y = event.clientY;
                                const cursor = point.matrixTransform(svg.getScreenCTM().inverse());
                                const nearest = points.reduce((selected, item) => {
                                    return Math.abs(item.x - cursor.x) < Math.abs(selected.x - cursor.x) ? item : selected;
                                }, points[0]);

                                if (!nearest) {
                                    return;
                                }

                                chart.classList.add('is-line-hovering');
                                title.textContent = nearest.label;
                                meta.textContent = `Tk ${Number(nearest.value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                tooltip.style.left = `${(Number(nearest.x) / 700) * 100}%`;
                                tooltip.style.top = `${(Number(nearest.y) / 285) * 100}%`;
                                tooltip.classList.add('active');
                            };
                            const hideTooltip = () => {
                                chart.classList.remove('is-line-hovering');
                                tooltip.classList.remove('active');
                            };

                            line.addEventListener('mousemove', showTooltip);
                            line.addEventListener('mouseleave', hideTooltip);
                        });
                    });

                    document.querySelectorAll('[data-branch-donut]:not([data-bound])').forEach((chart) => {
                        chart.dataset.bound = 'true';
                        const tooltip = chart.querySelector('[data-branch-tooltip]');
                        const title = tooltip.querySelector('strong');
                        const meta = tooltip.querySelector('span');
                        const slices = chart.querySelectorAll('.donut-slice');

                        slices.forEach((slice) => {
                            const showTooltip = () => {
                                chart.classList.add('is-filtering');
                                slices.forEach((item) => {
                                    item.classList.toggle('is-hovered', item.dataset.branchName === slice.dataset.branchName);
                                });

                                title.textContent = slice.dataset.branchName;
                                meta.textContent = `${slice.dataset.branchPercent} (${slice.dataset.branchAmount})`;
                                tooltip.classList.add('active');
                            };
                            const hideTooltip = () => {
                                chart.classList.remove('is-filtering');
                                slices.forEach((item) => item.classList.remove('is-hovered'));
                                tooltip.classList.remove('active');
                            };

                            slice.addEventListener('mouseenter', showTooltip);
                            slice.addEventListener('mouseleave', hideTooltip);
                        });
                    });

                    document.querySelectorAll('[data-compose-email]:not([data-bound])').forEach((button) => {
                        button.dataset.bound = 'true';
                        button.addEventListener('click', () => {
                            window.openSalesProCompose?.(button);
                        });
                    });
                };

                window.refreshDashboard = async (form) => {
                    const url = new URL(window.location.href);
                    url.pathname = new URL(form.action || dashboardUrl, window.location.origin).pathname;
                    new FormData(form).forEach((value, key) => {
                        if (value !== '') {
                            url.searchParams.set(key, value);
                        } else {
                            url.searchParams.delete(key);
                        }
                    });

                    const main = document.querySelector('.main');
                    main?.setAttribute('aria-busy', 'true');

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                    });

                    if (!response.ok) {
                        form.submit();
                        return;
                    }

                    const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                    const targetSelector = form.dataset.dashboardTarget;

                    if (targetSelector) {
                        const currentTarget = document.querySelector(targetSelector);
                        const nextTarget = page.querySelector(targetSelector);

                        if (!currentTarget || !nextTarget) {
                            form.submit();
                            return;
                        }

                        currentTarget.replaceWith(nextTarget);
                        window.history.replaceState({}, '', url);
                        window.initDashboardInteractions();
                        document.querySelector('.main')?.removeAttribute('aria-busy');
                        return;
                    }

                    const nextTopbar = page.querySelector('.topbar');
                    const nextContent = page.querySelector('.content');
                    const currentTopbar = document.querySelector('.topbar');
                    const currentContent = document.querySelector('.content');

                    if (!nextTopbar || !nextContent || !currentTopbar || !currentContent) {
                        form.submit();
                        return;
                    }

                    currentTopbar.replaceWith(nextTopbar);
                    currentContent.replaceWith(nextContent);
                    window.history.replaceState({}, '', url);
                    window.initDashboardInteractions();
                    document.querySelector('.main')?.removeAttribute('aria-busy');
                };

                if (!window.dashboardFiltersBound) {
                    window.dashboardFiltersBound = true;

                    document.addEventListener('click', (event) => {
                        if (!event.target.closest('.custom-filter') && !event.target.closest('.custom-filter-menu')) {
                            window.closeSalesProFilters?.();
                        }
                    });

                    document.addEventListener('change', (event) => {
                        const select = event.target.closest('[data-dashboard-filter] select');

                        if (!select) {
                            return;
                        }

                        event.preventDefault();
                        window.refreshDashboard(select.form).catch(() => select.form.submit());
                    });

                    document.addEventListener('submit', (event) => {
                        const form = event.target.closest('[data-dashboard-filter]');

                        if (!form) {
                            return;
                        }

                        event.preventDefault();
                        window.refreshDashboard(form).catch(() => form.submit());
                    });
                }

                window.initDashboardInteractions();
            })();
        </script>

        <div class="bottom-grid">
            <section class="panel">
                <div class="panel-header">
                    <h2>Recent Sales</h2>
                    <a class="filter-button" href="{{ route('admin.sales.index') }}">View All</a>
                </div>
                <div class="dashboard-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Total Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as [$orderId, $customer, $branch, $amount, $date, $status])
                                <tr>
                                    <td>{{ $orderId }}</td>
                                    <td>{{ $customer }}</td>
                                    <td>{{ $branch }}</td>
                                    <td>{{ $amount }}</td>
                                    <td>{{ $date }}</td>
                                    <td><span @class(['status', 'pending' => $status === 'Pending'])>{{ $status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">No recent sales yet.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>

    @include('admin.reengagements._compose_modal')
@endsection
