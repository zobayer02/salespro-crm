<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status') === 'inactive' ? 'lost' : $request->query('status');
        $lostCustomerDays = Customer::lostCustomerDays();

        $customers = Customer::query()
            ->withPurchaseMetrics()
            ->withCount([
                'assignments as active_assignments_count' => fn ($query) => $query->where('status', CustomerAssignment::STATUS_ASSIGNED),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status === 'lost', function ($query) use ($lostCustomerDays): void {
                $query->lost($lostCustomerDays);
            })
            ->when($status === 'active', function ($query) use ($lostCustomerDays): void {
                $query->activeByPurchaseHistory($lostCustomerDays);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $search,
            'status' => $status,
            'lostCustomerDays' => $lostCustomerDays,
            'mode' => 'index',
        ]);
    }

    public function inactive(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $lostCustomerDays = Customer::lostCustomerDays();

        $customers = Customer::query()
            ->withPurchaseMetrics()
            ->withCount([
                'assignments as active_assignments_count' => fn ($query) => $query->where('status', CustomerAssignment::STATUS_ASSIGNED),
            ])
            ->lost($lostCustomerDays)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $search,
            'status' => 'lost',
            'lostCustomerDays' => $lostCustomerDays,
            'mode' => 'lost',
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.create', [
            'customer' => new Customer([
                'status' => Customer::STATUS_ACTIVE,
            ]),
        ]);
    }

    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        Customer::query()->create($request->validated());

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function show(Customer $customer): View
    {
        $customer->loadCount('sales')
            ->loadSum('sales as total_spent', 'total_amount')
            ->loadMax('sales as last_purchase_at', 'sold_at');

        return view('admin.customers.show', [
            'customer' => $customer,
            'sales' => $customer->sales()
                ->withCount('items')
                ->latest('sold_at')
                ->paginate(10),
            'lostCustomerDays' => Customer::lostCustomerDays(),
        ]);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists() || $customer->assignments()->exists()) {
            return redirect()
                ->route('admin.customers.index')
                ->with('success', 'Customer has purchase or assignment history and cannot be deleted.');
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
