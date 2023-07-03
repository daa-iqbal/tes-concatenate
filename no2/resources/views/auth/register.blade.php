<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Sign Up | Bootstrap Based Admin Template - Material Design</title>
    <!-- Favicon-->
    <link rel="icon" href="{{url('/')}}/templates/AdminBSBMaterialDesign/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{url('/')}}/templates/AdminBSBMaterialDesign/css/style.css" rel="stylesheet">
</head>

<body class="signup-page">
    <div class="signup-box">
        <div class="logo">
            <a href="javascript:void(0);">Admin<b>BSB</b></a>
            <small>Admin BootStrap Based - Material Design</small>
        </div>
        @if($errors->any())

            <div class="alert alert-dismissible alert-danger">
                <strong>{{$errors->first()}}</strong>
            </div>
        @endif
        @error('name')
           <div class="alert alert-dismissible alert-danger">
               <strong>{{ $message }}</strong>
           </div>

        @enderror
        @error('phone')
           <div class="alert alert-dismissible alert-danger">
               <strong>{{ $message }}</strong>
           </div>

        @enderror
        @error('email')
           <div class="alert alert-dismissible alert-danger">
               <strong>{{ $message }}</strong>
           </div>

        @enderror
        @error('password')
            <div class="alert alert-dismissible alert-danger">
               <strong>{{ $message }}</strong>
           </div>
        @enderror
        <div class="card">
            <div class="body">
                <form id="sign_up" method="POST" action="{{ route('custom-register') }}">
                    @csrf
                    <div class="msg">Register a new membership</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input id="name" type="text" class="form-control  @error('name') is-invalid @enderror" name="name" placeholder="Name" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">settings_phone</i>
                        </span>
                        <div class="form-line">
                            <input id="phone" type="number" class="form-control  @error('phone') is-invalid @enderror" name="phone" placeholder="Phone" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" minlength="6" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input id="password-confirm"  type="password" class="form-control" name="password_confirmation" minlength="6" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <input type="checkbox" name="terms" id="terms" class="filled-in chk-col-pink">
                        <label for="terms">I read and agree to the <a href="javascript:void(0);">terms of usage</a>.</label>
                    </div> --}}

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">SIGN UP</button>

                    <div class="m-t-25 m-b--5 align-center">
                        <a href="{{ route('login') }}">You already have a membership?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="{{url('/')}}/templates/AdminBSBMaterialDesign/plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="{{url('/')}}/templates/AdminBSBMaterialDesign/js/admin.js"></script>
    <script src="{{url('/')}}/templates/AdminBSBMaterialDesign/js/pages/examples/sign-up.js"></script>
</body>

</html>
