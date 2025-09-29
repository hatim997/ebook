@extends('layouts.master')

@section('title', __('Book Laws'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.books.index') }}">{{ __('Books') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Book Laws') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Books List Table -->
        <div class="card">
            <div class="card-header">
                @canany(['create book'])
                    <a href="{{ route('dashboard.book-laws.create', $book->id) }}" class="add-new btn btn-primary waves-effect waves-light">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                            class="d-none d-sm-inline-block">{{ __('Add New Book Law') }}</span>
                    </a>
                @endcan
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Content') }}</th>
                            @canany(['delete book law', 'update book law'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookLaws as $index => $bookLaw)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bookLaw->title }}</td>
                                <td>{!! Str::limit($bookLaw->content, 15, '...') !!}</td>
                                @canany(['delete book law', 'update book law'])
                                    <td class="d-flex">
                                        @canany(['delete book law'])
                                            <form action="{{ route('dashboard.book-laws.destroy', $bookLaw->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="#" type="submit"
                                                    class="btn btn-icon btn-text-danger waves-effect waves-light rounded-pill delete-record delete_confirmation"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete Book Law') }}">
                                                    <i class="ti ti-trash ti-md"></i>
                                                </a>
                                            </form>
                                        @endcan
                                        @canany(['update book law'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.book-laws.edit', $bookLaw->id) }}"
                                                    class="btn btn-icon btn-text-success waves-effect waves-light rounded-pill me-1 edit-order-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Book Law') }}">
                                                    <i class="ti ti-edit ti-md"></i>
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
