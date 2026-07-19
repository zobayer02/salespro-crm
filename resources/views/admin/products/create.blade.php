@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Create Product'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Create Product</h2>
                <p>Add a catalog item with SKU, price and opening stock.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.products.store') }}">
                @include('admin.products._form', ['submitLabel' => 'Create Product'])
            </form>
        </section>
    </section>
@endsection
