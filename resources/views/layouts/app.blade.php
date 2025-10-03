<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>


    <meta charset="utf-8" />
    <title>Dashboard - Transformasi Data Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicons/fav.ico') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem("themeMode");
            if (savedTheme) {
                const { theme } = JSON.parse(savedTheme);
                const body = document.documentElement;

                if (theme === "dark") {
                body.setAttribute("data-bs-theme", "dark");
                body.className = "menuitem-active";

                document.write('<link href="{{ asset('assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" />');
                } else {
                body.setAttribute("data-bs-theme", "light");
                document.write('<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />');
                }
            } else {
                document.write('<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />');
            }
        })();
    </script>
    <link href="{{ asset('assets/css/style-custom.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/animate.css/animate.min.css') }}" rel="stylesheet" type="text/css" />




    @stack('header')
</head>

<body data-bs-theme="light" id="body">
    <!-- leftbar-tab-menu -->
    <div class="leftbar-tab-menu">
        <div class="main-icon-menu">
            <a href="/homes" class="logo logo-metrica d-block text-center">
                <span>
                    <img src="{{ asset('assets/img/logos/tdd-second.png') }}" alt="logo-small" class="logo-sm">
                </span>
            </a>
            <div class="main-icon-menu-body">
                <div class="position-reletive h-100" data-simplebar style="overflow-x: hidden;">
                    <ul class="nav nav-tabs" role="tablist" id="tab-menu">
                        <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard"
                            data-bs-trigger="hover">
                            <a href="#MetricaDashboard" id="dashboard-tab" class="nav-link" data-bs-toggle="tab"
                                role="tab" aria-selected="true">
                                <i class="ti ti-smart-home menu-icon"></i>
                            </a><!--end nav-link-->
                        </li><!--end nav-item-->
                        <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Apps"
                            data-bs-trigger="hover">
                            <a href="#MetricaApps" id="apps-tab"
                                class="nav-link  {{ request()->is(['user-leave*', 'user-salar*']) ? 'active' : '' }}">
                                <i class="ti ti-users menu-icon"></i>
                            </a><!--end nav-link-->
                        </li><!--end nav-item-->
                    </ul><!--end nav-->
                </div><!--end /div-->
            </div><!--end main-icon-menu-body-->
            <div class="pro-metrica-end">
                <a href="" class="profile">
                    <img src="{{ asset('assets/images/users/user-4.jpg') }}" alt="profile-user"
                        class="rounded-circle thumb-sm">
                </a>
            </div><!--end pro-metrica-end-->
        </div>
        <!--end main-icon-menu-->

        <div class="main-menu-inner">
            <!-- LOGO -->
            <div class="topbar-left">
                {{-- <a href="/" class="logo">
                    <span>
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-large"
                            class="logo-lg logo-dark">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo-large" class="logo-lg logo-light">
                    </span>
                </a><!--end logo--> --}}
                <a href="/homes">
                    <div class="left--menu">
                        <div class="title-tdd--main">Transformasi</div>
                        <div class="title-tdd--sub">Data Digital</div>
                    </div>
                </a>
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
                            <a class="nav-link" href="{{ url('/homes') }}">Home</a>
                        </li><!--end nav-item-->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/calendar') }}">Calendar</a>
                        </li><!--end nav-item-->
                    </ul><!--end nav-->

                    <div class="title-box">
                        <h6 class="menu-title">{{ Auth::user()->role->role_name }} Area</h6>
                    </div>

                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#Absensi" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="Absensi">
                                    Absensi
                                </a>
                                <div class="collapse " id="Absensi">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                           <a class="nav-link" href="{{ route('attendance.index') }}">Absensi</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('attendance.list') }}">Riwayat Absensi</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div><!-- end Dashboards -->

                <div id="MetricaApps"
                    class="main-icon-menu-pane tab-pane {{ request()->is(['user-leave*', 'user-salaries*']) ? 'active show' : '' }}"
                    role="tabpanel" aria-labelledby="apps-tab">
                    <div class="title-box">
                        <h6 class="menu-title">Menu {{ Auth::user()->role->role_name }}</h6>

                    </div>

                    <div class="collapse navbar-collapse" id="sidebarCollapse">
                        <ul class="navbar-nav">
                            @php
                                $user = Auth::user();
                                $role = $user->role_name;
                                $menus = $user->menu_items;
                            @endphp

                            @php
                                $menuMap = [
                                    'Cuti' => ['all' => 'user-leave.index', 'user' => 'user-leave.user'],
                                    // 'Absensi' => ['route' => 'attendance.list'],
                                    'Gaji Bulanan' => ['route' => 'monthly.salary.index'],
                                    'Draft Gaji Bulanan' => ['route' => 'finance.monthly.salary.draft'],
                                    'Gaji' => ['route' => 'user-salaries.index'],
                                    'Shift Karyawan' => ['route' => 'user-shift.index'],
                                    'Shift' => ['route' => 'shift.index'],
                                    'Bagian' => ['route' => 'role.index'],
                                    'Karyawan' => ['route' => 'user-employee.index'],
                                    'Configuration' => ['route' => 'config.index'],
                                    'Kontrak' => ['route' => 'user-contract.index'],
                                    'Surat Referensi' => ['route' => 'user-references.index'],
                                ];
                            @endphp

                            @if(in_array('All', $menus))
                                {{-- Supervisor: show all mapped menus (uses "all" route when specified) --}}
                                @foreach($menuMap as $label => $meta)
                                    @php
                                        $routeName = $meta['route'] ?? ($meta['all'] ?? ($meta['user'] ?? null));
                                    @endphp
                                    @if($routeName)
                                        <li class="nav-item"><a href="{{ route($routeName) }}" class="nav-link">{{ $label }}</a></li>
                                    @endif
                                @endforeach
                            @else
                                {{-- Non-supervisor: always show Cuti (user-specific), then show other allowed menus --}}
                                <li class="nav-item"><a href="{{ route($menuMap['Cuti']['user']) }}" class="nav-link">Cuti</a></li>

                                @foreach($menuMap as $label => $meta)
                                    @continue($label === 'Cuti')
                                    @if(in_array($label, $menus) && isset($meta['route']))
                                        <li class="nav-item"><a href="{{ route($meta['route']) }}" class="nav-link">{{ $label }}</a></li>
                                    @endif
                                @endforeach

                            @endif
                        </ul>
                    </div>

                </div><!-- end Crypto -->


            </div>
            <!--end menu-body-->
        </div><!-- end main-menu-inner-->
    </div>
    <!-- end leftbar-tab-menu-->

    <!-- Top Bar Start -->
    <!-- Top Bar Start -->
    <div class="topbar">
        <nav class="navbar-custom" id="navbar-custom">
            <ul class="list-unstyled topbar-nav float-end mb-0">
                <!-- <li class="dropdown">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="/assets/images/flags/us_flag.jpg" alt="" class="thumb-xxs rounded-circle">
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><img src="/assets/images/flags/us_flag.jpg" alt="" height="15"
                                class="me-2">English</a>
                        <a class="dropdown-item" href="#"><img src="/assets/images/flags/spain_flag.jpg" alt=""
                                height="15" class="me-2">Spanish</a>
                        <a class="dropdown-item" href="#"><img src="/assets/images/flags/germany_flag.jpg" alt=""
                                height="15" class="me-2">German</a>
                        <a class="dropdown-item" href="#"><img src="/assets/images/flags/french_flag.jpg" alt=""
                                height="15" class="me-2">French</a>
                    </div>
                </li> -->

                <li class="dropdown">
                    <div class="nav-link nav-icon">
                        <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i id="themeMode" class="mdi mdi-weather-sunny"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item d-flex align-items-center theme-Mode" href="#"
                                data-icon="mdi-weather-sunny" data-theme="light">
                                <i class="mdi mdi-weather-sunny me-2"></i>
                                Light Mode
                            </a>
                            <a class="dropdown-item d-flex align-items-center theme-Mode" href="#"
                                data-icon="mdi-weather-night" data-theme="dark">
                                <i class="mdi mdi-weather-night me-2"></i>
                                Dark Mode
                            </a>
                        </div>
                    </div>
                </li>

                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-mail"></i>
                        <span class="alert-badge"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg pt-0">

                        <h6
                            class="dropdown-item-text font-15 m-0 py-3 border-bottom d-flex justify-content-between align-items-center">
                            Notifications <span class="badge bg-soft-primary badge-pill">2</span>
                        </h6>
                        <div class="notification-menu" data-simplebar>
                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">2 min ago</small>
                                <div class="media">
                                    <div class="avatar-md bg-soft-primary">
                                        <i class="ti ti-chart-arcs"></i>
                                    </div>
                                    <div class="media-body align-self-center ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark">Your order is placed</h6>
                                        <small class="text-muted mb-0">Dummy text of the printing and industry.</small>
                                    </div>
                                </div>
                            </a>

                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">10 min ago</small>
                                <div class="media">
                                    <div class="avatar-md bg-soft-primary">
                                        <i class="ti ti-device-computer-camera"></i>
                                    </div>
                                    <div class="media-body align-self-center ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark">Meeting with designers</h6>
                                        <small class="text-muted mb-0">It is a long established fact that a
                                            reader.</small>
                                    </div>
                                </div>
                            </a>

                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">40 min ago</small>
                                <div class="media">
                                    <div class="avatar-md bg-soft-primary">
                                        <i class="ti ti-diamond"></i>
                                    </div>
                                    <div class="media-body align-self-center ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark">UX 3 Task complete.</h6>
                                        <small class="text-muted mb-0">Dummy text of the printing.</small>
                                    </div>
                                </div>
                            </a>

                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">1 hr ago</small>
                                <div class="media">
                                    <div class="avatar-md bg-soft-primary">
                                        <i class="ti ti-drone"></i>
                                    </div>
                                    <div class="media-body align-self-center ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark">Your order is placed</h6>
                                        <small class="text-muted mb-0">It is a long established fact that a
                                            reader.</small>
                                    </div>
                                </div>
                            </a>

                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">2 hrs ago</small>
                                <div class="media">
                                    <div class="avatar-md bg-soft-primary">
                                        <i class="ti ti-users"></i>
                                    </div>
                                    <div class="media-body align-self-center ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark">Payment Successfull</h6>
                                        <small class="text-muted mb-0">Dummy text of the printing.</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <a href="javascript:void(0);" class="dropdown-item text-center text-primary">
                            View all <i class="fi-arrow-right"></i>
                        </a>
                    </div>
                </li>

                <li class="dropdown">
                    <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <div class="menu-nav--users">
                            <img src="https://static.dazz2.com/upload/auth/photo/d2e9985df3639586e9fabd281aade533.jpg"
                                alt="profile-user" class="rounded-circle me-2 thumb-sm" />
                            <div>
                                <small class="d-none d-md-block font-11">Role {{ Auth::user()->role->role_name }}</small>
                                <span class="d-none d-md-block fw-semibold font-12">
                                    {{ Auth::user()->name }}
                                    <i class="mdi mdi-chevron-down"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                            <i class="ti ti-user font-16 me-1 align-text-bottom"></i>
                            Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="ti ti-settings font-16 me-1 align-text-bottom"></i>
                            Settings
                        </a>
                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item text-danger" href="{{ route('login.logout') }}">
                            <i class="ti ti-power font-16 me-1 align-text-bottom"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>

            <ul class="list-unstyled topbar-nav mb-0">
                <li>
                    <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </li>
                <li class="hide-phone app-search">
                    <form role="search" action="#" method="get">
                        <input type="search" name="search" class="form-control top-search mb-0"
                            placeholder="Type text...">
                        <button type="submit"><i class="ti ti-search"></i></button>
                    </form>
                </li>
            </ul>
        </nav>
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
                &copy;
                <script>
                    document.write(new Date().getFullYear())
                </script> Transformasi Data Digital
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

    {{-- datatables --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom/tdd.timer.clock.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/tdd.app.mode.js') }}"></script>
    @stack('scripts')

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Tidak ada entri yang tersedia",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Berikutnya"
                    }
                },
                "columnDefs": [{
                "targets": [ 0 ],
                "className": "dt-body-left",
                }]
            });
        });
    </script>

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
