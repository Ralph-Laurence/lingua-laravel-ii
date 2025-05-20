@extends('shared.base-admin')
@section('title')
    Dashboard
@endsection

@section('content')
    <section class="container mb-5">

        <div class="row mx-auto mb-3 counter-cards-container">
            <div class="col-3">
                <div class="card bg-primary">
                    <div class="card-body text-white">
                        <div class="w-100 d-flex align-items-center">
                            <h6 class="flex-fill m-0">Total Members</h6>
                            <h5 class="fw-bold m-0">{{ $totals['totalMembers'] }}</h5>
                        </div>
                        <hr class="my-1">
                        <small class="flex-fill m-0 text-primary-accent text-12">*Sum of tutors and learners</small>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card bg-teal">
                    <div class="card-body text-white">
                        <div class="w-100 d-flex align-items-center">
                            <h6 class="flex-fill m-0">Total Tutors</h6>
                            <h5 class="fw-bold m-0">{{ $totals['totalTutors'] }}</h5>
                        </div>
                        <hr class="my-1">
                        <small class="flex-fill m-0 text-teal-accent text-12">*All verified tutors</small>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card bg-orange">
                    <div class="card-body text-white">
                        <div class="w-100 d-flex align-items-center">
                            <h6 class="flex-fill m-0">Total Learners</h6>
                            <h5 class="fw-bold m-0">{{ $totals['totalLearners'] }}</h5>
                        </div>
                        <hr class="my-1">
                        <small class="flex-fill text-orange-accent m-0 text-12">*All active learners</small>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card bg-danger">
                    <div class="card-body text-white">
                        <div class="w-100 d-flex align-items-center">
                            <h6 class="flex-fill m-0">Pending Registrations</h6>
                            <h5 class="fw-bold m-0">{{ $totals['totalPending'] }}</h5>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex align-items-center gap-2">
                            <small class="flex-fill text-red-accent opacity-85 m-0 text-12">* Unapproved tutors</small>
                            @if (!empty($totals['totalPending']))
                                <a class="btn btn-sm btn-light text-12"
                                    href="{{ route('admin.dashboard.view-pending') }}">View</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mx-auto mb-4">
            <div class="col-8 top-tutors-col">
                <div class="card">
                    <div class="card-body">
                        <h6 id="chart-title">Top 5 tutors</h6>

                        @if (array_key_exists('topTutors', $totals) && !empty($totals['topTutors']))
                            <textarea id="chartData-top-tutors" class="d-none">
                                {{ $totals['topTutors'] }}
                            </textarea>
                        @endif

                        <div class="d-flex w-100">
                            <div class="container flex-fill">
                                <canvas id="topTutorsChart"></canvas>
                            </div>
                            <div class="photos d-flex align-items-center flex-column">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-2">
            <h6 id="chart-title" class="text-center">Tutor with most Learners</h6>
            <div class="card">
                <div class="card-body text-center">
                    <img class="mb-1" alt="photo" id="best-tutor-photo" width="80" height="80">
                    <p class="fw-bold my-1">
                        <i class="fas fa-award me-1 text-primary"></i>
                        <span id="best-tutor-name" class="text-14"></span>
                    </p>
                    <a id="best-tutor-details" class="btn btn-sm btn-primary w-100 mt-2 text-13" role="button">About Tutor</a>
                </div>
            </div>
        </div> --}}
            <div class="col-2">
                <h6 id="chart-title" class="text-center">Tutor with most Learners</h6>
                <div class="card">
                    <div class="card-body text-center">
                        @if (isset($totals['topTutor']) && !empty($totals['topTutor']))
                            <img src="{{ $totals['topTutor']['tutorPhoto'] }}" class="mb-1" alt="photo"
                                id="best-tutor-photo" width="80" height="80">
                            <p class="fw-bold my-1">
                                <i class="fas fa-medal me-1 text-primary"></i>
                                <span id="best-tutor-name" class="text-14">{{ $totals['topTutor']['tutorName'] }}</span>
                            </p>
                            <a href="{{ $totals['topTutor']['tutorDetails'] }}"
                                class="btn btn-sm btn-primary w-100 mt-2 text-13" role="button">About Tutor</a>
                        @else
                            <div class="text-secondary text-14">
                                None yet
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-2">
                <h6 id="chart-title" class="text-center light">Learner with most Tutors</h6>
                <div class="card">
                    <div class="card-body text-center">
                        @if (isset($totals['topLearner']) && !empty($totals['topLearner']))
                            <img src="{{ $totals['topLearner']['learnerPhoto'] }}" class="mb-1" alt="photo"
                                id="best-learner-photo" width="80" height="80">
                            <p class="fw-bold my-1">
                                <i class="fas fa-star me-1 text-primary"></i>
                                <span id="best-tutor-name"
                                    class="text-14">{{ $totals['topLearner']['learnerName'] }}</span>
                            </p>
                            <a href="{{ $totals['topLearner']['learnerDetails'] }}"
                                class="btn btn-sm btn-primary w-100 mt-2 text-13" role="button">About Learner</a>
                        @else
                            <div class="text-secondary text-14">
                                None yet
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mx-auto">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h6 id="chart-title" class="text-center">Impaired vs Non-Impaired</h6>
                        <div class="container d-flex align-items-center flex-fill">
                            <canvas id="impairedRatioChart" class="small-donut"></canvas>
                            <canvas id="nonImpairedRatioChart" class="small-donut"></canvas>
                        </div>
                        <textarea class="d-none" id="chartdata-impared-ratio">{{ json_encode($totals['impairmentRatio']) }}</textarea>
                        <textarea class="d-none" id="chartdata-nonimpared-ratio">{{ json_encode($totals['nonImpairedRatio']) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col groupedImpairmentsCol">
                <div class="card">
                    <div class="card-body">
                        <h6 id="chart-title" class="text-center">Common Impairments</h6>
                        <div class="container d-flex align-items-center flex-fill">
                            <canvas id="groupedImpairmentsChart"></canvas>
                        </div>
                        <textarea class="d-none" id="chartdata-grouped-impairments">{{ json_encode($totals['totalGroupedImpairments']) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        #chart-title {
            width: fit-content;
            padding: 4px 8px;
            border-radius: 8px;
            background: #2E244D;
            color: white;
            font-size: 13px;
        }

        #chart-title.light {
            background: #6c757d;
        }

        .text-primary-accent {
            color: #a2c3f5;
        }

        .bg-teal {
            background: #20A264;
        }

        .text-teal-accent {
            color: #97e7c1;
        }

        .bg-orange {
            background: #FF7701;
        }

        .text-orange-accent {
            color: #fcd5b4;
        }

        .text-red-accent {
            color: #ffdbdb;
        }

        .photo-item {
            width: fit-content;
        }

        .photo-item img {
            height: 36px;
            width: 36px;
            border-radius: 4px;
            border: 1px solid #E5E5E5;
        }

        #best-tutor-photo,
        #best-learner-photo {
            width: 80px;
            height: 80px;
            border-radius: .25rem;
        }

        .counter-cards-container .card {
            max-height: 90px;
            height: 90px;
        }

        .small-donut {
            width: 240px;
            height: 240px;
            max-width: 240px;
            max-height: 240px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/lib/chartjs/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/js/admin/dashboard.js') }}"></script>
@endpush
