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
                    <li class="nav-item has-treeview {{ request()->is('admin/creditos*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/creditos*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-credit-card"></i></i>
                            <p>
                                Creditos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/creditos') }}"
                                    class="nav-link {{ request()->is('admin/creditos') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de Creditos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/admin/creditos/aprobar') }}"
                                    class="nav-link {{ request()->is('admin/creditos/aprobar') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Aprobación</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ url('/admin/creditos/aprobarreprogramados') }}"
                                    class="nav-link {{ request()->is('admin/creditos/aprobarreprogramados') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reprogramados</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ url('/inicio-operaciones') }}"
                                    class="nav-link {{ request()->is('inicio-operaciones') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Iniciar Operación</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/admin/creditos/ingresosday') }}"
                                    class="nav-link {{ request()->is('admin/creditos/ingresosday') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ingresos/Egresos de Caja Diario</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('preciosoro.index') }}" class="nav-link">
                            <i class="nav-icon fas"><i class="bi bi-gem"></i></i>
                            <p> Precios de Oro</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ request()->is('admin/credijoya/pagos/reversar*') || request()->is('admin/pagos/reversar-individual*') || request()->is('admin/pagos/reversar-grupal*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/credijoya/pagos/reversar*') || request()->is('admin/pagos/reversar-individual*') || request()->is('admin/pagos/reversar-grupal*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-arrow-counterclockwise"></i></i>
                            <p>
                                Reversiones
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/credijoya/pagos/reversar') }}"
                                    class="nav-link {{ request()->is('admin/credijoya/pagos/reversar') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reversión Credijoya</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/admin/pagos/reversar-individual') }}"
                                    class="nav-link {{ request()->is('admin/pagos/reversar-individual') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reversión Individual</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/admin/pagos/reversar-grupal') }}"
                                    class="nav-link {{ request()->is('admin/pagos/reversar-grupal') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reversión Grupal</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole



                @role('Asesor de creditos')
                    <li class="nav-item has-treeview {{ request()->is('admin/creditos*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/creditos*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-credit-card"></i></i>
                            <p>
                                Creditos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/creditos/') }}"
                                    class="nav-link {{ request()->is('admin/creditos') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de Creditos</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole


                <li class="nav-item">
                    <a href="{{ url('/admin/creditos/simulador') }}" class="nav-link">
                        <i class="nav-icon fas"><i class="bi bi-credit-card"></i></i>
                        <p> Simulador</p>
                    </a>

                </li>

                @role('Administrador|Asesor de creditos|Cajera')
                    <li class="nav-item has-treeview {{ request()->is('admin/clientes*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/clientes*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-person-check-fill"></i></i>
                            <p>
                                Clientes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/clientes') }}"
                                    class="nav-link {{ request()->is('admin/clientes') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lista de clientes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('preciosoro.index') }}" class="nav-link">
                            <i class="nav-icon fas"><i class="bi bi-gem"></i></i>
                            <p> Precios de Oro</p>
                        </a>
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
                    <li
                        class="nav-item has-treeview {{ request()->is('admin/caja*') || request()->is('admin/creditos') || request()->is('admin/gastos') || request()->is('admin/ingresos-extras') ||request()->is('admin/credijoya/devoluciones')? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('admin/caja*') || request()->is('admin/creditos') || request()->is('admin/gastos') ||  request()->is('admin/ingresos-extras')|| request()->is('admin/credijoya/devoluciones') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-bank2"></i></i>
                            <p>
                                Caja
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if ($operacionesAbiertas)
                                @if ($cajaAbierta)
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/creditos') }}"
                                            class="nav-link {{ request()->is('admin/creditos') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Listado de Creditos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/caja/pagarcredito') }}"
                                            class="nav-link {{ request()->is('admin/caja/pagarcredito') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Desembolsar Crédito</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/caja/cobrar') }}"
                                            class="nav-link {{ request()->is('admin/caja/cobrar') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Cobrar Crédito</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/credijoya/devoluciones') }}"
                                            class="nav-link {{ request()->is('admin/credijoya/devoluciones') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Delvolver Joyas</p>
                                        </a>
                                    </li>
                                @endif
                                @if (!$cajaAbierta)
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/caja/habilitar') }}"
                                            class="nav-link {{ request()->is('admin/caja/habilitar') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Habilitar Caja</p>
                                        </a>
                                    </li>
                                @endif
                                @if ($cajaAbierta)
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/caja/arqueo') }}"
                                            class="nav-link {{ request()->is('admin/caja/arqueo') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Arqueo de Caja</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/caja/pagares') }}"
                                            class="nav-link {{ request()->is('admin/caja/pagares') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pagares</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/gastos') }}"
                                            class="nav-link {{ request()->is('admin/gastos') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Gastos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/ingresos-extras') }}"
                                            class="nav-link {{ request()->is('admin/ingresos-extras') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Ingresos Extras</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('admin/depositos-cts') }}"
                                            class="nav-link {{ request()->is('admin/depositos-cts') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Abono CTS</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('admin/desembolso-cts') }}"
                                            class="nav-link {{ request()->is('admin/desembolsos-efectivo*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Desembolso Efectivo CTS</p>
                                        </a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <span class="nav-link text-warning">Operaciones no iniciadas</span>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endrole




                @role('Administrador|Asesor de creditos')
                    <li class="nav-item has-treeview {{ request()->is('admin/cobranza*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/cobranza*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-collection"></i></i>
                            <p>
                                Cobranza
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/cobranza/carta') }}"
                                    class="nav-link {{ request()->is('admin/cobranza/carta') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Carta de Cobranza</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ url('/admin/cobranza/generarnotificacion') }}"
                                    class="nav-link {{ request()->is('admin/cobranza/generarnotificacion') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Notificación</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/admin/cobranza/generarcompromiso') }}"
                                    class="nav-link {{ request()->is('admin/cobranza/generarcompromiso') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Compromiso</p>
                                </a>
                            </li> --}}
                        </ul>
                    </li>
                @endrole


                @role('Administrador')
                    <li class="nav-item has-treeview {{ request()->is('admin/boveda*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/boveda*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-activity"></i></i>
                            <p>
                                Transacciones
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/boveda') }}"
                                    class="nav-link {{ request()->is('admin/boveda') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bóveda</p>
                                </a>
                            </li>
                            {{-- Si tienes más rutas como egresos, agrégalas aquí igual --}}
                        </ul>
                    </li>
                @endrole


                {{-- @role('Administrador')
                    <li
                        class="nav-item has-treeview {{ request()->is('admin/transacciones/ingresos*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('admin/transacciones/ingresos*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-buildings"></i></i>
                            <p>
                                Sucursales
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/transacciones/ingresos') }}"
                                    class="nav-link {{ request()->is('admin/transacciones/ingresos') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de Sucursales</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole --}}


                @role('Administrador|Asesor de creditos')
                    <li class="nav-item has-treeview {{ request()->is('admin/reportes*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/reportes*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-archive"></i></i>
                            <p>
                                Reportes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Créditos Individuales --}}
                            <li class="nav-item">
                                <a href="{{ url('/admin/reportes/creditoindividual') }}"
                                    class="nav-link {{ request()->is('admin/reportes/creditoindividual') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Creditos Individuales</p>
                                </a>
                            </li>

                            {{-- Créditos Grupales --}}
                            <li class="nav-item">
                                <a href="{{ url('/admin/reportes/creditogrupal') }}"
                                    class="nav-link {{ request()->is('admin/reportes/creditogrupal') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Creditos Grupales</p>
                                </a>
                            </li>

                            {{-- Total de Interés Mensual (solo Admin) --}}
                            @role('Administrador')
                                <li class="nav-item">
                                    <a href="{{ url('/admin/reportes/interesesmensual') }}"
                                        class="nav-link {{ request()->is('admin/reportes/interesesmensual') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Total de interes Mensual</p>
                                    </a>
                                </li>
                            @endrole
                        </ul>
                    </li>

                @endrole



                @can('usuarios.index')
                    <li class="nav-item has-treeview {{ request()->is('admin/usuarios*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('admin/usuarios*') ? 'active' : '' }}">
                            <i class="nav-icon fas"><i class="bi bi-people"></i></i>
                            <p>
                                Usuarios
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/usuarios') }}"
                                    class="nav-link {{ request()->is('admin/usuarios') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de usuarios</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <li class="nav-item has-treeview {{ request()->is('admin/cts/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('admin/cts/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-piggy-bank"></i>
                        <p>
                            Cuenta CTS
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- Asignar saldo – sólo Administrador --}}
                        @role('Administrador')
                            <li class="nav-item">
                                <a href="{{ url('/admin/cts/permisos') }}"
                                    class="nav-link {{ request()->is('admin/cts/asignar-saldo') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Permisos CTS</p>
                                </a>
                            </li>
                        @endrole

                        {{-- Ver saldo – accesible para todos los roles --}}
                        <li class="nav-item">
                            <a href="{{ url('/admin/cts/ver-saldo') }}"
                                class="nav-link {{ request()->is('admin/cts/ver-saldo') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ver saldo</p>
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
