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

<body class="layout-fluid">
    <script src="{{url('/admin')}}/dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page">
        <!-- Navbar -->
        @include('layouts.admin.header')
        @include('layouts.admin.navbar')

        <div class="page-wrapper">
            <!-- Page header -->
            <div class="page-header d-print-none">
                @stack('page-haeder')
            </div>
            <!-- Page body -->
            <div class="page-body">
                @yield('content')
            </div>
            @include('layouts.admin.footer')
        </div>
    </div>

    <!-- Global Modal -->
    <div class="modal modal-blur fade" id="globalModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal-content-container">
                <!-- Konten AJAX akan dirender di sini -->
                <div class="modal-body text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.admin.components.js')
    @stack('scripts')
</body>

</html>
