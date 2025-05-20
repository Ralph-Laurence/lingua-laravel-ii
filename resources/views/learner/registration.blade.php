@php
    $includeHeader = false;
    $includeFooter = false;
@endphp
@extends('shared.base-members')

@section('content')
{{-- <x-error-blade-display /> --}}
<div class="forms-card m-auto py-4">
        <div class="logo-wrapper flex-center mb-4">
            <img id="logo" src="{{ asset('assets/img/logo-brand-sm.png') }}" alt="" srcset="">
        </div>
        <div class="w-100 card shadow-sm">
            <div class="card-body">

                <form action="{{ route('learner.register-submit') }}" method="post" class="needs-validation" novalidate>
                    @csrf
                    <h6 class="text-center mb-3">Learner Registration</h6>
                    <div class="border p-2 mb-2">
                        <h6 class="text-13 text-secondary mb-2">Basic Information</h6>
                        <div class="row">
                            <div class="col">
                                <label class="text-12" for="firstname">Firstname *</label>
                                <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('firstname') ? 'is-invalid' : '' }}" id="firstname" name="firstname" maxlength="32"
                                    value="{{ old('firstname') }}" required>
                                <div class="invalid-feedback text-12">
                                    @if ($errors->has('firstname'))
                                        @error('firstname')
                                            {{ $message }}
                                        @enderror
                                    @else
                                        Please enter your firstname
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <label class="text-12" for="lastname">Lastname *</label>
                                <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('lastname') ? 'is-invalid' : '' }}" id="lastname" name="lastname" maxlength="32"
                                    value="{{ old('lastname') }}" required>
                                <div class="invalid-feedback text-12">
                                    @if ($errors->has('lastname'))
                                        @error('lastname')
                                            {{ $message }}
                                        @enderror
                                    @else
                                        Please enter your lastname
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">

                                <label class="text-12" for="contact">Contact No. *</label>
                                <div class="input-group">
                                    <span class="input-group-text text-12">
                                        <i class="fi fi-ph"></i> +63
                                    </span>
                                    <input type="tel" class="form-control text-13 rounded-end {{ $errors->any() && $errors->has('contact') ? 'is-invalid' : '' }}" id="contact" name="contact"
                                    value="{{ old('contact') }}" placeholder="9123456789" maxlength="10" required>
                                    <span class="invalid-feedback text-12">
                                        @if ($errors->has('contact'))
                                            @error('contact')
                                                {{ $message }}
                                            @enderror
                                        @else
                                            Please add a contact number
                                        @endif
                                    </span>
                                </div>

                            </div>
                            <div class="col">
                                <label class="text-12" for="address">Address *</label>
                                <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('address') ? 'is-invalid' : '' }}" id="address" name="address" maxlength="150"
                                    value="{{ old('address') }}" required>
                                <div class="invalid-feedback text-12">
                                    @if ($errors->has('address'))
                                        @error('address')
                                            {{ $message }}
                                        @enderror
                                    @else
                                        Please enter your address
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="text-12 h-100 flex-start">Accessibility Needs (hearing & speech)</div>
                            </div>
                            <div class="col">
                                <select class="form-select px-1 text-13" name="disability" style="padding-top: 6px; padding-bottom: 6px;">
                                    @foreach ($disabilityFilter as $k => $v)
                                        @php
                                            $isSelected = old('disability') == $k ? 'selected' : '';
                                        @endphp
                                        <option class="text-14" {{ $isSelected }} value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border p-2">
                        <h6 class="text-13 text-secondary mb-2">Your Account</h6>
                        <div class="row">
                            <div class="col">
                                <label class="text-12" for="email">Email *</label>
                                <input type="email" class="form-control text-13 {{ $errors->any() && $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" maxlength="32"
                                    value="{{ old('email') }}" required>
                                <div class="invalid-feedback text-12">
                                    @if ($errors->has('email'))
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    @else
                                        Please enter your email
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <label class="text-12" for="username">Username *</label>
                                <input type="text" class="form-control text-13 no-spaces {{ $errors->any() && $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" data-label="Username"
                                    value="{{ old('username') }}" maxlength="32" required>
                                <div class="invalid-feedback text-12">
                                    @if ($errors->has('username'))
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    @else
                                        Please choose enter username
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label class="text-12" for="password">Password *</label>
                                <input type="password" class="form-control text-13 {{ $errors->any() && $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" maxlength="32" required>
                                <div class="invalid-feedback text-12">
                                    @if ($errors->has('password'))
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    @else
                                        Please enter your password
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <label class="text-12" for="password_confirmation">Confirm Password *</label>
                                <input type="password" class="form-control text-13" id="password_confirmation" name="password_confirmation" maxlength="64" required>
                                <div class="invalid-feedback text-12">
                                    Please confirm your password
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <p class="text-13 mb-0">
                            Already a member?
                            <a href="/login" class="text-primary">Login instead</a>
                        </p>
                        <button type="submit" class="btn btn-primary btn-sm">Register</button>
                    </div>
                </form>

            </div>
        </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/bootstrap5-form-novalidate.js') }}"></script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script>
        $(() => {
            $('#email').attr('data-bs-toggle', 'tooltip').tooltip({
                trigger: 'focus',
                title: 'Please use a valid email address so we can reach you.',
                placement: 'auto'
            });
        });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/flagicons7.2.3/css/flag-icons.min.css') }}">
    <style>
        body {
            background: #F3F4F6;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-image:
                linear-gradient(
                    to right,
                    rgba(255, 123, 0, 0.5),   /* Red with 50% opacity */
                    /* rgba(0, 4, 255, 0.5), Yellow with 50% opacity */
                    /*rgba(111, 0, 255, 0.5)     Blue with 50% opacity */
                    rgba(17, 0, 255, 0.5)
                ),
                url({{ asset('assets/img/learner_registration_bg_blurred.jpg') }});
            background-size: cover;
            background-position: center;
        }
        .forms-card {
            width: 450px;
            border-radius: .5rem;
        }
        .forms-card #logo {
            /* width: 64px; */
            width: auto;
            height: 64px;
        }
        .forms-card h6 {
            font-family: 'Poppins-SemiBold';
            color: #374151;
        }
    </style>
@endpush
