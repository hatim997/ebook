@extends('layouts.master')

@section('title', __('Purchases'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Purchases') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Purchases List Table -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Order No#') }}</th>
                            <th>{{ __('User Name') }}</th>
                            <th>{{ __('Book') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Payment Type') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            @canany(['view user purchases'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userPurchases as $index => $purchase)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><a href="{{ route('dashboard.user-purchases.show', $purchase->id) }}">{{ $purchase->order_no }}</a></td>
                                <td>{{ $purchase->billing ? $purchase->billing->firstname.' '.$purchase->billing->lastname : 'N/A' }}</td>
                                <td>{{ $purchase->book ? $purchase->book->name : 'N/A' }}</td>
                                <td>{{ \App\Helpers\Helper::formatCurrency($purchase->amount) }}</td>
                                <td>{{ ucwords($purchase->payment_type) }}</td>
                                <td>
                                    @if ($purchase->payment_status == 'pending')
                                        <span class="badge me-4 bg-label-warning">{{ ucfirst($purchase->payment_status) }}</span>
                                    @elseif ($purchase->payment_status == 'paid')
                                        <span class="badge me-4 bg-label-success">{{ ucfirst($purchase->payment_status) }}</span>
                                    @elseif ($purchase->payment_status == 'failed')
                                        <span class="badge me-4 bg-label-danger">{{ ucfirst($purchase->payment_status) }}</span>
                                    @else
                                        <span class="badge me-4 bg-label-secondary">Unknown</span>
                                    @endif
                                </td>
                                @canany(['view user purchases'])
                                    <td class="d-flex">
                                        @canany(['view user purchases'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.user-purchases.show', $purchase->id) }}"
                                                    class="btn btn-icon btn-text-warning waves-effect waves-light rounded-pill me-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View Purchase Details') }}">
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
