{{--
    ------------------------------------------------------
      BASE ADMIN LAYOUT IS USED SOLELY BY ADMINISTRATORS
    ------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('meta')
    <title>@yield('title', 'Sign Lingua')</title>

    <!-- FRAMEWORKS, LIBRARIES -->
    <link rel="shortcut icon" href="{{ asset('assets/img/logo-s.png')}}" type="image/x-icon">
    <link rel="stylesheet"    href="{{ asset('assets/lib/bootstrap5/bootstrap.min.css') }}">
    <link rel="stylesheet"    href="{{ asset('assets/lib/fontawesome6.7.2/css/fontawesome.min.css') }}">
    <link rel="stylesheet"    href="{{ asset('assets/lib/fontawesome6.7.2/css/solid.min.css') }}">

    <!-- MAIN STYLES -->
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/header-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-workspace.css') }}">

    @stack('styles')
</head>

<body>

    @yield('before-header')
    @include('shared.header-admin')

    @stack('dialogs')
    @yield('content')

    @if($includeFooter ?? true)
        @include('shared.layouts.footer')
    @endif

    <script src="{{ asset('assets/lib/jquery3.7.1/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/lib/popper2.9.2/popper.min.js') }}"></script>
    <script src="{{ asset('assets/lib/bootstrap5/bootstrap.min.js') }}"></script>

    @stack('scripts')
</body>

</html>
