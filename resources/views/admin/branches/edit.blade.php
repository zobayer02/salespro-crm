@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Edit Branch'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Edit Branch</h2>
                <p>Update store location information.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.branches.update', $branch) }}">
                @method('PUT')
                @include('admin.branches._form', ['submitLabel' => 'Update Branch'])
            </form>
        </section>
    </section>
@endsection
