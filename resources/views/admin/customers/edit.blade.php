@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Edit Customer'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Edit Customer</h2>
                <p>Update customer contact and CRM status.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                @method('PUT')
                @include('admin.customers._form', ['submitLabel' => 'Update Customer'])
            </form>
        </section>
    </section>
@endsection
