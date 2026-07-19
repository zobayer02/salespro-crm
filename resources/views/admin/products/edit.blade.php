@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Edit Product'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Edit Product</h2>
                <p>Update product catalog details and available stock.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.products.update', $product) }}">
                @method('PUT')
                @include('admin.products._form', ['submitLabel' => 'Update Product'])
            </form>
        </section>
    </section>
@endsection
