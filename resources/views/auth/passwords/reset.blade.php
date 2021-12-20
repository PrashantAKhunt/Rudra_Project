<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin_asset/assets/plugins/images/favicon.png') }}">
        <title>{{ config('app.name') }}</title>
        <!-- Bootstrap Core CSS -->
        <link href="{{ asset('admin_asset/assets/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- animation CSS -->
        <link href="{{ asset('admin_asset/assets/css/animate.css') }}" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{ asset('admin_asset/assets/css/style.css') }}" rel="stylesheet">
        <!-- color CSS -->
        <link href="{{ asset('admin_asset/assets/css/colors/default.css') }}" id="theme"  rel="stylesheet">

    </head>
    <body>
        <!-- Preloader -->
        <div class="preloader">
            <div class="cssload-speeding-wheel"></div>
        </div>
        <section id="wrapper" class="login-register">
            <div class="login-box">
                <div class="white-box">
                    <form method="POST" class="form-horizontal form-material" action="{{ route('password.update') }}">
                   
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <h3 class="box-title m-b-20">{{ __('Reset Password') }}</h3>
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input id="email" placeholder="Registered Email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input id="password" placeholder="New Password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input placeholder="Confirm Password" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">{{ __('Reset Password') }}</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </section >
        <!-- jQuery -->
        <script src="{{ asset('admin_asset/assets/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="{{ asset('admin_asset/assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <!-- Menu Plugin JavaScript -->
        <script src="{{ asset('admin_asset/assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>

        <!--slimscroll JavaScript -->
        <script src="{{ asset('admin_asset/assets/js/jquery.slimscroll.js') }}"></script>
        <!--Wave Effects -->
        <script src="{{ asset('admin_asset/assets/js/waves.js') }}"></script>
        <!-- Custom Theme JavaScript -->
        <script src="{{ asset('admin_asset/assets/js/custom.min.js') }}"></script>
        <!--Style Switcher -->
        <script src="{{ asset('admin_asset/assets/plugins/bower_components/styleswitcher/jQuery.style.switcher.js') }}"></script>
    </body>
</html>

