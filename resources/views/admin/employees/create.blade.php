@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Create Employee'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Create Employee</h2>
                <p>Add an employee for customer follow-up and KPI tracking.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.employees.store') }}">
                @include('admin.employees._form', ['submitLabel' => 'Create Employee'])
            </form>
        </section>
    </section>
@endsection
