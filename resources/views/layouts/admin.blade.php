<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Prestamos</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <!-- icono de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                {{-- <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ url('/') }}" class="nav-link">Sistema de prestamos</a>
                </li> --}}
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">



                
                @guest
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @endif

                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas"><i class="bi bi-door-closed"></i></i> Cerrar Sesión
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            @endguest

                {{-- <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </li> --}}

            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="brand-link">
                {{-- <img src="{{ asset('dist/img/logo.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
                <span class="brand-text font-weight-light">GRUPO CREDIPALMO</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                            alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                    </div>
                </div>


                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->

                        @can('usuarios.index')
                            <li class="nav-item">
                                <a href="#" class="nav-link active">
                                    <i class="nav-icon fas"><i class="bi bi-people"></i></i>
                                    <p>
                                        Usuarios
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/usuarios') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Listado de usuarios</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endcan

                        
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-credit-card"></i></i>
                                <p>
                                    Creditos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/creditos/') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de Creditos</p>
                                    </a>
                                    <a href="{{ url('/admin/creditos/aprobar') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Aprobación</p>
                                    </a>
                                    <a href="{{ url('/admin/creditos/supervisar') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Supervisar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-person-check-fill"></i></i>
                                <p>
                                    Clientes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/clientes') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Lista de clientes</p>
                                    </a>
                                    <a href="{{ url('/admin/clientes/evaluar') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Evaluar al cliente</p>
                                    </a>
                                    
                                    <a href="{{ url('/admin/clientes/ratios') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Ratios FInancieros</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-bank2"></i></i>
                                <p>
                                    Caja
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/caja/pagarcredito') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pagar Crédito</p>
                                    </a>
                                    <a href="{{ url('/admin/caja/habilitar') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Habilitar caja</p>
                                    </a>
                                    <a href="{{ url('/admin/caja/arqueo') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Arqueo de Caja</p>
                                    </a>
                                    
                                    <a href="{{ url('/admin/caja/pagares') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pagares</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-collection"></i></i>
                                <p>
                                    Cobranza
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/cobranza/carta') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Carta de Cobranza</p>
                                    </a>
                                    <a href="{{ url('/admin/cobranza/generarnotificacion') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Generar Notificación</p>
                                    </a>
                                    <a href="{{ url('/admin/cobranza/generarcompromiso') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Generar Compromiso</p>
                                    </a>
                                    
                                    <a href="{{ url('/admin/cobranza/cargarcompromiso') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cargar Compromiso</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-activity"></i></i>
                                <p>
                                    Transacciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Ingresos</p>
                                    </a>
                                    <a href="{{ url('/admin/transacciones/egresos') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Egresos</p>
                                    </a>
                                    
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-archive"></i></i>
                                <p>
                                    Reportes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/admin/reportes/clientes') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                    <a href="{{ url('/admin/reportes/prestamosactivos') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Prestamos activos</p>
                                    </a>
                                    {{-- <a href="{{ url('/admin/usuarios') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Transacciones</p>
                                    </a> --}}
                                    
                                    <a href="{{ url('/admin/reportes/prestamosvencidos') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Prestamos vencidos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>




                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <br>

            @if (($message = Session::get('mensaje')) && ($icono = Session::get('icono')))
                <script>
                    Swal.fire({
                        title: "Mensaje",
                        text: "{{ $message }}",
                        icon: "{{ $icono }}"
                    });
                </script>
            @endif


            <div class="container">
                @yield('content')
            </div>

        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            {{-- <div class="float-right d-none d-sm-inline">
                Anything you want
            </div> --}}
            <!-- Default to the left -->
            <strong>Copyright &copy; 2024 <a href="·">GRUPO CREDIPALMO</a>.</strong> Todos los derechos reservados.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>

</html>
