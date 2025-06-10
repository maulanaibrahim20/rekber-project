<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{str_replace("_", " ", config('app.name'))}} @yield('title')</title>
    @include('layouts.admin.components.css')
    @stack('styles')
</head>

<body class=" d-flex flex-column bg-white">
    <script src="{{ url('/admin') }}/dist/js/demo-theme.min.js?1692870487"></script>
    {{-- <div class="page page-center">
        <div class="container container-tight py-4">
            @yield('content')
        </div>
    </div> --}}
    <div class="row g-0 flex-fill">
        @yield('content')
    </div>
    @include('layouts.admin.components.js')
    @stack('scripts')
</body>

</html>