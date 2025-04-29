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
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
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
                                <a href="{{ url('/admin/cobranza/generarnotificacion') }}" class="nav-link active">
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
                                {{-- <a href="{{ url('/admin/boveda') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Egresos</p>
                                </a> --}}

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
                                @role('Administrador')
                                    <a href="{{ url('/admin/reportes/interesesmensual') }}" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Total de interes Mensual</p>
                                    </a>
                                @endrole
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- @role('Administrador')
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-buildings"></i></i>
                            <p>
                                Contabilidad
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/cuentas') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cuentas</p>
                                </a>
                                <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Plan Cuentas</p>
                                </a>
                                <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Compras</p>
                                </a>
                                <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ventas</p>
                                </a>
                                <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Libro Diario</p>
                                </a>
                                <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Libro Mayor</p>
                                </a>
                                <a href="{{ url('/admin/transacciones/ingresos') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Balance</p>
                                </a>

                            </li>
                        </ul>
                    </li>
                @endrole -->

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


