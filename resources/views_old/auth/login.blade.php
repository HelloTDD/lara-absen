<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>


    <meta charset="utf-8" />
    <title>Login Karyawan - Transformasi Data Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicons/fav.ico') }}">

    <link href="{{ asset('assets/libs/magic.css/magic.min.css') }}" rel="stylesheet" />


    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style-custom.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/animate.css/animate.min.css') }}" rel="stylesheet" type="text/css">


</head>
<body id="body" class="auth-page"
    style="background-image: url('assets/images/p-1.png'); background-size: cover; background-position: center center;">
    <!-- Log In page -->
    <div class="container-md">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card card--login magictime swashIn">
                                <div class="card-body p-0 auth-header-box">
                                    <div class="text-center p-3">
                                        <a href="index.html" class="logo logo-admin">
                                            <img src="assets/img/logos/tdd-second.png" height="50" alt="logo"
                                                class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold text-white font-18">
                                            Welcome Back!
                                        </h4>
                                        <p class="text-muted  mb-0">Masuk untuk melanjutkan ke Dashboard.</p>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    @session('error')
                                        <div class="alert alert-danger alert-dismissible fade show my-4" role="alert">
                                            <strong>Login Gagal!</strong> {{ session('error') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endsession
                                    <form class="my-4" action="{{ route('login.ceklogin') }}" method="POST" autocomplete="off" id="tdd_login_form">
                                        @method('POST')
                                        @csrf
                                        <div class="fv-row form-group mb-2">
                                            <label class="form-label" for="username">Username</label>
                                            <input type="text" class="form-control rounded-3" id="username"
                                                name="username" placeholder="Enter username">
                                        </div>

                                        <div class="fv-row form-group">
                                            <label class="form-label" for="userpassword">Password</label>
                                            <input type="password" class="form-control rounded-3" name="password"
                                                id="userpassword" placeholder="Enter password">
                                        </div>

                                        <div class="form-group row mt-3">
                                            <div class="col-sm-6">
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="customSwitchSuccess">
                                                    <label class="form-check-label" for="customSwitchSuccess">Remember
                                                        me</label>
                                                </div>
                                            </div>
                                            {{-- <div class="col-sm-6 text-end">
                                                Don't have an Account?
                                                <a href="page/register.html" class="text-primary font-13">
                                                    <i class="dripicons-lock"></i>
                                                    Sign Up
                                                </a>
                                            </div> --}}
                                        </div>

                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <!-- <button class="btn btn-primary rounded-3" id="tdd_login_submit"
                                                        type="submit">
                                                        Log In
                                                        <i class="fas fa-sign-in-alt ms-1"></i>
                                                    </button> -->

                                                    <button class="btn btn-primary rounded-3" id="tdd_login_submit"
                                                        type="submit">
                                                        <span class="indicator-label">
                                                            Log In
                                                            <i class="fas fa-sign-in-alt ms-1"></i>
                                                        </span>

                                                        <span class="indicator-progress">
                                                            Please wait... <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- <hr class="hr-dashed mt-4">
                                    <div class="text-center mt-n5">
                                        <h6 class="card-bg px-3 my-4 d-inline-block">Or Login With</h6>
                                    </div> -->

                                    {{-- <div class="separator separator-content border-dark my-15">
                                        <h6 class="card-bg w-md-150px">Or Login With</h6>
                                    </div> --}}

                                    {{-- <div class="form-group mb-1 row">
                                        <div class="col-12">
                                            <div class="d-grid gap-3">
                                                <a href="page/absensi-masuk-face.html"
                                                    class="btn btn-outline-primary rounded-3">
                                                    Absen Masuk
                                                    <i class="mdi mdi-face-recognition ms-1"></i>
                                                </a>
                                                <a href="page/absensi-pulang-face.html"
                                                    class="btn btn-outline-primary rounded-3">
                                                    Absen Pulang
                                                    <i class="mdi mdi-face-recognition ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
    <!-- vendor js -->

    <!-- vendor js -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js')}}"></script>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js')}}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11')}}"></script> -->
    <script src="{{ asset('assets/js/pages/sweet-alert.init.js')}}"></script>

    <!-- <script src="assets/js/pages/form-validation.js')}}"></script> -->

    <script>
        "use strict";
        var TDDSignIn = (function () {
            var t, e, r;

            return {
                init: function () {
                    t = document.querySelector("#tdd_login_form");
                    e = document.querySelector("#tdd_login_submit");

                    if (!t || !e) return;

                    r = FormValidation.formValidation(t, {
                        fields: {
                            username: { validators: { notEmpty: { message: "Username is required" } } },
                            password: { validators: { notEmpty: { message: "The password is required" } } }
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: ".fv-row"
                            }),
                        },
                    });

                    e.addEventListener("click", function (i) {
                        i.preventDefault();

                        r.validate().then(function (result) {
                            if (result === "Valid") {
                                e.setAttribute("data-tdd-indicator", "on");
                                e.disabled = true;

                                let formData = new FormData(t);

                                fetch(t.action, {
                                    method: "POST",
                                    body: formData,
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                                    }
                                })
                                .then(res => res.json())
                                .then(res => {
                                    e.removeAttribute("data-tdd-indicator");
                                    e.disabled = false;

                                    if (res.success) {
                                        Swal.fire({
                                            title: "<h3 class='text-success'>Berhasil Masuk!</h3>",
                                            text: "Sedang dialihkan, harap tunggu!",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 2000
                                        }).then(() => {
                                            window.location.href = res.redirect;
                                        });
                                    } else {
                                        Swal.fire({
                                            title: "<h3 class='text-danger'>Terjadi Kesalahan!</h3>",
                                            text: res.message,
                                            icon: "error",
                                            confirmButtonText: "Kembali",
                                            customClass: { confirmButton: "btn btn-danger" }
                                        });
                                    }
                                })
                                .catch(err => {
                                    e.removeAttribute("data-tdd-indicator");
                                    e.disabled = false;
                                    Swal.fire("Error", "Terjadi error sistem!", "error");
                                });
                            }
                        });
                    });
                },
            };
        })();

        TDDUtil.onDOMContentLoaded(function () {
            TDDSignIn.init();
        });

    </script>

</body>

</html>
