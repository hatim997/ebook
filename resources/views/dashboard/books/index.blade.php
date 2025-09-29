@extends('layouts.master')

@section('title', __('Books'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Books') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Books List Table -->
        <div class="card">
            <div class="card-header">
                @canany(['create book'])
                    <a href="{{ route('dashboard.books.create') }}" class="add-new btn btn-primary waves-effect waves-light">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                            class="d-none d-sm-inline-block">{{ __('Add New Book') }}</span>
                    </a>
                @endcan
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Laws') }}</th>
                            @canany(['delete book','view book law', 'update book'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $index => $book)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $book->bookType ? $book->bookType->name : 'N/A' }}</td>
                                <td>{{ $book->title }}</td>
                                <td>{{ \App\Helpers\Helper::formatCurrency($book->price) }}</td>
                                <td>{{ $book->book_laws_count }}</td>
                                @canany(['delete book','view book law', 'update book'])
                                    <td class="d-flex">
                                        @canany(['delete book'])
                                            <form action="{{ route('dashboard.books.destroy', $book->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="#" type="submit"
                                                    class="btn btn-icon btn-text-danger waves-effect waves-light rounded-pill delete-record delete_confirmation"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete Book') }}">
                                                    <i class="ti ti-trash ti-md"></i>
                                                </a>
                                            </form>
                                        @endcan
                                        @canany(['update book'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.books.edit', $book->id) }}"
                                                    class="btn btn-icon btn-text-success waves-effect waves-light rounded-pill me-1 edit-order-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Book') }}">
                                                    <i class="ti ti-edit ti-md"></i>
                                                </a>
                                            </span>
                                        @endcan

                                        @canany(['view book law'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.books.show', $book->id) }}"
                                                    class="btn btn-icon btn-text-warning waves-effect waves-light rounded-pill me-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View Book Laws') }}">
                                                    <i class="ti ti-scale ti-md"></i>
                                                </a>
                                            </span>
                                        @endcan
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script src="{{asset('assets/js/app-user-list.js')}}"></script> --}}
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection
