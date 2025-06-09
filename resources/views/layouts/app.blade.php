<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>


    <meta charset="utf-8" />
    <title>{{env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('header')
</head>

<body id="body">
    <!-- leftbar-tab-menu -->
    <div class="leftbar-tab-menu">
        <div class="main-icon-menu">
            <a href="index.html" class="logo logo-metrica d-block text-center">
                <span>
                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt="logo-small" class="logo-sm">
                </span>
            </a>
            <div class="main-icon-menu-body">
                <div class="position-reletive h-100" data-simplebar style="overflow-x: hidden;">
                    <ul class="nav nav-tabs" role="tablist" id="tab-menu">
                        <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard" data-bs-trigger="hover">
                            <a href="#MetricaDashboard" id="dashboard-tab" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="true">
                                <i class="ti ti-smart-home menu-icon"></i>
                            </a><!--end nav-link-->
                        </li><!--end nav-item-->
                        <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Apps" data-bs-trigger="hover">
                            <a href="#MetricaApps" id="apps-tab" class="nav-link  {{ request()->is(['user-leave*','user-salar*']) ? 'active' : '' }}">
                                <i class="ti ti-users menu-icon"></i>
                            </a><!--end nav-link-->
                        </li><!--end nav-item-->
                    </ul><!--end nav-->
                </div><!--end /div-->
            </div><!--end main-icon-menu-body-->
            <div class="pro-metrica-end">
                <a href="" class="profile">
                    <img src="{{ asset('assets/images/users/user-4.jpg') }}" alt="profile-user" class="rounded-circle thumb-sm">
                </a>
            </div><!--end pro-metrica-end-->
        </div>
        <!--end main-icon-menu-->

        <div class="main-menu-inner">
            <!-- LOGO -->
            <div class="topbar-left">
                <a href="index.html" class="logo">
                    <span>
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo-large" class="logo-lg logo-light">
                    </span>
                </a><!--end logo-->
            </div><!--end topbar-left-->
            <!--end logo-->
            <div class="menu-body navbar-vertical tab-content" data-simplebar>
                <div id="MetricaDashboard" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="dasboard-tab">
                    <div class="title-box">
                        <h6 class="menu-title">Dashboard</h6>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Home</a>
                        </li><!--end nav-item-->

                    </ul><!--end nav-->
                </div><!-- end Dashboards -->

                <div id="MetricaApps" class="main-icon-menu-pane tab-pane {{ request()->is(['user-leave*','user-salaries*']) ? 'active show' : '' }}" role="tabpanel"
                    aria-labelledby="apps-tab">
                    <div class="title-box">
                        <h6 class="menu-title">Managemen Karyawan</h6>
                    </div>

                    <div class="collapse navbar-collapse" id="sidebarCollapse">
                        <!-- Navigation -->
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#sidebarAnalytics" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarAnalytics">
                                    Managemen Karyawan
                                </a>
                                <div class="collapse " id="sidebarAnalytics">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a href="{{ route('user-salaries.index') }}" class="nav-link ">Gaji</a>
                                        </li><!--end nav-item-->
                                        <li class="nav-item">
                                            <a href="{{ route('user-leave.index') }}" class="nav-link ">Cuti</a>
                                        </li><!--end nav-item-->
                                        <li class="nav-item">
                                            <a href="{{ route('attendance.list') }}" class="nav-link ">Absensi</a>
                                        </li><!--end nav-item-->

                                        @if(Auth::user()->is_admin == 1)
                                        <li class="nav-item">
                                            <a href="{{ route('user-shift.index') }}" class="nav-link ">Shift Karyawan</a>
                                        </li><!--end nav-item-->
                                        <li class="nav-item">
                                            <a href="{{ route('shift.index') }}" class="nav-link ">Shift</a>
                                        </li><!--end nav-item-->
                                        @endif

                                        <li class="nav-item">
                                            <a href="{{ route('user-contract.index') }}" class="nav-link ">Kontrak</a>
                                        </li><!--end nav-item-->
                                    </ul><!--end nav-->
                                </div><!--end sidebarAnalytics-->
                            </li><!--end nav-item-->
                        </ul><!--end navbar-nav--->
                    </div><!--end sidebarCollapse-->
                </div><!-- end Crypto -->


            </div>
            <!--end menu-body-->
        </div><!-- end main-menu-inner-->
    </div>
    <!-- end leftbar-tab-menu-->

    <!-- Top Bar Start -->
    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- Navbar -->
        <nav class="navbar-custom" id="navbar-custom">
            <ul class="list-unstyled topbar-nav float-end mb-0">

                <li class="dropdown">
                    <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/users/user-4.jpg') }}" alt="profile-user" class="rounded-circle me-2 thumb-sm" />
                            <div>
                                <span class="d-none d-md-block fw-semibold font-12">{{ Auth::user()->name }} <i
                                        class="mdi mdi-chevron-down"></i></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#"><i class="ti ti-user font-16 me-1 align-text-bottom"></i> Profile</a>
                        <a class="dropdown-item" href="#"><i class="ti ti-settings font-16 me-1 align-text-bottom"></i> Settings</a>
                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item" href="{{ route('login.logout') }}"><i class="ti ti-power font-16 me-1 align-text-bottom"></i> Logout</a>
                    </div>
                </li><!--end topbar-profile-->
            </ul><!--end topbar-nav-->

            <ul class="list-unstyled topbar-nav mb-0">
                <li>
                    <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </li>
            </ul>
        </nav>
        <!-- end navbar-->
    </div>
    <!-- Top Bar End -->
    <!-- Top Bar End -->

    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content-tab">

            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="float-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">TDD System</a>
                                    </li><!--end nav-item-->
                                    <li class="breadcrumb-item"><a href="#">Pages</a>
                                    </li><!--end nav-item-->
                                    <li class="breadcrumb-item active">@yield('page-title')</li>
                                </ol>
                            </div>
                            <h4 class="page-title">@yield('page-title')</h4>
                        </div><!--end page-title-box-->
                    </div><!--end col-->
                </div>
                <!-- end page title end breadcrumb -->
                @yield('content')

            </div><!-- container -->

            <!--Start Footer-->
            <!-- Footer Start -->
            <footer class="footer text-center text-sm-start">
                &copy; <script>
                    document.write(new Date().getFullYear())
                </script> Metrica <span class="text-muted d-none d-sm-inline-block float-end">Crafted with <i
                        class="mdi mdi-heart text-danger"></i> by Mannatthemes</span>
            </footer>
            <!-- end Footer -->
            <!--end footer-->
        </div>
        <!-- end page content -->
    </div>
    <!-- end page-wrapper -->

    <!-- Javascript  -->
    <!-- vendor js -->

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')

    @session('success')
    <script>
        Swal.fire({
            title: 'Success',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
    @endsession

    @session('error')
    <script>
        Swal.fire({
            title: 'Error',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>
    @endsession
</body>
<!--end body-->

</html>
