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

    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">

    <!-- icono de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Añade esto en el <head> de tu HTML o justo antes del cierre de </body>, dependiendo de tus necesidades -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet"> -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">


    <link rel="icon" href="{{ asset('dist/img/fdfds.ico') }}" type="image/x-icon">


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
                        <a class="nav-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas"><i class="bi bi-door-closed"></i></i> Cerrar Sesión
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>

                @endguest



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
                        data-accordion="true">

                        <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->

                        @role('Administrador')
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
                                        <a href="{{ url('/inicio-operaciones') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Inciar Operación</p>
                                        </a>
                                        <a href="{{ url('/admin/creditos/ingresosday') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Ingresos/Egresos de Caja Diario</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endrole


                        @role('Asesor de creditos')
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
                                    </li>
                                </ul>
                            </li>
                        @endrole

                        <li class="nav-item">
                            <a href="{{ url('/admin/creditos/simulador') }}" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-credit-card"></i></i>
                                <p> Simulador</p>
                            </a>

                        </li>

                        @role('Administrador|Asesor de creditos')
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
                                        <!-- <a href="{{ url('/admin/clientes/evaluar') }}" class="nav-link active">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Evaluar al cliente</p>
                                                </a>

                                                <a href="{{ url('/admin/clientes/ratios') }}" class="nav-link active">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Ratios FInancieros</p>
                                                </a> -->
                                    </li>
                                </ul>
                            </li>
                        @endrole
                        <!-- validad operaciones -->
                        @php
                            $sucursalId = Auth::user()->sucursal_id;
                            $usuarioId = Auth::user()->id;
                            $operacionesAbiertas = App\Models\InicioOperaciones::where('sucursal_id', $sucursalId)
                                                                                ->where('permiso_abierto', true)
                                                                                ->exists();
                            $cajaAbierta = App\Models\CajaTransaccion::where('sucursal_id', $sucursalId)
                                                                ->where('user_id', $usuarioId)
                                                                ->whereNull('hora_cierre')
                                                                ->exists();
                        @endphp

                        @role('Administrador|Cajera')
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
                                         @if ($operacionesAbiertas)
                                        @if ($cajaAbierta) 
                                        <a href="{{ url('/admin/caja/pagarcredito') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Desembolsar Crédito</p>
                                        </a>
                                        <a href="{{ url('/admin/caja/cobrar') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Cobrar Crédito</p>
                                        </a>
                                         @endif
                                            @if (!$cajaAbierta) 
                                        <a href="{{ url('/admin/caja/habilitar') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Habilitar caja</p>
                                        </a>
                                         @endif
                                            @if ($cajaAbierta) 
                                        <a href="{{ url('/admin/caja/arqueo') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Arqueo de Caja</p>
                                        </a>
                                         @endif
                                            @if ($cajaAbierta) 
                                        <a href="{{ url('/admin/caja/pagares') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pagares</p>
                                        </a>
                                        @endif
                                        @if ($cajaAbierta) 
                                        <a href="{{ url('/admin/gastos') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Gastos</p>
                                        </a>
                                        @endif
                                        @if ($cajaAbierta) 
                                        <a href="{{ url('/admin/ingresos-extras') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Ingresos</p>
                                        </a>
                                        @endif
                                        @else 
                                            <span class="nav-link text-warning">Operaciones no iniciadas</span>
                                        @endif 
                                    </li>
                                </ul>
                            </li>
                        @endrole


                        @role('Administrador|Asesor de creditos')
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
                                        <a href="{{ url('/admin/cobranza/generarnotificacion') }}"
                                            class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Notificación</p>
                                        </a>
                                        <a href="{{ url('/admin/cobranza/generarcompromiso') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Compromiso</p>
                                        </a>

                                    </li>
                                </ul>
                            </li>
                        @endrole

                        @role('Administrador')
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
                                        <a href="{{ url('/admin/boveda') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>boveda</p>
                                        </a>
                                        {{--<a href="{{ url('/admin/boveda') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Egresos</p>
                                        </a>--}}

                                    </li>
                                </ul>
                            </li>
                        @endrole

                        @role('Administrador')
                            <li class="nav-item">
                                <a href="#" class="nav-link active">
                                    <i class="nav-icon fas"><i class="bi bi-buildings"></i></i>
                                    <p>
                                        Sucursales
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Listado de Sucursales</p>
                                        </a>

                                    </li>
                                </ul>
                            </li>
                        @endrole

                        @role('Administrador|Asesor de creditos')
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
                                        <a href="{{ url('/admin/reportes/creditoindividual') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Creditos Individuales</p>
                                        </a>
                                        <a href="{{ url('/admin/reportes/creditogrupal') }}" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Creditos Grupales</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endrole

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


            <div class="plantilla">
                @yield('content')
            </div>

        </div>
        <!-- /.content-wrapper -->
        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            {{-- <div class="float-right d-none d-sm-inline">
                Anything you want
            </div> --}}
            <!-- Default to the left -->
            <strong>Copyright &copy; 2024 <a href="·">GRUPO CREDIPALMO</a>.</strong> Todos los derechos
            reservados.
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

    <script src="{{ asset('dist/js/main.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

</body>

</html>
