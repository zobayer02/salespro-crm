@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Create Customer'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Create Customer</h2>
                <p>Add customer details for future sales and CRM tracking.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.customers.store') }}">
                @include('admin.customers._form', ['submitLabel' => 'Create Customer'])
            </form>
        </section>
    </section>
@endsection
