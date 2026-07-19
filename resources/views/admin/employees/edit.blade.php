@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Edit Employee'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Edit Employee</h2>
                <p>Update employee details, status and KPI score.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
                @method('PUT')
                @include('admin.employees._form', ['submitLabel' => 'Update Employee'])
            </form>
        </section>
    </section>
@endsection
