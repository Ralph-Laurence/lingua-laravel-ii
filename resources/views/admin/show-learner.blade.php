@php $includeFooter = false; @endphp
@extends('shared.base-admin')
@section('content')
{{-- @dd($learnerDetails) --}}
@push('dialogs')
    @include('partials.confirmbox')
    <x-toast-container>
        @if (session('alert_error'))
            @include('partials.toast', [
                'toastMessage'  => session('alert_error'),
                'toastTitle'    => 'Alert',
                'autoClose'     => 'true'
            ])
        @endif
    </x-toast-container>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tutor-details.css') }}">
@endpush

    <section class="px-2 pt-2 pb-3 mx-auto w-50">
        <div class="flex-start gap-2">
            <a role="button" href="{{ route('admin.learners-index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Back
            </a>
            <h6 class="mb-0">Learners / Details <span class="text-secondary">/ {{ $learnerDetails['fullname'] }}</span></h6>
        </div>
    </section>

    <section class="px-2 mt-3 mx-auto w-50">
        <div class="card">
            <div class="card-body d-flex">
                <div class="profile-photo-wrapper">
                    <img src="{{ $learnerDetails['photo'] }}" alt="profile-photo" height="160">
                </div>
                <div class="profile-captions ps-4">
                    <div class="tutor-name flex-start gap-2 mb-2">
                        <h4 class="mb-0 darker-text">{{ $learnerDetails['fullname'] }}</h4>
                    </div>
                    @if (!empty($learnerDetails['disabilityBadge']))
                    <div class="tutor-badges flex-start mb-2">
                        <span data-bs-toggle="tooltip" title="{{ $learnerDetails['disabilityDesc'] }}" class="badge awareness_badge disability-tooltip {{ $learnerDetails['disabilityBadge'] }}">{{  $learnerDetails['disability'] }}</span>
                    </div>
                    <hr class="border-light border-1">
                    @endif
                    <div class="tutor-address text-secondary mb-2">
                        <i class="fas fa-location-dot me-2"></i>
                        {{ $learnerDetails['address'] }}
                    </div>
                    <div class="tutor-email text-secondary mb-2">
                        <i class="fas fa-at me-2"></i>
                        {{ $learnerDetails['email'] }}
                    </div>
                    <div class="tutor-contact text-secondary mb-3">
                        <i class="fas fa-phone me-2"></i>
                        {{ $learnerDetails['contact'] }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="hr my-4 w-50 mx-auto">

    <section class="mx-auto w-50 section-danger-zone p-3 rounded mb-4">
        <h6 class="text-danger text-14 poppins-semibold mx-3 mb-1">
            <i class="fas fa-warning me-2"></i>Danger Zone
        </h6>
        <small class="text-danger text-12 mx-3">Warning! This section allows for potentially destructive actions. Please proceed with caution.</small>
        <div class="row mx-auto pt-4">
            <div class="col-9">
                <p class="text-14 mb-0">Terminate this account</p>
                <small class="text-muted text-13">Termination is irreversible and the account cannot be reinstated.</small>
            </div>
            <div class="col-3 text-end">
                <x-sl-button type="button" id="btn-terminate" style="danger" text="Terminate" data-learner-name="{{ $learnerDetails['fullname'] }}"/>
            </div>
        </div>
        <form action="{{ route('admin.terminate-learner') }}" method="POST" class="d-none" id="terminateLearnerForm">
            @csrf
            <input type="hidden" name="userid" value="{{ $hashedId }}">
        </form>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin/show-learner.js') }}"></script>
    <script src="{{ asset('assets/lib/dompurify/purify.min.js') }}"></script>
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
@endpush
