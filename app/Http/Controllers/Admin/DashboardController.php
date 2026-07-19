<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $range = (string) $request->query('range', 'last_7_days');
        $salesOverviewRange = (string) $request->query('sales_overview_range', 'last_10_days');
        $branchSalesRange = (string) $request->query('branch_sales_range', 'this_month');
        $topProductsRange = (string) $request->query('top_products_range', 'this_month');
        $search = trim((string) $request->query('dashboard_search', ''));
        [$rangeStart, $rangeLabel] = $this->rangeMeta($range);
        $availableRanges = ['today', 'last_7_days', 'last_10_days', 'this_month', 'this_year', 'all_time'];
        $branchSalesRange = in_array($branchSalesRange, $availableRanges, true)
            ? $branchSalesRange
            : 'this_month';
        $topProductsRange = in_array($topProductsRange, $availableRanges, true)
            ? $topProductsRange
            : 'this_month';
        [$branchRangeStart, $branchRangeLabel] = $this->rangeMeta($branchSalesRange);
        [$topProductsRangeStart, $topProductsRangeLabel] = $this->rangeMeta($topProductsRange);
        $salesOverview = $this->salesOverviewChart($salesOverviewRange);
        $lastSevenDays = now()->subDays(7);
        $lostCustomerDays = Customer::lostCustomerDays();
        $totalProducts = Product::query()->count();
        $newProductsThisWeek = Product::query()
            ->where('created_at', '>=', $lastSevenDays)
            ->count();
        $salesQuery = Sale::query()
            ->when($rangeStart, fn ($query) => $query->where('sold_at', '>=', $rangeStart));
        $totalSales = (float) Sale::query()->sum('total_amount');
        $salesThisPeriod = (float) (clone $salesQuery)
            ->sum('total_amount');
        $totalOrders = Sale::query()->count();
        $newOrdersThisPeriod = (clone $salesQuery)
            ->count();
        $topProducts = SaleItem::query()
            ->when($topProductsRangeStart, function ($query) use ($topProductsRangeStart): void {
                $query->whereHas('sale', fn ($query) => $query->where('sold_at', '>=', $topProductsRangeStart));
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('product_name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->selectRaw('product_id, product_name, sku, SUM(quantity) as sold_quantity')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderByDesc('sold_quantity')
            ->take(5)
            ->get()
            ->map(fn (SaleItem $item): array => [
                $item->product_name,
                $item->sku,
                (string) $item->sold_quantity,
                strtoupper(substr($item->product_name, 0, 1)),
            ])
            ->all();
        $recentSales = Sale::query()
            ->with(['customer', 'branch'])
            ->when($rangeStart, fn ($query) => $query->where('sold_at', '>=', $rangeStart))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('branch', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            })
            ->latest('sold_at')
            ->take(5)
            ->get()
            ->map(fn (Sale $sale): array => [
                '#' . $sale->order_number,
                $sale->customer->name,
                $sale->branch?->name ?? 'N/A',
                'Tk ' . number_format((float) $sale->total_amount, 2),
                $sale->sold_at->format('M d, Y'),
                ucfirst($sale->status),
            ])
            ->all();
        $branchSalesQuery = Sale::query()
            ->whereNotNull('branch_id')
            ->when($branchRangeStart, fn ($query) => $query->where('sold_at', '>=', $branchRangeStart));
        $branchSalesTotal = max((float) (clone $branchSalesQuery)->sum('total_amount'), 1);
        $branchColors = ['#4049a3', '#4b85e9', '#c6be58', '#ff7a2f', '#16a34a'];
        $branchSales = Branch::query()
            ->withSum(['sales as sales_total' => function ($query) use ($branchRangeStart): void {
                $query->when($branchRangeStart, fn ($query) => $query->where('sold_at', '>=', $branchRangeStart));
            }], 'total_amount')
            ->orderByDesc('sales_total')
            ->take(5)
            ->get()
            ->map(function (Branch $branch, int $index) use ($branchSalesTotal, $branchColors): array {
                $amount = (float) ($branch->sales_total ?? 0);

                return [
                    'name' => $branch->name,
                    'amount' => $amount,
                    'percent' => round(($amount / $branchSalesTotal) * 100),
                    'slice' => ($amount / $branchSalesTotal) * 100,
                    'color' => $branchColors[$index % count($branchColors)],
                ];
            })
            ->all();
        $totalCustomers = Customer::query()->count();
        $newCustomersThisWeek = Customer::query()
            ->where('created_at', '>=', $lastSevenDays)
            ->count();
        $totalBranches = Branch::query()->count();
        $newBranchesThisWeek = Branch::query()
            ->where('created_at', '>=', $lastSevenDays)
            ->count();
        $inactiveCustomerCount = Customer::query()
            ->lost($lostCustomerDays)
            ->count();
        $inactiveCustomers = Customer::query()
            ->withPurchaseMetrics()
            ->lost($lostCustomerDays)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function (Customer $customer): array {
                $lastPurchaseAt = $customer->lastPurchaseAt();

                return [
                    $customer->name,
                    $lastPurchaseAt ? $lastPurchaseAt->format('M d, Y') : 'Never',
                    $lastPurchaseAt ? ((int) $lastPurchaseAt->diffInDays(now())) . ' days' : 'No purchase',
                    $customer->id,
                    $customer->email,
                ];
            })
            ->all();

        return view('admin.dashboard', [
            'stats' => [
                ['Total Sales', 'Tk ' . number_format($totalSales, 2), 'Tk ' . number_format($salesThisPeriod, 2) . " {$rangeLabel}", 'bag', 'purple', false],
                ['Total Orders', (string) $totalOrders, "{$newOrdersThisPeriod} orders {$rangeLabel}", 'cart', 'green', false],
                ['Total Products', (string) $totalProducts, "+{$newProductsThisWeek} new this week", 'box', 'orange', false],
                ['Total Customers', (string) $totalCustomers, "+{$newCustomersThisWeek} new this week", 'users', 'blue', false],
                ['Inactive Customers', (string) $inactiveCustomerCount, "{$inactiveCustomerCount} need follow-up", 'user', 'red', true],
                ['Total Branches', (string) $totalBranches, "+{$newBranchesThisWeek} new this week", 'branch', 'purple', false],
            ],
            'search' => $search,
            'salesOverview' => $salesOverview,
            'branchSalesFilter' => [
                'selected' => $branchSalesRange,
                'label' => $branchRangeLabel,
            ],
            'topProductsFilter' => [
                'selected' => $topProductsRange,
                'label' => $topProductsRangeLabel,
            ],
            'products' => $topProducts,
            'branchSales' => $branchSales,
            'sales' => $recentSales,
            'inactiveCustomers' => $inactiveCustomers,
        ]);
    }

    private function rangeMeta(string $range): array
    {
        return match ($range) {
            'today' => [now()->startOfDay(), 'today'],
            'last_10_days' => [now()->subDays(10), 'last 10 days'],
            'this_month' => [now()->startOfMonth(), 'this month'],
            'this_year' => [now()->startOfYear(), 'this year'],
            'all_time' => [null, 'all time'],
            default => [now()->subDays(7), 'last 7 days'],
        };
    }

    private function salesOverviewChart(string $range): array
    {
        [$start, $end, $label] = match ($range) {
            'today' => [now()->startOfDay(), now()->endOfDay(), 'Today'],
            'last_7_days' => [now()->subDays(6)->startOfDay(), now()->endOfDay(), 'Last 7 Days'],
            'last_10_days' => [now()->subDays(9)->startOfDay(), now()->endOfDay(), 'Last 10 Days'],
            'this_month' => [now()->startOfMonth()->startOfDay(), now()->endOfDay(), 'This Month'],
            'this_year' => [now()->startOfYear()->startOfDay(), now()->endOfDay(), 'This Year'],
            'all_time' => [
                optional(Sale::query()->oldest('sold_at')->first())->sold_at?->copy()->startOfDay() ?? now()->startOfDay(),
                now()->endOfDay(),
                'All Time',
            ],
            default => [now()->subDays(6)->startOfDay(), now()->endOfDay(), 'Last 7 Days'],
        };

        $selectedRange = in_array($range, ['today', 'last_7_days', 'last_10_days', 'this_month', 'this_year', 'all_time'], true)
            ? $range
            : 'last_7_days';

        $totals = Sale::query()
            ->whereBetween('sold_at', [$start, $end])
            ->get(['total_amount', 'sold_at'])
            ->groupBy(fn (Sale $sale): string => $sale->sold_at->format('Y-m-d'))
            ->map(fn ($sales): float => (float) $sales->sum('total_amount'));

        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $dates[] = [
                'date' => $date->copy(),
                'value' => (float) ($totals[$key] ?? 0),
            ];
        }

        $maxValue = max(array_column($dates, 'value') ?: [0]);
        $yMax = $this->niceChartMax($maxValue);
        $plot = [
            'left' => 82,
            'right' => 665,
            'top' => 18,
            'bottom' => 220,
        ];
        $height = $plot['bottom'] - $plot['top'];
        $count = max(count($dates), 1);

        $points = collect($dates)
            ->values()
            ->map(function (array $item, int $index) use ($plot, $height, $count, $yMax): array {
                $x = $count === 1
                    ? ($plot['left'] + $plot['right']) / 2
                    : $plot['left'] + (($plot['right'] - $plot['left']) / ($count - 1)) * $index;
                $y = $plot['bottom'] - (($item['value'] / $yMax) * $height);

                return [
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                    'value' => $item['value'],
                    'label' => $item['date']->format('M j'),
                ];
            })
            ->all();

        $linePath = $this->smoothChartPath($points);

        $areaPath = $linePath
            ? "{$linePath} L{$plot['right']} {$plot['bottom']} L{$plot['left']} {$plot['bottom']} Z"
            : '';

        $ticks = collect(range(4, 0))
            ->map(function (int $step) use ($plot, $height, $yMax): array {
                $value = ($yMax / 4) * $step;

                return [
                    'value' => $value,
                    'label' => $this->formatChartValue($value),
                    'y' => round($plot['bottom'] - (($value / $yMax) * $height), 2),
                ];
            })
            ->all();

        $labelStep = max(1, (int) ceil($count / 10));
        $labels = collect($points)
            ->filter(fn (array $point, int $index): bool => $index % $labelStep === 0 || $index === $count - 1)
            ->values()
            ->all();

        return [
            'selected' => $selectedRange,
            'label' => $label,
            'ticks' => $ticks,
            'points' => $points,
            'labels' => $labels,
            'plot' => $plot,
            'linePath' => $linePath,
            'areaPath' => $areaPath,
        ];
    }

    private function smoothChartPath(array $points): string
    {
        if (count($points) === 0) {
            return '';
        }

        if (count($points) === 1) {
            return "M{$points[0]['x']} {$points[0]['y']}";
        }

        $path = "M{$points[0]['x']} {$points[0]['y']}";
        $lastIndex = count($points) - 1;

        for ($index = 0; $index < $lastIndex; $index++) {
            $previous = $points[max(0, $index - 1)];
            $current = $points[$index];
            $next = $points[$index + 1];
            $afterNext = $points[min($lastIndex, $index + 2)];

            $controlOneX = round($current['x'] + (($next['x'] - $previous['x']) / 6), 2);
            $controlOneY = round($current['y'] + (($next['y'] - $previous['y']) / 6), 2);
            $controlTwoX = round($next['x'] - (($afterNext['x'] - $current['x']) / 6), 2);
            $controlTwoY = round($next['y'] - (($afterNext['y'] - $current['y']) / 6), 2);

            $path .= " C{$controlOneX} {$controlOneY}, {$controlTwoX} {$controlTwoY}, {$next['x']} {$next['y']}";
        }

        return $path;
    }

    private function niceChartMax(float $value): float
    {
        if ($value <= 0) {
            return 1000;
        }

        $magnitude = 10 ** floor(log10($value));
        $normalized = $value / $magnitude;
        $nice = match (true) {
            $normalized <= 1 => 1,
            $normalized <= 2 => 2,
            $normalized <= 5 => 5,
            default => 10,
        };

        return $nice * $magnitude;
    }

    private function formatChartValue(float $value): string
    {
        if ($value >= 1000000) {
            return rtrim(rtrim(number_format($value / 1000000, 1), '0'), '.') . 'm';
        }

        if ($value >= 1000) {
            return rtrim(rtrim(number_format($value / 1000, 1), '0'), '.') . 'k';
        }

        return (string) (int) $value;
    }

}
