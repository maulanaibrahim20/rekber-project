<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="nav-item {{ Request::is('~admin') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Home
                            </span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                    <path d="M12 12l8 -4.5" />
                                    <path d="M12 12l0 9" />
                                    <path d="M12 12l-8 -4.5" />
                                    <path d="M16 5.25l-8 4.5" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Interface
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item" href="./alerts.html">
                                        Alerts
                                    </a>
                                    <a class="dropdown-item" href="./accordion.html">
                                        Accordion
                                    </a>
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#sidebar-authentication"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                            aria-expanded="false">
                                            Authentication
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="./sign-in.html" class="dropdown-item">
                                                Sign in
                                            </a>
                                            <a href="./sign-in-link.html" class="dropdown-item">
                                                Sign in link
                                            </a>
                                            <a href="./sign-in-illustration.html" class="dropdown-item">
                                                Sign in with illustration
                                            </a>
                                            <a href="./sign-in-cover.html" class="dropdown-item">
                                                Sign in with cover
                                            </a>
                                            <a href="./sign-up.html" class="dropdown-item">
                                                Sign up
                                            </a>
                                            <a href="./forgot-password.html" class="dropdown-item">
                                                Forgot password
                                            </a>
                                            <a href="./terms-of-service.html" class="dropdown-item">
                                                Terms of service
                                            </a>
                                            <a href="./auth-lock.html" class="dropdown-item">
                                                Lock screen
                                            </a>
                                            <a href="./2-step-verification.html" class="dropdown-item">
                                                2 step verification
                                            </a>
                                            <a href="./2-step-verification-code.html" class="dropdown-item">
                                                2 step verification code
                                            </a>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="./blank.html">
                                        Blank page
                                    </a>
                                    <a class="dropdown-item" href="./badges.html">
                                        Badges
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./buttons.html">
                                        Buttons
                                    </a>
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#sidebar-cards"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                            aria-expanded="false">
                                            Cards
                                            <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="./cards.html" class="dropdown-item">
                                                Sample cards
                                            </a>
                                            <a href="./card-actions.html" class="dropdown-item">
                                                Card actions
                                                <span
                                                    class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                            </a>
                                            <a href="./cards-masonry.html" class="dropdown-item">
                                                Cards Masonry
                                            </a>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="./carousel.html">
                                        Carousel
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./charts.html">
                                        Charts
                                    </a>
                                    <a class="dropdown-item" href="./colors.html">
                                        Colors
                                    </a>
                                    <a class="dropdown-item" href="./colorpicker.html">
                                        Color picker
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./datagrid.html">
                                        Data grid
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./datatables.html">
                                        Datatables
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./dropdowns.html">
                                        Dropdowns
                                    </a>
                                    <a class="dropdown-item" href="./dropzone.html">
                                        Dropzone
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#sidebar-error"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                            aria-expanded="false">
                                            Error pages
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="./error-404.html" class="dropdown-item">
                                                404 page
                                            </a>
                                            <a href="./error-500.html" class="dropdown-item">
                                                500 page
                                            </a>
                                            <a href="./error-maintenance.html" class="dropdown-item">
                                                Maintenance page
                                            </a>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="./flags.html">
                                        Flags
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./inline-player.html">
                                        Inline player
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                </div>
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item" href="./lightbox.html">
                                        Lightbox
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./lists.html">
                                        Lists
                                    </a>
                                    <a class="dropdown-item" href="./modals.html">
                                        Modal
                                    </a>
                                    <a class="dropdown-item" href="./maps.html">
                                        Map
                                    </a>
                                    <a class="dropdown-item" href="./map-fullsize.html">
                                        Map fullsize
                                    </a>
                                    <a class="dropdown-item" href="./maps-vector.html">
                                        Map vector
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./markdown.html">
                                        Markdown
                                    </a>
                                    <a class="dropdown-item" href="./navigation.html">
                                        Navigation
                                    </a>
                                    <a class="dropdown-item" href="./offcanvas.html">
                                        Offcanvas
                                    </a>
                                    <a class="dropdown-item" href="./pagination.html">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/pie-chart -->
                                        Pagination
                                    </a>
                                    <a class="dropdown-item" href="./placeholder.html">
                                        Placeholder
                                    </a>
                                    <a class="dropdown-item" href="./steps.html">
                                        Steps
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./stars-rating.html">
                                        Stars rating
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                    <a class="dropdown-item" href="./tabs.html">
                                        Tabs
                                    </a>
                                    <a class="dropdown-item" href="./tags.html">
                                        Tags
                                    </a>
                                    <a class="dropdown-item" href="./tables.html">
                                        Tables
                                    </a>
                                    <a class="dropdown-item" href="./typography.html">
                                        Typography
                                    </a>
                                    <a class="dropdown-item" href="./tinymce.html">
                                        TinyMCE
                                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item {{ Request::segment(3) === 'account' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('administrator.account') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/checkbox -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-shield-check">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06" />
                                    <path d="M15 19l2 2l4 -4" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Administrator
                            </span>
                        </a>
                    </li>
                    <li class="nav-item dropdown {{ Request::segment(2) === 'user' ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                User
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item {{ Request::segment(2) === 'user' ? 'active' : '' }}"
                                        href="{{ route('user') }}">
                                        User
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li
                        class="nav-item dropdown {{ Request::segment(2) === 'product' || Request::segment(2) === 'tag' ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-archive">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                    <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" />
                                    <path d="M10 12l4 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Product & Tag
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item {{ Request::segment(2) === 'product' ? 'active' : '' }}"
                                        href="{{ route('product') }}">
                                        Product
                                    </a>
                                    <a class="dropdown-item {{ Request::segment(2) === 'tag' ? 'active' : '' }}"
                                        href="{{ route('tag') }}">
                                        Tags
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li
                        class="nav-item dropdown {{ Request::segment(3) === 'permission' || Request::segment(3) === 'assign-permission' ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Config
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    {{-- @can('menu assign') --}}
                                    <a class="dropdown-item {{ Request::segment(3) === 'permission' ? 'active' : '' }}"
                                        href="{{ route('config.permission') }}">
                                        Permission
                                    </a>
                                    <a class="dropdown-item {{ Request::segment(3) === 'assign-permission' ? 'active' : '' }}"
                                        href="{{ route('config.assign') }}">
                                        Assign Permission
                                    </a>
                                    {{-- @else
                                    <span class="dropdown-item text-muted">
                                        🔒 Tidak punya akses konfigurasi
                                    </span>
                                    @endcan --}}
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
                    <form action="./" method="get" autocomplete="off" novalidate>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                            </span>
                            <input type="text" value="" class="form-control" placeholder="Search…"
                                aria-label="Search in website">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>