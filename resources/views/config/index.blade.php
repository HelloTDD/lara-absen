@extends('layouts.app')
@section('page-title', 'Configuration')
@section('content')
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                </div><!--end card-body-->
                <div class="card-body p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#Settings" role="tab"
                                aria-selected="false">Settings</a>
                        </li>
                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div class="tab-pane p-3 show active" id="Settings" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6 col-xl-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h4 class="card-title">Configuration Information</h4>
                                                </div><!--end col-->
                                            </div> <!--end row-->
                                        </div><!--end card-header-->
                                        <div class="card-body">
                                            @if ($errors->any() && request()->routeIs('profile.update'))
                                                <div class="alert alert-danger mb-3">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if(session('success') && request()->routeIs('profile.update'))
                                                {{-- Check if the session has a success message --}}
                                                <div class="alert alert-success mb-3">
                                                    {{ session('success') }}
                                                </div>
                                            @endif
                                            <form action="{{ route('config.update') }}" method="post">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group mb-3 row">
                                                    <label
                                                        class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Key</label>
                                                    <div class="col-lg-9 col-xl-8">
                                                        <input type="text" name="key" class="form-control"
                                                            value="{{ $configs->key ?? '' }}" placeholder="masukan key"
                                                            aria-describedby="basic-addon1" required>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3 row">
                                                    <label
                                                        class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Value</label>
                                                    <div class="col-lg-9 col-xl-8">
                                                        <input type="number" name="value" class="form-control"
                                                            value="{{ $configs->value  ?? '' }}" placeholder="masukan value"
                                                            aria-describedby="basic-addon1" required>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3 row">
                                                    <label
                                                        class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Batas Absen 1 Bulan</label>
                                                    <div class="col-lg-9 col-xl-8">
                                                        <input type="text" name="attendance_count" class="form-control"
                                                            value="{{ $configs->attendance_count ?? '' }}" placeholder="masukan batas absen 1 bulan"
                                                            aria-describedby="basic-addon1" required>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3 row">
                                                    <label
                                                        class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">deskripsi</label>
                                                    <div class="col-lg-9 col-xl-8">
                                                        <textarea name="description" class="form-control"
                                                            placeholder="masukan deskripsi" rows="3"
                                                            aria-describedby="basic-addon1">{{ $configs->description ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3 row">
                                                    <div class="col-lg-9 col-xl-8 offset-lg-3">
                                                        <button type="submit" class="btn btn-de-primary">Update</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> <!--end col-->
                            </div><!--end row-->
                        </div>
                    </div>
                </div> <!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->

    </div><!-- container -->

    <!--Start Rightbar-->
    <!--Start Rightbar/offcanvas-->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="Appearance" aria-labelledby="AppearanceLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="m-0 font-14" id="AppearanceLabel">Appearance</h5>
            <button type="button" class="btn-close text-reset p-0 m-0 align-self-center" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <h6>Account Settings</h6>
            <div class="p-2 text-start mt-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="settings-switch1">
                    <label class="form-check-label" for="settings-switch1">Auto updates</label>
                </div><!--end form-switch-->
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="settings-switch2" checked>
                    <label class="form-check-label" for="settings-switch2">Location Permission</label>
                </div><!--end form-switch-->
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="settings-switch3">
                    <label class="form-check-label" for="settings-switch3">Show offline Contacts</label>
                </div><!--end form-switch-->
            </div><!--end /div-->
            <h6>General Settings</h6>
            <div class="p-2 text-start mt-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="settings-switch4">
                    <label class="form-check-label" for="settings-switch4">Show me Online</label>
                </div><!--end form-switch-->
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="settings-switch5" checked>
                    <label class="form-check-label" for="settings-switch5">Status visible to all</label>
                </div><!--end form-switch-->
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="settings-switch6">
                    <label class="form-check-label" for="settings-switch6">Notifications Popup</label>
                </div><!--end form-switch-->
            </div><!--end /div-->
        </div><!--end offcanvas-body-->
    </div>
    <!--end Rightbar/offcanvas-->
    <!--end Rightbar-->

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
@endsection
