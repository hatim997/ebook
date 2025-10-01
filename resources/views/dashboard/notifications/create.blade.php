@extends('layouts.master')

@section('title', __('Send Notification'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Send Notification') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <!-- Account -->
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('dashboard.notifications.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row p-5">
                        <h3>{{ __('Send New Notification') }}</h3>
                        <div class="mb-4 col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="send_all" name="send_all"
                                    value="1" {{ old('send_all') ? 'checked' : 'checked' }}>
                                <label class="form-check-label" for="send_all">{{ __('Send to All Users') }}</label>
                            </div>
                        </div>

                        <div class="mb-4 col-md-12" id="users_select_box">
                            <label class="form-label" for="user_ids">{{ __('Users') }}</label>
                            <select id="user_ids" name="user_ids[]"
                                class="select2 multiple form-select @error('user_ids') is-invalid @enderror" multiple>
                                {{-- <option value="" disabled>{{ __('Select User') }}</option> --}}
                                @if (isset($users) && count($users) > 0)
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ collect(old('user_ids'))->contains($user->id) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('user_ids')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-12">
                            <label for="title" class="form-label">{{ __('Title') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('title') is-invalid @enderror" type="text" id="title"
                                name="title" required placeholder="{{ __('Enter title') }}" autofocus
                                value="{{ old('title') }}" />
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-12">
                            <label for="message" class="form-label">{{ __('Message') }}</label><span
                                class="text-danger">*</span>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message"
                                placeholder="{{ __('Enter message') }}" required cols="10" rows="5">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-3">{{ __('Send Notification') }}</button>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
@endsection

@section('script')
    <!-- Vendors JS -->
    <script>
        $(document).ready(function() {
            function toggleUserSelect() {
                if ($('#send_all').is(':checked')) {
                    $('#users_select_box').hide();
                    $('#user_ids').val(null).trigger('change'); // clear selection if send_all
                } else {
                    $('#users_select_box').show();
                }
            }

            // Run on page load
            toggleUserSelect();

            // Run on checkbox toggle
            $('#send_all').on('change', function() {
                toggleUserSelect();
            });
        });
    </script>
@endsection
