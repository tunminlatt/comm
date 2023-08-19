<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=yes">
    <title>@yield('title_prefix', config('adminlte.title_prefix', ''))
    @yield('title', config('adminlte.title', 'AdminLTE 3'))
    @yield('title_postfix', config('adminlte.title_postfix', ''))</title>
    @if(! config('adminlte.enabled_laravel_mix'))
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css?v=1.1.0') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    @include('adminlte::plugins', ['type' => 'css'])

    @yield('adminlte_css_pre')

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    @yield('adminlte_css')

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @else
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
</head>
<body class="@yield('classes_body')" @yield('body_data') id="main-body">

<div class="modal fade loading-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-sm">
            <span class="fa fa-spinner fa-spin fa-3x"></span>
    </div>
</div>
@yield('body')

@if(! config('adminlte.enabled_laravel_mix'))
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

@include('adminlte::plugins', ['type' => 'js'])

@yield('adminlte_js')
@else
<script src="{{ asset('js/app.js') }}"></script>
@endif
</body>
</html>
