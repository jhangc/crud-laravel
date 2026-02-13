<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GRUPO CREDIPALMO</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <link rel="icon" href="{{ asset('dist/img/fdfds.ico') }}" type="image/x-icon">
  
  <style>
    body.login-page {
      background: linear-gradient(135deg, #044b6b 0%, #066a94 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .login-box {
      width: 400px;
    }
    
    .login-logo {
      text-align: center;
      margin-bottom: 30px;
      background: white;
      padding: 30px 20px;
      border-radius: 10px 10px 0 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .login-logo img {
      max-width: 280px;
      height: auto;
      margin-bottom: 15px;
    }
    
    .login-logo a {
      color: #044b6b;
      font-weight: 600;
      font-size: 18px;
      text-decoration: none;
    }
    
    .card {
      border-radius: 0 0 10px 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      border: none;
    }
    
    .card-body {
      padding: 30px;
    }
    
    .login-box-msg {
      color: #666;
      font-weight: 500;
      margin-bottom: 20px;
    }
    
    .btn-primary {
      background-color: #044b6b;
      border-color: #044b6b;
      padding: 10px;
      font-weight: 500;
    }
    
    .btn-primary:hover {
      background-color: #066a94;
      border-color: #066a94;
    }
    
    .form-control:focus {
      border-color: #044b6b;
      box-shadow: 0 0 0 0.2rem rgba(4, 75, 107, 0.25);
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img src="{{asset('logo.png')}}" alt="Grupo Credipalmo Logo">
    <br>
    <a href="{{url('/')}}"><b>SISTEMA DE ACCESO</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Ingrese sus credenciales</p>
    
      <div class="row">
        <div class="col-md-12">
        <form method="POST" action="{{ route('login') }}">
                        @csrf

            <div class="row mb-12">
                <label for="email" >{{ __('Email Address') }}</label>
                <div class="col-md-12">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <br>
            <div class="row mb-12">
                <label for="password">{{ __('Password') }}</label>

                <div class="col-md-12">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="row mb-0">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">
                        {{ __('Login') }}
                    </button>
                </div>
            </div>
        </form>
        </div>
      </div>

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
{{-- @php
    $password = bcrypt('contraseña123');
echo $password;
@endphp --}}

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script>
$(function() {
  $('form[action$="login"]').on('submit', function() {
    var btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true);
    var original = btn.html();
    btn.data('original', original);
    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Iniciando...');
  });
});
</script>
</body>
</html>
