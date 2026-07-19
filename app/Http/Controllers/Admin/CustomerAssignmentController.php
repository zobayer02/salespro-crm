<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerAssignmentStoreRequest;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $assignments = CustomerAssignment::query()
            ->with(['customer', 'employee'])
            ->when(in_array($status, [CustomerAssignment::STATUS_ASSIGNED, CustomerAssignment::STATUS_CONVERTED], true), function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->latest('assigned_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.assignments.index', [
            'assignments' => $assignments,
            'status' => $status,
        ]);
    }

    public function create(Request $request): View
    {
        $lostCustomerDays = Customer::lostCustomerDays();

        return view('admin.assignments.create', [
            'customers' => Customer::query()
                ->withPurchaseMetrics()
                ->lost($lostCustomerDays)
                ->whereDoesntHave('assignments', function ($query): void {
                    $query->where('status', CustomerAssignment::STATUS_ASSIGNED);
                })
                ->orderBy('name')
                ->get(),
            'employees' => Employee::query()
                ->where('status', Employee::STATUS_ACTIVE)
                ->orderBy('name')
                ->get(),
            'selectedCustomerId' => $request->query('customer_id'),
            'lostCustomerDays' => $lostCustomerDays,
        ]);
    }

    public function store(CustomerAssignmentStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $customer = Customer::query()
            ->withPurchaseMetrics()
            ->findOrFail($data['customer_id']);

        if (! $customer->isLostCustomer(Customer::lostCustomerDays())) {
            return back()
                ->withInput()
                ->withErrors(['customer_id' => 'Only inactive customers can be assigned.']);
        }

        $hasActiveAssignment = CustomerAssignment::query()
            ->where('customer_id', $customer->id)
            ->where('status', CustomerAssignment::STATUS_ASSIGNED)
            ->exists();

        if ($hasActiveAssignment) {
            return back()
                ->withInput()
                ->withErrors(['customer_id' => 'This customer already has an active assignment.']);
        }

        CustomerAssignment::query()->create([
            'customer_id' => $customer->id,
            'employee_id' => $data['employee_id'],
            'status' => CustomerAssignment::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        return redirect()
            ->route('admin.assignments.index')
            ->with('success', 'Customer assigned successfully.');
    }
}
