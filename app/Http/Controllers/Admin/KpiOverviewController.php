<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerAssignment;
use App\Models\Employee;
use Illuminate\View\View;

class KpiOverviewController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.kpi.index', [
            'employees' => Employee::query()
                ->withCount([
                    'assignments as assigned_customers_count',
                    'assignments as active_assignments_count' => fn ($query) => $query->where('status', CustomerAssignment::STATUS_ASSIGNED),
                    'assignments as converted_customers_count' => fn ($query) => $query->where('status', CustomerAssignment::STATUS_CONVERTED),
                ])
                ->orderByDesc('kpi_score')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
