@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Add Branch'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Add Branch</h2>
                <p>Create a new store location.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.branches.store') }}">
                @include('admin.branches._form', ['submitLabel' => 'Create Branch'])
            </form>
        </section>
    </section>
@endsection
