@extends('layouts.master')

@section('title', __('Purchase Details'))

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.user-purchases.index') }}">{{ __('Purchases') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Details') }}</li>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Purchase Details') }}</h5>
                <div>
                    @if ($userPurchase->payment_status == 'pending')
                        <span class="badge me-4 bg-label-warning">{{ ucfirst($userPurchase->payment_status) }}</span>
                    @elseif ($userPurchase->payment_status == 'paid')
                        <span class="badge me-4 bg-label-success">{{ ucfirst($userPurchase->payment_status) }}</span>
                    @elseif ($userPurchase->payment_status == 'failed')
                        <span class="badge me-4 bg-label-danger">{{ ucfirst($userPurchase->payment_status) }}</span>
                    @else
                        <span class="badge me-4 bg-label-secondary">Unknown</span>
                    @endif
                    <a href="{{ route('dashboard.user-purchases.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-arrow-back"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>
            <div class="card-body">

                {{-- User Details --}}
                <h6 class="text-uppercase text-muted fw-bold border-bottom pb-1 mb-3">{{ __('Basic Details') }}</h6>
                <dl class="row">
                    <dt class="col-sm-3">{{ __('Book Name') }}</dt>
                    <dd class="col-sm-9">{{ $userPurchase->book ? $userPurchase->book->name : 'N/A' }}</dd>

                    <dt class="col-sm-3">{{ __('Total Amount') }}</dt>
                    <dd class="col-sm-9">{{ \App\Helpers\Helper::formatCurrency($userPurchase->amount) }}</dd>

                    <dt class="col-sm-3">{{ __('Payment Type') }}</dt>
                    <dd class="col-sm-9">{{ ucfirst($userPurchase->payment_type) }}</dd>

                    <dt class="col-sm-3">{{ __('User') }}</dt>
                    <dd class="col-sm-9">{{ $userPurchase->user ? $userPurchase->user->name : 'N/A' }}</dd>
                </dl>
                <h6 class="text-uppercase text-muted fw-bold border-bottom pb-1 mb-3">{{ __('Billing Details') }}</h6>
                @if ($userPurchase->billing)
                    <dl class="row">
                        <dt class="col-sm-3">{{ __('Name') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->firstname.' '.$userPurchase->billing->lastname }}</dd>

                        <dt class="col-sm-3">{{ __('Email') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->email ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">{{ __('Phone') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->phone ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">{{ __('Company Name') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->companyname ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">{{ __('City') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->city ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">{{ __('Zip Code') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->zip ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">{{ __('Country') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->country ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">{{ __('Address') }}</dt>
                        <dd class="col-sm-9">{{ $userPurchase->billing->address ?? 'N/A' }}</dd>
                    </dl>
                @else
                    No Billing Details
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
    </script>
@endsection
