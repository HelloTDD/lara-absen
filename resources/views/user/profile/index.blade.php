@extends('layouts.app')
@section('page-title', 'Profile')
@section('content')
                <!-- end page title end breadcrumb -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="met-profile">
                                    <div class="row">
                                        <div class="col-lg-4 align-self-center mb-3 mb-lg-0">
                                            <div class="met-profile-main">
                                                <div class="met-profile-main-pic">
                                                    <img src="assets/images/users/user-4.jpg" alt="" height="110" class="rounded-circle">
                                                    <span class="met-profile_main-pic-change">
                                                        <i class="fas fa-camera"></i>
                                                    </span>
                                                </div>
                                                <div class="met-profile_user-detail">
                                                    <h5 class="met-user-name">{{ Auth::user()->name }}</h5>                                                        
                                                    <p class="mb-0 met-user-name-post">Demo</p>                                                        
                                                    <p class="mb-0 met-user-name-post">Salary : Rp {{ empty($data->salary_basic) ? 0 : number_format($data->salary_basic) }} </p>                                                        
                                                </div>
                                            </div>                                                
                                        </div><!--end col-->
                                        
                                        <div class="col-lg-4 ms-auto align-self-center">
                                            <ul class="list-unstyled personal-detail mb-0">
                                                <li class=""><i class="las la-phone mr-2 text-secondary font-22 align-middle"></i> <b> phone </b> : {{ Auth::user()->phone }}</li>
                                                <li class="mt-2"><i class="las la-envelope text-secondary font-22 align-middle mr-2"></i> <b> Email </b> : {{ Auth::user()->email }}</li>
                                                <li>
                                                    <button class="btn btn-primary btn-sm mt-2 ms-4" type="button" onclick="window.location.href='{{ route('profile.slip.gaji') }}'">Download Slip Gaji</button>
                                                </li>                                         
                                            </ul>
                                           
                                        </div><!--end col-->
                                        
                                    </div><!--end row-->
                                </div><!--end f_profile-->                                                                                
                            </div><!--end card-body-->  
                            <div class="card-body p-0">    
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">                                             
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#Settings" role="tab" aria-selected="false">Settings</a>
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
                                                                <h4 class="card-title">Personal Information</h4>                      
                                                            </div><!--end col-->                                                       
                                                        </div>  <!--end row-->                                  
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
                                                        <form action="{{ route('profile.update',['id' => Auth::user()->id ]) }}" method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Name</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input class="form-control" name="name" type="text" value="{{ Auth::user()->name }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Username</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input class="form-control" name="username" type="text" value="{{ Auth::user()->username }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Email</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="las la-at"></i></span>
                                                                    <input type="text" name="email" class="form-control" value="{{ Auth::user()->email }}" placeholder="Email" aria-describedby="basic-addon1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">TTL</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input type="date" name="birth_date" class="form-control" value="{{ Auth::user()->birth_date }}" aria-describedby="basic-addon1">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Phone</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}" placeholder="081234567890" aria-describedby="basic-addon1">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Kelamin</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <select class="form-select" name="gender">
                                                                    <option value="Laki-laki" {{ Auth::user()->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-Laki</option>
                                                                    <option value="Perempuan" {{ Auth::user()->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Address</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <textarea type="text" class="form-control" rows="3" name="address" placeholder="Carolina, St" aria-describedby="basic-addon1">{{ Auth::user()->address }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <div class="col-lg-9 col-xl-8 offset-lg-3">
                                                                <button type="submit" class="btn btn-de-primary">Submit</button>
                                                            </div>
                                                        </div>                                                    
                                                        </form>                      
                                                    </div>                                            
                                                </div>
                                            </div> <!--end col--> 
                                            <div class="col-lg-6 col-xl-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Change Password</h4>
                                                    </div><!--end card-header-->
                                                    <div class="card-body"> 

                                                        @if ($errors->any() && request()->routeIs('profile.change.password'))
                                                            {{-- Check if the session has a success message --}}
                                                            <div class="alert alert-danger mb-3">
                                                                <ul class="mb-0">
                                                                    @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        @if(session('success') && request()->routeIs('profile.change.password'))
                                                            <div class="alert alert-success mb-3">
                                                                {{ session('success') }}
                                                            </div>
                                                        @endif

                                                        <form action="{{ route('profile.change.password') }}" method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Current Password</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input class="form-control" name="current_password" type="password" placeholder="Password">
                                                                <a href="#" class="text-primary font-12">Forgot password ?</a>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">New Password</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input class="form-control" name="password" type="password" placeholder="New Password">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Confirm Password</label>
                                                            <div class="col-lg-9 col-xl-8">
                                                                <input class="form-control" name="confirm_password" type="password" placeholder="Re-Password">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3 row">
                                                            <div class="col-lg-9 col-xl-8 offset-lg-3">
                                                                <button type="submit" class="btn btn-de-primary">Change Password</button>
                                                                <button type="button" class="btn btn-de-danger">Cancel</button>
                                                            </div>
                                                        </div>   
                                                        </form>
                                                    </div><!--end card-body-->
                                                </div><!--end card-->
                                                {{-- <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Other Settings</h4>
                                                    </div><!--end card-header-->
                                                    <div class="card-body"> 
    
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="" id="Email_Notifications" checked>
                                                            <label class="form-check-label" for="Email_Notifications">
                                                                Email Notifications
                                                            </label>
                                                            <span class="form-text text-muted font-12 mt-0">Do you need them?</span>
                                                          </div>
                                                          <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="" id="API_Access">
                                                            <label class="form-check-label" for="API_Access">
                                                                API Access
                                                            </label>
                                                            <span class="form-text text-muted font-12 mt-0">Enable/Disable access</span>
                                                        </div>
                                                    </div><!--end card-body-->
                                                </div><!--end card--> --}}
                                            </div> <!-- end col -->                                                                              
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
                      <button type="button" class="btn-close text-reset p-0 m-0 align-self-center" data-bs-dismiss="offcanvas" aria-label="Close"></button>
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
@endsection