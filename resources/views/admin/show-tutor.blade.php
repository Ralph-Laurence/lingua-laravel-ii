@extends('shared.base-admin')
@section('content')
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
            <a role="button" href="{{ route('admin.tutors-index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Back
            </a>
            <h6 class="mb-0">Tutors / Details <span class="text-secondary">/ {{ $tutorDetails['fullname'] }}</span></h6>
        </div>
    </section>

    <section class="px-2 pb-3 mx-auto w-50">
        <div class="profile-details d-flex">
            <div class="profile-photo-wrapper">
                <img src="{{ $tutorDetails['photo'] }}" alt="profile-photo" height="160">
            </div>
            <div class="profile-captions ps-4">
                <div class="tutor-name flex-start gap-2 mb-3">
                    <h2 class="mb-0 darker-text">{{ $tutorDetails['fullname'] }}</h2>
                </div>
                <div class="tutor-badges">
                    @if (!empty($tutorDetails['disabilityBadge']))
                        <div class="tutor-badges flex-start mb-2">
                            <span data-bs-toggle="tooltip" title="{{ $tutorDetails['disabilityDesc'] }}" class="badge awareness_badge disability-tooltip {{ $tutorDetails['disabilityBadge'] }}">{{ $tutorDetails['disability'] }}</span>
                        </div>
                        <hr class="border border-1" />
                    @endif
                </div>
                <h6 class="tutor-bio darker-text text-14 mb-3">
                    @foreach (explode("\n", $tutorDetails['bio']) as $line)
                        {{ $line }}<br>
                    @endforeach
                </h6>
                <div class="tutor-address text-secondary mb-2">
                    <i class="fas fa-location-dot me-2"></i>
                    {{ $tutorDetails['address'] }}
                </div>
                <div class="tutor-email text-secondary mb-2">
                    <i class="fas fa-at me-2"></i>
                    {{ $tutorDetails['email'] }}
                </div>
                <div class="tutor-contact text-secondary mb-3">
                    <i class="fas fa-phone me-2"></i>
                    {{ $tutorDetails['contact'] }}
                </div>
            </div>
        </div>
    </section>

    <section class="tutor-story px-2 mx-auto w-50 mb-4">
        <h5 class="title-about-me darker-text">About Me</h5>
        <div class="msw-justify" id="doc-content-about">
            {{-- @foreach (explode("\n", $tutorDetails['about']) as $line)
                {{ $line }}<br>
            @endforeach --}}
            {!! $tutorDetails['about'] !!}
        </div>
    </section>

    <section class="tutor-resume px-2 mx-auto w-50 mb-5">
        <h5 class="title-about-me darker-text">Resume</h5>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#home">Education</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu1">Work Experience</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu2">Certifications</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu3">My Skills</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div id="home" class="container tab-pane active"><br>

                @if (!empty($tutorDetails['education']))
                    @foreach ($tutorDetails['education'] as $obj)
                        <div class="row mb-3">
                            <div class="col-2 text-secondary pe-0">{{ $obj['from'] }} - {{ $obj['to'] }}</div>
                            <div class="col">
                                <p class="mb-1">{{ $obj['institution'] }}</p>
                                <small class="text-secondary">{{ $obj['degree'] }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="text-secondary">Nothing to show</span>
                @endif

            </div>
            <div id="menu1" class="container tab-pane fade"><br>
                @if (!empty($tutorDetails['work']))
                    @foreach ($tutorDetails['work'] as $obj)
                        <div class="row mb-3">
                            <div class="col-2 text-secondary pe-0">{{ $obj['from'] }} - {{ $obj['to'] }}</div>
                            <div class="col">
                                <p class="mb-1">{{ $obj['company'] }}</p>
                                <small class="text-secondary">{{ $obj['role'] }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="text-secondary">Nothing to show</span>
                @endif
            </div>
            <div id="menu2" class="container tab-pane fade"><br>
                @if (!empty($tutorDetails['certs']))
                    @foreach ($tutorDetails['certs'] as $obj)
                        <div class="row mb-3">
                            <div class="col-2 text-secondary">{{ $obj['from'] }}</div>
                            <div class="col">
                                <p class="mb-1">{{ $obj['certification'] }}</p>
                                <small class="text-secondary">{{ $obj['description'] }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="text-secondary">Nothing to show</span>
                @endif
            </div>
            <div id="menu3" class="container tab-pane fade"><br>
                @if (!empty($tutorDetails['skills']))
                    <div class="w-100-h-100 flex-start gap-2 skills-list">
                        @foreach ($tutorDetails['skills'] as $skill)
                            <span class="badge bg-secondary skill-badge">{{ $skill }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-secondary">Nothing to show</span>
                @endif
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
                <x-sl-button type="button" id="btn-terminate" style="danger" text="Terminate" data-tutor-name="{{ $tutorDetails['fullname'] }}"/>
            </div>
        </div>
        <form action="{{ route('admin.terminate-tutor') }}" method="POST" class="d-none" id="terminateTutorForm">
            @csrf
            <input type="hidden" name="userid" value="{{ $tutorDetails['hashedId'] }}">
        </form>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin/show-tutor.js') }}"></script>
    <script src="{{ asset('assets/lib/dompurify/purify.min.js') }}"></script>
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
@endpush
