<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\credito;
use App\Models\cliente;
use App\Models\CreditoCliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Cronograma;
use App\Models\Caja;
use App\Models\CajaTransaccion;
use App\Models\InicioOperaciones;
use App\Models\Ingreso;
use App\Models\Gasto;
use App\Models\IngresoExtra;
use App\Models\CorrelativoCredito;



class creditoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Administrador')) {
            // Si es administrador o cajera, obtener todos los créditos activos
            $creditos = credito::with('clientes')->where('activo', 1)->get();
        } else {
            // Si no es administrador, obtener solo los créditos registrados por el usuario
            $creditos = credito::with('clientes')->where('activo', 1)->where('user_id', $user->id)->get();
        }

        // Pasar los créditos a la vista
        return view('admin.creditos.index', ['creditos' => $creditos]);
    }


    public function viewSimulador()
    {

        return view('admin.creditos.simulador');
    }



    public function comercio()
    {
        return view('admin.creditos.comercio');
    }

    public function produccion()
    {
        return view('admin.creditos.produccion');
    }

    public function servicio()
    {
        return view('admin.creditos.servicio');
    }

    public function grupal()
    {
        return view('admin.creditos.grupal');
    }

    public function agricola()
    {
        return view('admin.creditos.agricola');
    }

    public function getdescripciones(Request $request)
    {
        $descripciones = \App\Models\MargenVenta::where('actividad_economica', $request->opcion)->get();

        return response()->json([
            'state' => '0',
            'mensaje' => 'Prestamo creado con exito',
            'data' => $descripciones
        ])->setStatusCode(200);
    }
    public function viewaprobar()
    {
        // Obtener solo los clientes activos (activo = 1)
        $creditos = credito::with('clientes')
            ->where('activo', 1)
            ->where('estado', 'revisado')
            ->get();
        return view('admin.creditos.aprobar', ['creditos' => $creditos]);
    }

    public function proyecciones(Request $request, $id)
    {

        $modulo = $request->query('modulo'); // Obtener el parámetro 'modulo' de la URL

        $prestamo = credito::find($id);
        $proyecciones = \App\Models\ProyeccionesVentas::where('id_prestamo', $id)->get();
        $deudas = \App\Models\DeudasFinancieras::where('prestamo_id', $id)->get();
        $gastosOperativos = \App\Models\GastosOperativos::where('id_prestamo', $id)->get();
        $inventario = \App\Models\Inventario::where('id_prestamo', $id)->get();
        $boletas = \App\Models\Boleta::where('id_prestamo', $id)->get();
        $gastosProducir = \App\Models\GastosProducir::where('id_prestamo', $id)->get();
        $garantias = \App\Models\Garantia::where('id_prestamo', $id)->get();
        $gastosfamiliares = \App\Models\GastosFamiliares::where('id_prestamo', $id)->get();
        $activos = \App\Models\Activos::where('prestamo_id', $id)->first();
        $ventasdiarias = \App\Models\VentasDiarias::where('prestamo_id', $id)->get();
        $cuotas = Cronograma::where('id_prestamo', $id)->first();
        $cuotastodas = Cronograma::where('id_prestamo', $id)->get();
        $inventarioterminado = \App\Models\Inventario::where('id_prestamo', $id)
            ->where('tipo_inventario', 1)
            ->get();

        $inventarioproceso = \App\Models\Inventario::where('id_prestamo', $id)
            ->where('tipo_inventario', 2)
            ->get();

        $inventariomateriales = \App\Models\Inventario::where('id_prestamo', $id)
            ->where('tipo_inventario', 3)
            ->get();

        $tipo_producto_agricola = \App\Models\TipoProducto::where('id_prestamo', $id)->get();
        $venta_mensual = \App\Models\VentasMensuales::where('id_prestamo', $id)->get();
        $productos_agricolas = \App\Models\ProductoAgricola::where('id_prestamo', $id)->first();

        $descripcion = $prestamo->descripcion_negocio;
        $margenmanual = \App\Models\MargenVenta::where('giro_economico', $descripcion)->first();

        $cliente = $prestamo->clientes->first();
        $responsable = auth()->user();

        // $listaclientes = $prestamo->clientes->get();

        $tipo = $prestamo->tipo;

        $comentarioasesor = $prestamo->comentario_asesor;
        $comentarioadministrador = $prestamo->comentario_administrador;

        // Calcular Totales
        $factorsemana = 15 / 7;
        $factormes = $factorsemana * 2;

        $totalprestamo = $prestamo->monto_total;
        $cuotaprestamo = $cuotas->monto;

        $estado = $prestamo->estado;

        if ($prestamo->producto != "grupal") {
            switch ($tipo) {
                case 'comercio':
                    $totalVentas = round((($ventasdiarias->sum('promedio')) * $factormes), 2);

                    // Inicializar variables
                    $pesoTotal = 0;
                    $sumaPonderadaRelacion = 0;
                    //$margenventas = ($margenmanual->margen_utilidad) * 100;

                    if ($margenmanual !== null) {
                        $margenventas = ($margenmanual->margen_utilidad) * 100;
                    } else {
                        $margenventas = 0;
                    }

                    // Recorrer las proyecciones para calcular el monto total de ventas y la relación de compra-venta promedio ponderada
                    foreach ($proyecciones as $proyeccion) {
                        $montoVenta = $totalVentas * ($proyeccion->proporcion_ventas / 100);
                        // Calcular la relación de compra-venta
                        $relacionCompraVenta = $proyeccion->precio_venta > 0 ? $proyeccion->precio_compra / $proyeccion->precio_venta : 0;
                        $sumaPonderadaRelacion += $relacionCompraVenta * $montoVenta;
                        $pesoTotal += $montoVenta;
                    }
                    // Calcular la relación de compra-venta promedio ponderada
                    $relacionCompraVentaPromedio = $pesoTotal > 0 ? $sumaPonderadaRelacion / $pesoTotal : 0;
                    // Calcular el costo total de ventas
                    $totalCompras = round($totalVentas * $relacionCompraVentaPromedio, 2);

                    if ($totalVentas != 0) {
                        $margen = round((1 - ($totalCompras / $totalVentas)), 2);
                    } else {
                        $margen = 0; // O cualquier otro valor que consideres apropiado cuando $totalVentas es 0
                    }
                    $proporcion_ventas = $proyecciones->sum('proporcion_ventas');
                    // Cálculos
                    $utilidadBruta = $totalVentas - $totalCompras;
                    $totalGastosOperativos = $gastosOperativos->sum(fn ($gasto) => $gasto->precio_unitario * $gasto->cantidad);
                    $total_venta_credito = (($prestamo->porcentaje_credito) * $totalVentas) / 100;

                    $total_inventario = $inventario->sum(fn ($item) => $item->precio_unitario * $item->cantidad);

                    $activo_corriente = $activos->saldo_en_caja_bancos + $activos->cuentas_por_cobrar + $activos->adelanto_a_proveedores + $total_inventario;
                    $activofijo = $garantias->sum('valor_mercado');
                    $activo = $activo_corriente + $activofijo;
                    $pasivo = $deudas->sum('saldo_capital');
                    $totalcuotadeuda = $deudas->sum('cuota');

                    $utilidadOperativa = $utilidadBruta - $totalGastosOperativos;
                    $saldo_disponible_negocio = $utilidadOperativa - $totalcuotadeuda;
                    $totalgastosfamiliares = round(($gastosfamiliares->sum(fn ($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                    $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                    $rentabilidad_ventas = $totalVentas != 0 ? round((($saldo_disponible_negocio / $totalVentas) * 100), 2) : 0;
                    $rotacion_inventario = $total_inventario != 0 ? round(($totalCompras / $total_inventario), 2) : 0;
                    $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : 0;
                    $roa = $activo != 0 ? round(($saldo_disponible_negocio / $activo) * 100, 2) : 0;
                    $capital_trabajo = $activo_corriente - $deudas->sum('saldo_capital');

                    $totalCuotasCreditos = $deudas->sum('cuota');
                    $totalPrestamos = $prestamo->monto_total;
                    $patrimonio = $activo - $pasivo;

                    $roe = $patrimonio != 0 ? round(($saldo_disponible_negocio / $patrimonio) * 100, 2) : 0;

                    $utilidadNeta = $utilidadBruta - $totalCuotasCreditos;
                    $cuotaEndeudamiento = $utilidadNeta - $totalgastosfamiliares;
                    $solvencia = $patrimonio != 0 ? round(($pasivo / $patrimonio), 2) : 0;
                    $indice_endeudamiento = $activo != 0 ? round(($pasivo / $activo) * 100, 2) : 0;

                    $rentabilidad = $totalVentas != 0 ? $utilidadNeta / $totalVentas : 0;
                    $indicadorInventario = $inventario->sum('precio_unitario') != 0 ? $totalPrestamos / $inventario->sum('precio_unitario') : 0;
                    $capitalTrabajo = 20000; // Asumiendo un valor para capital de trabajo
                    $indicadorCapitalTrabajo = $capitalTrabajo != 0 ? $totalPrestamos / $capitalTrabajo : 0;

                    $Endeudamientopatrimonial = $patrimonio != 0 ? round((($pasivo + $totalPrestamos) / $patrimonio), 2) : 0;
                    $cuotaexcedente = $saldo_final != 0 ? round(($cuotaprestamo / $saldo_final), 2) : 0;

                    return view('admin.creditos.proyeccionesmargen', compact(
                        'prestamo',
                        'cliente',
                        'responsable',
                        'utilidadBruta',
                        'utilidadOperativa',
                        'utilidadNeta',
                        'cuotaEndeudamiento',
                        'solvencia',
                        'rentabilidad',
                        'indicadorInventario',
                        'indicadorCapitalTrabajo',
                        'proyecciones',
                        'deudas',
                        'gastosOperativos',
                        'inventario',
                        'boletas',
                        'gastosProducir',
                        'totalVentas',
                        'totalCompras',
                        'margen',
                        'proporcion_ventas',
                        'totalGastosOperativos',
                        'total_venta_credito',
                        'total_inventario',
                        'activo_corriente',
                        'garantias',
                        'patrimonio',
                        'pasivo',
                        'activo',
                        'saldo_disponible_negocio',
                        'saldo_final',
                        'rentabilidad_ventas',
                        'rotacion_inventario',
                        'liquidez',
                        'roa',
                        'capital_trabajo',
                        'roe',
                        'solvencia',
                        'indice_endeudamiento',
                        'activos',
                        'totalgastosfamiliares',
                        'totalcuotadeuda',
                        'totalprestamo',
                        'cuotaprestamo',
                        'margenventas',
                        'Endeudamientopatrimonial',
                        'cuotaexcedente',
                        'comentarioasesor',
                        'comentarioadministrador',
                        'modulo',
                        'estado'

                    ));
                case 'servicio':
                    if ($prestamo->producto == "microempresa") {
                        $totalVentas = round((($ventasdiarias->sum('promedio')) * $factormes), 2);

                        $totalGastosOperativos = round(($gastosOperativos->sum(fn ($gasto) => $gasto->precio_unitario * $gasto->cantidad)), 2);
                        $totalCompras = $totalGastosOperativos;

                        $margensoles = $totalVentas - $totalCompras;
                        $margenporcentaje = $totalVentas != 0 ? round(((1 - ($totalCompras / $totalVentas)) * 100), 2) : 0;

                        $total_venta_credito = (($prestamo->porcentaje_credito) * $totalVentas) / 100;

                        $saldo_en_caja_bancos = $activos->saldo_en_caja_bancos;
                        $cuenta_cobrar = $activos->cuentas_por_cobrar;
                        $adelanto_proveedores = $activos->adelanto_a_proveedores;

                        if ($inventario->isEmpty()) {
                            $totalinventario = 0;
                        } else {
                            $totalinventario = round(($inventario->sum(fn ($inven) => $inven->precio_unitario * $inven->cantidad)), 2);
                        }

                        $activo_corriente = $saldo_en_caja_bancos + $cuenta_cobrar + $adelanto_proveedores + $totalinventario;
                        $totalgarantia = $garantias->sum('valor_mercado');
                        $activofijo = $totalgarantia;

                        $totalactivo = $activo_corriente + $activofijo;
                        $totaldeudas = $deudas->sum('saldo_capital');
                        $totalcuotadeuda = $deudas->sum('cuota');
                        $pasivo = $totaldeudas;
                        $patrimonioneto = $totalactivo - $pasivo;

                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn ($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                        $totalgastosfinancieros = round(($deudas->sum('saldo_capital')), 2);

                        $saldo_disponible_negocio = $margensoles - $totalcuotadeuda;
                        $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                        //$margenventas = ($margenmanual->margen_utilidad) * 100;

                        if ($margenmanual !== null) {
                            $margenventas = ($margenmanual->margen_utilidad) * 100;
                        } else {
                            $margenventas = 0;
                        }

                        $rentabilidad_ventas = $totalVentas != 0 ? round(($saldo_disponible_negocio / $totalVentas) * 100, 2) : 0;
                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : 0;
                        $roe = $patrimonioneto != 0 ? round(($saldo_disponible_negocio / $patrimonioneto) * 100, 2) : 0;
                        $solvencia = $patrimonioneto != 0 ? round(($pasivo / $patrimonioneto), 2) : 0;
                        $roa = $totalactivo != 0 ? round(($saldo_disponible_negocio / $totalactivo) * 100, 2) : 0;
                        $capital_trabajo = $activo_corriente - $pasivo;
                        $indice_endeudamiento = $totalactivo != 0 ? round(($pasivo / $totalactivo) * 100, 2) : 0;
                        $cuotaexcedente = $saldo_final != 0 ? round(($cuotaprestamo / $saldo_final), 2) : 0;

                        return view('admin.creditos.evaluacionserviciomicroempresa', compact(
                            'prestamo',
                            'cliente',
                            'totalprestamo',
                            'cuotaprestamo',
                            'responsable',
                            'totalVentas',
                            'totalCompras',
                            'margensoles',
                            'margenporcentaje',
                            'total_venta_credito',
                            'saldo_en_caja_bancos',
                            'cuenta_cobrar',
                            'adelanto_proveedores',
                            'totalinventario',
                            'totalgarantia',
                            'activofijo',
                            'activo_corriente',
                            'totalactivo',
                            'totaldeudas',
                            'pasivo',
                            'patrimonioneto',
                            'totalgastosfamiliares',
                            'gastosfamiliares',
                            'totalgastosfinancieros',
                            'saldo_disponible_negocio',
                            'saldo_final',
                            'margenventas',
                            'margenmanual',
                            'rentabilidad_ventas',
                            'liquidez',
                            'roa',
                            'capital_trabajo',
                            'roe',
                            'solvencia',
                            'indice_endeudamiento',
                            'totalcuotadeuda',
                            'cuotaexcedente',
                            'comentarioasesor',
                            'comentarioadministrador',
                            'modulo',
                            'estado'
                        ));
                    } else {
                        $totalVentas = round(($boletas->sum('total_boleta')));

                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn ($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                        $totalCompras = $totalgastosfamiliares;
                        //utilidad bruta
                        $margensoles = $totalVentas - $totalCompras;
                        $margenporcentaje = $totalVentas != 0 ? round(((1 - ($totalCompras / $totalVentas)) * 100), 2) : 0;

                        $activo_corriente = 0;
                        $totalgarantia = $garantias->sum('valor_mercado');
                        $activofijo = $totalgarantia;

                        $totalactivo = $activo_corriente + $activofijo;
                        $totaldeudas = $deudas->sum('saldo_capital');
                        $pasivo = $totaldeudas;
                        $totalcuotadeuda = $deudas->sum('cuota');
                        $patrimonioneto = $totalactivo;

                        $totalgastosfinancieros = round(($deudas->sum('saldo_capital')), 2);

                        $saldo_final = $totalVentas - $totalcuotadeuda - $totalgastosfamiliares;

                        //$margenventas = $margenmanual->margen_utilidad;

                        if ($margenmanual !== null) {
                            $margenventas = $margenmanual->margen_utilidad;
                        } else {
                            $margenventas = 0;
                        }

                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : 0;

                        $solvencia = $patrimonioneto != 0 ? round(($pasivo / $patrimonioneto), 2) : 0;

                        $capital_trabajo = $activo_corriente - $pasivo;
                        $indice_endeudamiento = $totalactivo != 0 ? round(($pasivo / $totalactivo), 2) : 0;

                        $cuotaendeudamiento = $saldo_final - $totalcuotadeuda;
                        $cuotaexcedente = $saldo_final != 0 ? round(($cuotaprestamo / $saldo_final), 2) : 0;

                        return view('admin.creditos.evaluacionservicioconsumo', compact(
                            'prestamo',
                            'cliente',
                            'totalprestamo',
                            'cuotaprestamo',
                            'responsable',
                            'totalVentas',
                            'totalCompras',
                            'margensoles',
                            'margenporcentaje',
                            'totalgarantia',
                            'activofijo',
                            'activo_corriente',
                            'totalactivo',
                            'totaldeudas',
                            'pasivo',
                            'patrimonioneto',
                            'totalgastosfamiliares',
                            'gastosfamiliares',
                            'totalgastosfinancieros',
                            'saldo_final',
                            'margenventas',
                            'margenmanual',
                            'liquidez',
                            'capital_trabajo',
                            'solvencia',
                            'indice_endeudamiento',
                            'cuotaendeudamiento',
                            'totalcuotadeuda',
                            'cuotaexcedente',
                            'comentarioasesor',
                            'comentarioadministrador',
                            'modulo',
                            'estado'
                        ));
                    }
                case 'produccion':
                    if ($prestamo->producto == "microempresa") {
                        $totalVentas = round((($ventasdiarias->sum('promedio')) * $factormes), 2);

                        // Inicializar variables
                        $pesoTotal = 0;
                        $sumaPonderadaRelacion = 0;

                        // Recorrer las proyecciones para calcular el monto total de ventas y la relación de compra-venta promedio ponderada
                        foreach ($proyecciones as $proyeccion) {
                            $montoVenta = $totalVentas * ($proyeccion->proporcion_ventas / 100);
                            // Calcular la relación de compra-venta
                            $relacionCompraVenta = $proyeccion->precio_venta > 0 ? $proyeccion->precio_compra / $proyeccion->precio_venta : 0;
                            $sumaPonderadaRelacion += $relacionCompraVenta * $montoVenta;
                            $pesoTotal += $montoVenta;
                        }
                        // Calcular la relación de compra-venta promedio ponderada
                        $relacionCompraVentaPromedio = $pesoTotal > 0 ? $sumaPonderadaRelacion / $pesoTotal : 0;
                        // Calcular el costo total de ventas
                        $totalCompras = round($totalVentas * $relacionCompraVentaPromedio, 2);

                        if ($totalVentas != 0) {
                            $margenporcentaje = round(((1 - ($totalCompras / $totalVentas)) * 100), 2);
                        } else {
                            $margenporcentaje = 0; // O cualquier otro valor que consideres apropiado cuando $totalVentas es 0
                        }
                        $proporcion_ventas = $proyecciones->sum('proporcion_ventas');
                        // Cálculos
                        $utilidadBruta = $totalVentas - $totalCompras;
                        $totalGastosOperativos = $gastosOperativos->sum(fn ($gasto) => $gasto->precio_unitario * $gasto->cantidad);
                        $total_venta_credito = (($prestamo->porcentaje_credito) * $totalVentas) / 100;

                        $totalinventarioterminado = $inventarioterminado->sum(fn ($item) => $item->precio_unitario * $item->cantidad);
                        $totalinventarioproceso = $inventarioproceso->sum(fn ($item) => $item->precio_unitario * $item->cantidad);

                        $totalinventariomateriales = $inventariomateriales !== null ? $inventariomateriales->sum(fn ($item) => $item->precio_unitario * $item->cantidad) : 0;

                        $total_inventario = $totalinventarioterminado + $totalinventarioproceso + $totalinventariomateriales;

                        $saldo_en_caja_bancos = $activos->saldo_en_caja_bancos;
                        $cuenta_cobrar = $activos->cuentas_por_cobrar;
                        $adelanto_proveedores = $activos->adelanto_a_proveedores;

                        $activo_corriente = $saldo_en_caja_bancos + $cuenta_cobrar + $adelanto_proveedores + $total_inventario;

                        $activofijo = $garantias->sum('valor_mercado');
                        $activo = $activo_corriente + $activofijo;
                        $pasivo = $deudas->sum('saldo_capital');
                        $patrimonio = $activo - $pasivo;

                        $totalcuotadeuda = $deudas->sum('cuota');

                        $utilidadOperativa = $utilidadBruta - $totalGastosOperativos;
                        $saldo_disponible_negocio = $utilidadOperativa - $totalcuotadeuda;
                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn ($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                        $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                        $rentabilidad_ventas = $totalVentas != 0 ? round((($saldo_disponible_negocio / $totalVentas) * 100), 2) : 0;
                        $rotacion_inventario = $total_inventario != 0 ? round(($totalCompras / $total_inventario), 2) : 0;
                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : 0;
                        $roa = $activo != 0 ? round(($saldo_disponible_negocio / $activo) * 100, 2) : 0;
                        $capital_trabajo = $activo_corriente - $deudas->sum('saldo_capital');

                        $totalCuotasCreditos = $deudas->sum('cuota');
                        $totalPrestamos = $prestamo->monto_total;

                        //$margenventas = ($margenmanual->margen_utilidad) * 100;

                        if ($margenmanual !== null) {
                            $margenventas = ($margenmanual->margen_utilidad) * 100;
                        } else {
                            $margenventas = 0;
                        }

                        $roe = $patrimonio != 0 ? round(($saldo_disponible_negocio / $patrimonio) * 100, 2) : 0;

                        $utilidadNeta = $utilidadBruta - $totalCuotasCreditos;
                        $cuotaEndeudamiento = $utilidadNeta - $totalgastosfamiliares;
                        $solvencia = $patrimonio != 0 ? round(($pasivo / $patrimonio), 2) : 0;
                        $indice_endeudamiento = $activo != 0 ? round(($pasivo / $activo) * 100, 2) : 0;

                        $rentabilidad = $totalVentas != 0 ? $utilidadNeta / $totalVentas : 0;
                        $indicadorInventario = $inventario->sum('precio_unitario') != 0 ? $totalPrestamos / $inventario->sum('precio_unitario') : 0;
                        $capitalTrabajo = 20000; // Asumiendo un valor para capital de trabajo
                        $indicadorCapitalTrabajo = $capitalTrabajo != 0 ? $totalPrestamos / $capitalTrabajo : 0;

                        $Endeudamientopatrimonial = $patrimonio != 0 ? round((($pasivo + $totalPrestamos) / $patrimonio), 2) : 0;
                        $cuotaexcedente = $saldo_final != 0 ? round(($cuotaprestamo / $saldo_final), 2) : 0;

                        return view('admin.creditos.evaluacionproduccionempresa', compact(
                            'prestamo',
                            'cliente',
                            'responsable',
                            'utilidadBruta',
                            'utilidadOperativa',
                            'totalVentas',
                            'totalCompras',
                            'margenporcentaje',
                            'proporcion_ventas',
                            'totalGastosOperativos',
                            'total_venta_credito',
                            'total_inventario',
                            'totalinventarioterminado',
                            'totalinventarioproceso',
                            'totalinventariomateriales',
                            'saldo_en_caja_bancos',
                            'cuenta_cobrar',
                            'adelanto_proveedores',
                            'activo_corriente',
                            'activofijo',
                            'patrimonio',
                            'pasivo',
                            'activo',
                            'saldo_disponible_negocio',
                            'saldo_final',
                            'rentabilidad_ventas',
                            'rotacion_inventario',
                            'liquidez',
                            'roa',
                            'capital_trabajo',
                            'roe',
                            'solvencia',
                            'indice_endeudamiento',
                            'activos',
                            'totalgastosfamiliares',
                            'totalcuotadeuda',
                            'totalprestamo',
                            'cuotaprestamo',
                            'margenventas',
                            'Endeudamientopatrimonial',
                            'cuotaexcedente',
                            'comentarioasesor',
                            'comentarioadministrador',
                            'modulo',
                            'estado'
                        ));
                    } else {

                        if ($productos_agricolas->ciclo_productivo_meses) {
                            // Asegúrate de que el ciclo productivo en meses no sea cero para evitar división por cero
                            if ($productos_agricolas->ciclo_productivo_meses != 0) {
                                $cicloproductivo = 12 / $productos_agricolas->ciclo_productivo_meses;
                            } else {
                                // Maneja el caso donde ciclo_productivo_meses es cero
                                $cicloproductivo = 1; // O cualquier valor o lógica que tenga sentido para tu caso
                            }
                        }

                        $cantidad_cultivo = $cicloproductivo*$productos_agricolas->cantidad_cultivar*$productos_agricolas->rendimiento_unidad_siembra;

                        //$totalVentas = round((($ventasdiarias->sum('promedio')) * $factormes), 2);

                        // Inicializar variables
                        $totalVentas = 0;
                        //$sumaPonderadaRelacion = 0;

                        // Recorrer las proyecciones para calcular el monto total de ventas y la relación de compra-venta promedio ponderada
                        foreach ($tipo_producto_agricola  as $producto) {
                            $montoVenta = $cantidad_cultivo*$producto->precio* ($producto->porcentaje / 100);
                            $totalVentas += $montoVenta;
                        }


                        $totalCompras=0;
                        foreach ($gastosOperativos as $gastoOperativo) {
                            $gasto = $cicloproductivo*$gastoOperativo->precio_unitario * $gastoOperativo->cantidad;
                            $totalCompras += $gasto;
                        }

                        //dd($prestamo->recurrencia);


                        switch ($prestamo->recurrencia) {
                            case 'catorcenal':
                            case 'quincenal':
                                $divisor = 24;
                                break;
                            case 'veinteochenal':
                            case 'mensual':
                                $divisor = 12;
                                break;
                            case 'semestral':
                                $divisor = 2;
                                break;
                            case 'anual':
                                $divisor = 1;
                                break;
                            default:
                                $divisor = 12;
                                break;
                        }
                        
                        $totalVentas = $totalVentas / $divisor;
                        $totalCompras = $totalCompras / $divisor;

                        // Imprimir el valor de totalVentas con dd
                        //dd($totalCompras);

                        if ($totalVentas != 0) {
                            $margenporcentaje = round(((1 - ($totalCompras / $totalVentas)) * 100), 2);
                        } else {
                            $margenporcentaje = 0; // O cualquier otro valor que consideres apropiado cuando $totalVentas es 0
                        }



                        //dd($margenporcentaje);
                        $proporcion_ventas = $venta_mensual->sum('porcentaje');
                        // Cálculos
                        $utilidadBruta = $totalVentas - $totalCompras;
                        $totalGastosOperativos = $totalCompras;

                         
                        $totalinventarioterminado = $inventarioterminado->sum(fn ($item) => $item->precio_unitario * $item->cantidad);
                        $totalinventarioproceso = $inventarioproceso->sum(fn ($item) => $item->precio_unitario * $item->cantidad);

                        $totalinventariomateriales = $inventariomateriales !== null ? $inventariomateriales->sum(fn ($item) => $item->precio_unitario * $item->cantidad) : 0;

                        $total_inventario = $totalinventarioterminado + $totalinventarioproceso + $totalinventariomateriales;

                        $saldo_en_caja_bancos = $activos->saldo_en_caja_bancos;
                        $cuenta_cobrar = $activos->cuentas_por_cobrar;
                        $adelanto_proveedores = $activos->adelanto_a_proveedores;

                        $activo_corriente = $saldo_en_caja_bancos + $cuenta_cobrar + $adelanto_proveedores + $total_inventario;

                        $activofijo = $garantias->sum('valor_mercado');
                        $activo = $activo_corriente + $activofijo;
                        $pasivo = $deudas->sum('saldo_capital');
                        $patrimonio = $activo - $pasivo;

                        $totalcuotadeuda = $deudas->sum('cuota');

                        $utilidadOperativa = $utilidadBruta - $totalGastosOperativos;
                        $saldo_disponible_negocio = $utilidadOperativa - $totalcuotadeuda;
                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn ($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                        $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                        $rentabilidad_ventas = $totalVentas != 0 ? round((($saldo_disponible_negocio / $totalVentas) * 100), 2) : 0;
                        $rotacion_inventario = $total_inventario != 0 ? round(($totalCompras / $total_inventario), 2) : 0;
                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : 0;
                        $roa = $activo != 0 ? round(($saldo_disponible_negocio / $activo) * 100, 2) : 0;
                        $capital_trabajo = $activo_corriente - $deudas->sum('saldo_capital');

                        $totalCuotasCreditos = $deudas->sum('cuota');
                        $totalPrestamos = $prestamo->monto_total;

                        if ($margenmanual !== null) {
                            $margenventas = ($margenmanual->margen_utilidad) * 100;
                        } else {
                            $margenventas = 0;
                        }
                        //$margenventas = ($margenmanual->margen_utilidad) * 100;

                        $roe = $patrimonio != 0 ? round(($saldo_disponible_negocio / $patrimonio) * 100, 2) : 0;

                        $utilidadNeta = $utilidadBruta - $totalCuotasCreditos;
                        $cuotaEndeudamiento = $utilidadNeta - $totalgastosfamiliares;
                        $solvencia = $patrimonio != 0 ? round(($pasivo / $patrimonio), 2) : 0;
                        $indice_endeudamiento = $activo != 0 ? round(($pasivo / $activo) * 100, 2) : 0;

                        $rentabilidad = $totalVentas != 0 ? $utilidadNeta / $totalVentas : 0;
                        // $indicadorInventario = $inventario->sum('precio_unitario') != 0 ? $totalPrestamos / $inventario->sum('precio_unitario') : 0;
                        // $capitalTrabajo = 20000; // Asumiendo un valor para capital de trabajo
                        // $indicadorCapitalTrabajo = $capitalTrabajo != 0 ? $totalPrestamos / $capitalTrabajo : 0;

                        $Endeudamientopatrimonial = $patrimonio != 0 ? round((($pasivo + $totalPrestamos) / $patrimonio), 2) : 0;
                        $cuotaexcedente = $saldo_final != 0 ? round(($cuotaprestamo / $saldo_final), 2) : 0;

                        return view('admin.creditos.evaluacionproduccionagricola', compact(
                            'prestamo',
                            'cliente',
                            'responsable',
                            'utilidadBruta',
                            'utilidadOperativa',
                            'totalVentas',
                            'totalCompras',
                            'margenporcentaje',
                            'proporcion_ventas',
                            'totalGastosOperativos',
                            'total_inventario',
                            'totalinventarioterminado',
                            'totalinventarioproceso',
                            'totalinventariomateriales',
                            'saldo_en_caja_bancos',
                            'cuenta_cobrar',
                            'adelanto_proveedores',
                            'activo_corriente',
                            'activofijo',
                            'patrimonio',
                            'pasivo',
                            'activo',
                            'saldo_disponible_negocio',
                            'saldo_final',
                            'rentabilidad_ventas',
                            'rotacion_inventario',
                            'liquidez',
                            'roa',
                            'capital_trabajo',
                            'roe',
                            'solvencia',
                            'indice_endeudamiento',
                            'activos',
                            'totalgastosfamiliares',
                            'totalcuotadeuda',
                            'totalprestamo',
                            'cuotaprestamo',
                            'margenventas',
                            'Endeudamientopatrimonial',
                            'cuotaexcedente',
                            'comentarioasesor',
                            'comentarioadministrador',
                            'modulo',
                            'estado',
                        ));
                    }
            }
        } else {
            $totalgarantia = $garantias->sum('valor_mercado');

            return view('admin.creditos.evaluaciongrupal', compact(
                'prestamo',
                'responsable',
                'totalgarantia',
                'totalprestamo',
                'cuotaprestamo',
                'modulo',
                'comentarioasesor',
                'comentarioadministrador',
                'estado',
                'cuotastodas'
            ));
        }
    }



    public function viewsupervisar()
    {
        return view('admin.creditos.supervisar');
    }

    public function viewarqueo()
    {
            $usuarioId = Auth::user()->id;
            $sucursalId = Auth::user()->sucursal_id;
            $cajaAbierta = CajaTransaccion::where('sucursal_id', $sucursalId)
                ->where('user_id', $usuarioId)
                ->whereNull('hora_cierre')
                ->first();

            if (!$cajaAbierta) {
                return redirect('/admin/caja')->with('error', 'No hay una caja abierta.');
            }

            // Obtener ingresos, egresos, gastos y ingresos extras de la caja abierta
            $ingresos = $cajaAbierta->cantidad_ingresos;
            $egresos = $cajaAbierta->cantidad_egresos;
            $montoApertura = $cajaAbierta->monto_apertura;

            $gastos = Gasto::where('caja_transaccion_id', $cajaAbierta->id)->sum('monto_gasto');
            $ingresosExtras = IngresoExtra::where('caja_transaccion_id', $cajaAbierta->id)->sum('monto');

            return view('admin.caja.arqueo', compact('cajaAbierta', 'ingresos', 'egresos', 'montoApertura', 'gastos', 'ingresosExtras'));
    }


    public function viewHabilitarCaja()
    {
        $user = Auth::user();
        $sucursal_id = $user->sucursal_id;
        $cajas = Caja::where('sucursal_id', $sucursal_id)
            ->whereDoesntHave('transacciones', function ($query) {
                $query->whereNull('fecha_cierre');
            })
            ->get();

        return view('admin.caja.habilitar', compact('cajas'));
    }

    public function ultimaTransaccion($caja_id)
    {
        $transaccion = CajaTransaccion::with('user')
            ->where('caja_id', $caja_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($transaccion) {
            return response()->json(['success' => true, 'transaccion' => $transaccion]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function abrirCaja(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'monto_cierre' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $sucursal_id = $user->sucursal_id;

        // Crear una nueva transacción de caja
        $transaccion = CajaTransaccion::create([
            'caja_id' => $request->caja_id,
            'user_id' => $user->id,
            'sucursal_id' => $sucursal_id,
            'monto_apertura' => $request->monto_cierre,
            'json_apertura' => json_encode($request->all()), // Guardar todos los datos de apertura
            'hora_apertura' => now(),
            'fecha_apertura' => today(),
        ]);

        return response()->json(['success' => true, 'transaccion_id' => $transaccion->id]);
    }

    public function viewpagarcredito()
    {
        // Obtener solo los clientes activos (activo = 1)
        $creditos = credito::with('clientes')
            ->where('activo', 1)
            ->where('estado', 'aprobado')
            ->get();

        return view('admin.caja.pagarcredito', ['creditos' => $creditos]);
    }

    public function viewcobrar()
    {
        // Obtener solo los clientes activos (activo = 1)
        $creditos = credito::with('clientes')
            ->where('activo', 1)
            ->where('estado', 'pagado')
            ->get();

        return view('admin.caja.cobrar', ['creditos' => $creditos]);
    }

    public function pagar(Request $request, $id)
    {
        $prestamo = credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->first();
        $cliente = $prestamo->clientes->first();
        $responsable = auth()->user();
        $tipo = $prestamo->tipo;
        $totalprestamo = $prestamo->monto_total;
        $cuotaprestamo = $cuotas->monto;
        $descripcion = $prestamo->descripcion_negocio;

        $estado = $prestamo->estado;
        return view('admin.caja.desembolso', compact(
            'prestamo',
            'cliente',
            'responsable',
            'estado',
            'cuotas'
        ));
    }


    public function guardarArqueo(Request $request)
    {
        $billetes = [
            "200" => (string)$request->billete_200,
            "100" => (string)$request->billete_100,
            "50" => (string)$request->billete_50,
            "20" => (string)$request->billete_20,
            "10" => (string)$request->billete_10
        ];

        $monedas = [
            "5" => (string)$request->moneda_5,
            "2" => (string)$request->moneda_2,
            "1" => (string)$request->moneda_1,
            "0.5" => (string)$request->moneda_0_5,
            "0.2" => (string)$request->moneda_0_2,
            "0.1" => (string)$request->moneda_0_1
        ];

        $totalEfectivo = array_sum(array_map(function ($billete, $cantidad) {
            return (float)$billete * (float)$cantidad;
        }, array_keys($billetes), $billetes)) + array_sum(array_map(function ($moneda, $cantidad) {
            return (float)$moneda * (float)$cantidad;
        }, array_keys($monedas), $monedas));

        $depositos = $request->input('depositos');
        $saldoFinal = floatval(str_replace('S/. ', '', $request->input('saldo_final')));

        $transaccion = CajaTransaccion::find($request->input('caja_id'));
        $transaccion->json_cierre = json_encode([
            'billetes' => $billetes,
            'monedas' => $monedas,
            'depositos' => (string)$depositos
        ]);
        $transaccion->monto_cierre = $totalEfectivo;
        $transaccion->hora_cierre = now();
        $transaccion->fecha_cierre = now();
        $transaccion->save();

        return response()->json(['success' => true, 'transaccion_id' => $transaccion->id]);
    }

    public function verpagocuota($id)
    {
        $credito = Credito::find($id);
        $clientesCredito = CreditoCliente::where('prestamo_id', $id)->with('clientes')->get();
    
        $cuotasGenerales = [];
        $cuotasPorCliente = [];
    
        // Obtener todas las cuotas para calcular el estado de las generales
        $todasLasCuotas = Cronograma::where('id_prestamo', $id)->get();
    
        foreach ($clientesCredito as $clienteCredito) {
            $cuotas = Cronograma::where('id_prestamo', $id)
                ->where('cliente_id', $clienteCredito->cliente_id)
                ->get();
    
            foreach ($cuotas as $cuota) {
                // Asegurarse de que fecha_vencimiento sea una instancia de Carbon
                $fecha_vencimiento = Carbon::parse($cuota->fecha);
    
                $ingreso = Ingreso::where('prestamo_id', $id)
                    ->where('numero_cuota', $cuota->numero)
                    ->where('cliente_id', $clienteCredito->cliente_id)
                    ->first();
    
                if ($ingreso) {
                    $cuota->estado = 'pagado';
                    $cuota->fecha_pago = $ingreso->fecha_pago;
                    $cuota->dias_mora = $ingreso->dias_mora;
                    $cuota->monto_mora = $ingreso->monto_mora;
                    $cuota->porcentaje_mora = $ingreso->porcentaje_mora;
                    $cuota->monto_total_pago_final = round($ingreso->monto + $ingreso->monto_mora, 2);
                    $cuota->ingreso_id = $ingreso->id;
                } elseif (now()->greaterThan($fecha_vencimiento)) {
                    $cuota->estado = 'vencida';
                    $cuota->dias_mora = now()->diffInDays($fecha_vencimiento);
                    $cuota->porcentaje_mora = 0.3; // 0.3% por día de mora por cada mil soles
                    $cuota->monto_mora = round(($cuota->monto * $cuota->porcentaje_mora / 1000) * $cuota->dias_mora * 5, 2);
                    $cuota->monto_total_pago_final = round($cuota->monto + $cuota->monto_mora, 2);
                } else {
                    $cuota->estado = 'pendiente';
                    $cuota->dias_mora = 0;
                    $cuota->monto_mora = 0;
                    $cuota->porcentaje_mora = 0;
                    $cuota->monto_total_pago_final = round($cuota->monto, 2);
                }
            }
    
            $cuotasPorCliente[$clienteCredito->cliente_id] = $cuotas;
        }
    
        if ($credito->categoria == 'grupal') {
            foreach ($todasLasCuotas as $cuotaGeneral) {
                if (is_null($cuotaGeneral->cliente_id)) {
                    $fecha_vencimiento = Carbon::parse($cuotaGeneral->fecha);
                    $cuotasRelacionadas = Cronograma::where('id_prestamo', $id)
                        ->where('fecha', $cuotaGeneral->fecha)
                        ->whereNotNull('cliente_id')
                        ->get();
    
                    $estadoGeneral = 'pagado';
                    $pagadas = 0;
                    $pendientes = 0;
                    $vencidas = 0;
                    $montoPagado = 0;
                    $montoPendiente = 0;
                    $montoVencido = 0;
                    $diasMora = 0;
                    $montoMoraTotal = 0;
                    $ingreso_ids = [];
    
                    foreach ($cuotasRelacionadas as $cuotaRelacionada) {
                        $ingresoRelacionado = Ingreso::where('prestamo_id', $id)
                            ->where('numero_cuota', $cuotaRelacionada->numero)
                            ->where('cliente_id', $cuotaRelacionada->cliente_id)
                            ->first();
    
                        if (!$ingresoRelacionado) {
                            if (now()->greaterThan($fecha_vencimiento)) {
                                $estadoGeneral = 'vencida';
                                $vencidas++;
                                $montoVencido += $cuotaRelacionada->monto;
                                $diasMora = max($diasMora, now()->diffInDays($fecha_vencimiento)); // Usar el máximo de días de mora
                                $montoMoraTotal += round(($cuotaRelacionada->monto * 0.3 / 1000) * $diasMora * 5, 2);
                            } else {
                                $estadoGeneral = 'pendiente';
                                $pendientes++;
                                $montoPendiente += $cuotaRelacionada->monto;
                            }
                        } else {
                            $pagadas++;
                            $montoPagado += $cuotaRelacionada->monto;
                            $ingreso_ids[] = $ingresoRelacionado->id; // Almacenar IDs de ingresos
                        }
                    }
                    if ($estadoGeneral == 'pagado') {
                        $ingresoGeneral = Ingreso::where('prestamo_id', $id)
                            ->where('numero_cuota', $cuotaGeneral->numero)
                            ->whereNull('cliente_id')
                            ->first();
                        if ($ingresoGeneral) {
                            $ingreso_ids[] = $ingresoGeneral->id;
                        }
                    }
    
                    if ($pagadas > 0 && ($pendientes > 0 || $vencidas > 0)) {
                        $estadoGeneral = 'parcial';
                    }
    
                    $cuotaGeneral->estado = $estadoGeneral;
                    $cuotaGeneral->pagadas = $pagadas;
                    $cuotaGeneral->pendientes = $pendientes;
                    $cuotaGeneral->vencidas = $vencidas;
                    $cuotaGeneral->monto_pagado = $montoPagado;
                    $cuotaGeneral->monto_pendiente = $montoPendiente;
                    $cuotaGeneral->monto_vencido = $montoVencido;
                    $cuotaGeneral->dias_mora = $diasMora;
                    $cuotaGeneral->monto_mora = $montoMoraTotal;
                    $cuotaGeneral->monto_total_pago_final = round($cuotaGeneral->monto + $montoMoraTotal, 2);
                    $cuotaGeneral->ingreso_ids = $ingreso_ids; // Añadir IDs de ingresos
                    $cuotasGenerales[] = $cuotaGeneral;
                }
            }
        }
    
        return view('admin.creditos.verpagocuota', compact('credito', 'clientesCredito', 'cuotasGenerales', 'cuotasPorCliente'));
    }       

    public function pagocuota(Request $request)
    {
        $user = auth()->user();

        // Retrieve the last open cash transaction for the logged-in user
        $ultimaTransaccion = \App\Models\CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if there is an open cash transaction
        if (!$ultimaTransaccion) {
            return response()->json(['error' => 'No hay una caja abierta para el usuario actual'], 400);
        }

        // Register the income
        $ingreso = Ingreso::create([
            'transaccion_id' => $ultimaTransaccion->id,
            'prestamo_id' => $request->prestamo_id,
            'cliente_id' => $request->cliente_id,
            'cronograma_id' => $request->cronograma_id,
            'numero_cuota' => $request->numero_cuota,
            'monto' => $request->monto, // Monto cuota
            'monto_mora' => $request->monto_mora,
            'dias_mora' => $request->dias_mora,
            'porcentaje_mora' => $request->porcentaje_mora,
            'fecha_pago' => now()->toDateString(),
            'hora_pago' => now()->toTimeString(),
            'sucursal_id' => $user->sucursal_id,
            'monto_total_pago_final' => round($request->monto - $request->monto_mora, 2), // Monto total a pagar (incluyendo mora)
        ]);

        // Update the total income in the cash transaction
        $ultimaTransaccion->cantidad_ingresos = $ultimaTransaccion->cantidad_ingresos + $request->monto;
        $ultimaTransaccion->save();

        return response()->json(['success' => 'Cuota pagada con éxito', 'ingreso_id' => $ingreso->id]);
    }
    public function pagoGrupal(Request $request)
    {
        $user = auth()->user();

        // Retrieve the last open cash transaction for the logged-in user
        $ultimaTransaccion = \App\Models\CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if there is an open cash transaction
        if (!$ultimaTransaccion) {
            return response()->json(['error' => 'No hay una caja abierta para el usuario actual'], 400);
        }

        $prestamo_id = $request->prestamo_id;
        $fecha = $request->fecha;

        $cuotas = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('fecha', '=', $fecha)
            // ->whereNotNull('cliente_id')
            ->get();

        $ingreso_ids = [];

        DB::beginTransaction();
        try {
            foreach ($cuotas as $cuota) {
                // Verificar si la cuota ya tiene un ingreso asociado
                $ingresoExistente = Ingreso::where('prestamo_id', $prestamo_id)
                    ->where('cronograma_id', $cuota->id)
                    // ->where('cronograma_id', $cuota->id)
                    ->first();

                if (!$ingresoExistente) {
                    $dias_mora = 0;
                    $monto_mora = 0;
                    $porcentaje_mora = 0.3; // Asumiendo 0.3% de mora por día por cada mil soles

                    // Calcular mora si está vencida
                    if (Carbon::now()->greaterThan(Carbon::parse($cuota->fecha))) {
                        $dias_mora = Carbon::now()->diffInDays(Carbon::parse($cuota->fecha));
                        $monto_mora = round(($cuota->monto * $porcentaje_mora / 1000) * $dias_mora * 5, 2);
                    }

                    // Register the income for each cuota
                    $ingreso = Ingreso::create([
                        'transaccion_id' => $ultimaTransaccion->id,
                        'prestamo_id' => $cuota->id_prestamo,
                        'cliente_id' => $cuota->cliente_id,
                        'cronograma_id' => $cuota->id,
                        'numero_cuota' => $cuota->numero,
                        'monto' =>  round($cuota->monto + $monto_mora, 2),
                        'monto_mora' => $monto_mora,
                        'dias_mora' => $dias_mora,
                        'porcentaje_mora' => $porcentaje_mora,
                        'fecha_pago' => now()->toDateString(),
                        'hora_pago' => now()->toTimeString(),
                        'sucursal_id' => $user->sucursal_id,
                        'monto_total_pago_final' => $cuota->monto,
                    ]);
                    $ingreso_ids[] = $ingreso->id;
                    if($ingreso->cliente_id!=null){
                    $ultimaTransaccion->cantidad_ingresos +=  $ingreso->monto;
                     }
                }
            }

            $ultimaTransaccion->save(); // Guardar la transacción con la suma actualizada

            DB::commit();

            return response()->json(['success' => 'Pago grupal realizado con éxito', 'ingreso_ids' => $ingreso_ids]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al realizar el pago grupal: ' . $e->getMessage()], 500);
        }
    }


    public function viewpagares()
    {
        return view('admin.caja.pagares');
    }


    public function viewcargarcompromiso()
    {


        return view('admin.cobranza.cargarcompromiso');
    }

    public function viewcarta()
    {
        $today = Carbon::now()->toDateString();

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Administrador')) {
            $creditos = Credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma',
                'correlativoPagare',
                'garantia',
                'ingresos'
            ])
                ->withCount('creditoClientes as cliente_creditos_count')
                ->where('activo', 1)
                ->whereHas('cronograma', function ($query) use ($today) {
                    $query->where('fecha', '<', $today)
                        ->whereDoesntHave('ingresos');
                })
                ->get();
        } else{
            $creditos = Credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma',
                'correlativoPagare',
                'garantia',
                'ingresos'
            ])
                ->withCount('creditoClientes as cliente_creditos_count')
                ->where('activo', 1)
                ->where('user_id', $user->id)
                ->whereHas('cronograma', function ($query) use ($today) {
                    $query->where('fecha', '<', $today)
                        ->whereDoesntHave('ingresos');
                })
                ->get();
        }

        

        $result = $creditos->map(function ($credito) use ($today) {
            // Filtrar cronograma para obtener solo las cuotas pendientes
            $cuotaPendiente = $credito->cronograma->filter(function ($cuota) use ($today) {
                return $cuota->fecha < $today && $cuota->ingresos->isEmpty();
            })->first();

            if ($cuotaPendiente) {
                $diasDeAtraso = Carbon::now()->diffInDays(Carbon::parse($cuotaPendiente->fecha));

                return [
                    'id' => $credito->id,
                    'nombre_cliente' => $credito->producto == 'grupal' ? $credito->nombre_prestamo : $credito->clientes->first()->nombre,
                    'producto' => $credito->producto,
                    'cuota' => $cuotaPendiente->numero, // Asumimos que 'numero' es el número de cuota
                    'fecha' => $cuotaPendiente->fecha,
                    'dias_de_atraso' => $diasDeAtraso
                ];
            }

            return null; // Return null if no pending cuota
        })->filter(); // filter() to remove null values

        return view('admin.cobranza.carta', ['creditos' => $result]);
    }

    public function viewgenerarcompromiso()
    {
        return view('admin.cobranza.generarcompromiso');
    }

    public function viewgenerarnotificacion()
    {
        return view('admin.cobranza.generarnotificacion');
    }

    public function viewegresos()
    {
        return view('admin.transacciones.egresos');
    }

    public function viewingresos()
    {
        return view('admin.transacciones.ingresos');
    }

    public function viewprestamosactivos()
    {
        return view('admin.reportes.prestamosactivos');
    }

    public function viewprestamosvencidos()
    {
        return view('admin.reportes.prestamosvencidos');
    }



    /**
     * crear credito nuevo
     */
    public function createnuevo()
    {
        return view('admin.creditos.createnuevo');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $decodedData = $request->all();
        foreach ([
            'clientesArray', 'proyeccionesArray', 'inventarioArray', 'deudasFinancierasArray', 'gastosOperativosArray', 'boletasArray', 'gastosProducirArray',
            'inventarioArray1', 'ventasdiarias', 'inventarioprocesoArray', 'ventasMensualesArray', 'tipoProductoArray', 'gastosAgricolaArray', 'inventarioMaterialArray'
        ] as $key) {
            if ($request->filled($key)) {
                $decodedData[$key] = json_decode($request->input($key), true);
            }
        }
        $request->validate([
            'tipo_credito' => 'required|max:50',
            'tipo_producto' => 'required|max:100',
            'subproducto' => 'nullable|max:100',
            'destino_credito' => 'nullable|max:100',
            'recurrencia' => 'nullable|max:50',
            'recurrencia1' => 'nullable|max:50',
            'tasa_interes' => 'nullable|numeric|min:0|max:100',
            'tiempo_credito' => 'nullable|integer|min:1',
            'monto' => 'nullable|numeric|min:0',
            'fecha_desembolso' => 'nullable|date',
            'descripcion_negocio' => 'nullable|max:255',
            'periodo_gracia_dias' => 'nullable|numeric|min:0',
            'nombre_prestamo' => 'nullable|max:200',
            'cantidad_grupo' => 'nullable|integer|min:1',
            'foto_grupal' => 'nullable|image',
            'activo' => 'boolean',
        ]);
        DB::beginTransaction();
        try {
            $prestamo = new Credito();
            $prestamo->tipo = $request->tipo_credito;
            $prestamo->producto = $request->tipo_producto;
            $prestamo->subproducto = $request->subproducto;
            $prestamo->destino = $request->destino_credito;
            $prestamo->recurrencia = $request->tipo_producto == 'grupal' ? $request->recurrencia1 : $request->recurrencia;
            $prestamo->tasa = $request->tasa_interes;
            $prestamo->tiempo = $request->tiempo_credito;
            $prestamo->monto_total = $request->monto;
            $prestamo->fecha_desembolso = $request->fecha_desembolso;
            $prestamo->periodo_gracia_dias = $request->periodo_gracia_dias;
            $prestamo->porcentaje_credito = $request->porcentaje_venta_credito;
            $prestamo->estado = "pendiente";

            if ($request->tipo_producto !== 'grupal') {
                $prestamo->categoria = 'individual';
                $prestamo->nombre_prestamo = "prestamo indivual";
                $prestamo->cantidad_integrantes = 1;
                $prestamo->descripcion_negocio = $request->descripcion_negocio;
            } else {
                $prestamo->categoria = 'grupal';
                $prestamo->nombre_prestamo = $request->nombre_prestamo;
                $prestamo->cantidad_integrantes = $request->cantidad_grupo;
                $prestamo->descripcion_negocio =  $request->descripcion_negocio;
            }
            $prestamo->user_id = Auth::id();
            $prestamo->save();

            //garantia
            $garantia = \App\Models\Garantia::create([
                'descripcion' => $request->descripcion_garantia,
                'valor_mercado' => $request->valor_mercado,
                'valor_realizacion' => $request->valor_realizacion,
                'valor_gravamen' => $request->valor_gravamen,
                'id_prestamo' => $prestamo->id,
                'estado' => 'activo'
            ]);
            //ACTIVOS
            $activos = \App\Models\Activos::create([
                'cuentas_por_cobrar' => $request->cuentas_por_cobrar,
                'saldo_en_caja_bancos' => $request->saldo_caja_bancos,
                'adelanto_a_proveedores' => $request->adelanto_a_proveedores,
                'otros' => $request->otros,
                'prestamo_id' => $prestamo->id,
            ]);

            //existe archivo de garantia
            if ($request->hasFile('archivo_garantia') && $request->file('archivo_garantia')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo_garantia')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo_garantia')->storeAs('public/documentos_garantia', $nombreArchivo);
                $garantia->documento_pdf = $ruta;
                $garantia->save();
            }

            if ($request->tipo_producto != 'grupal') {
                $cliente = Cliente::where('documento_identidad', $request->documento_identidad)->where('activo', 1)->first();
                if ($cliente) {
                    $credito_cliente = new CreditoCliente();
                    $credito_cliente->prestamo_id = $prestamo->id;
                    $credito_cliente->cliente_id = $cliente->id;
                    $credito_cliente->monto_indivual = $request->monto;
                    $credito_cliente->save();
                    $this->guardarCronograma($prestamo, $cliente, $request, $request->monto);
                }
            } else {
                if (is_array($decodedData['clientesArray'])) {
                    foreach ($decodedData['clientesArray'] as $clienteData) {
                        $cliente = Cliente::where('documento_identidad', $clienteData['documento'])->where('activo', 1)->first();
                        if ($cliente) {
                            $credito_cliente = new CreditoCliente();
                            $credito_cliente->prestamo_id = $prestamo->id;
                            $credito_cliente->cliente_id = $cliente->id;
                            $credito_cliente->monto_indivual = $clienteData['monto'];
                            $credito_cliente->save();
                            $this->guardarCronograma($prestamo, $cliente, $request, $clienteData['monto']);
                        }
                    }
                    //guardar cronograma del  credito en general
                    $this->guardarCronograma($prestamo, null, $request, $request->monto);

                    
                }
            }


            $this->saveArrayData($decodedData, $prestamo->id, $request);
            $fecha_desembolso = Carbon::parse($request->fecha_desembolso);
            $tiempo_credito = $request->tiempo_credito;
            $prestamo->fecha_fin = $fecha_desembolso->copy()->addMonths($tiempo_credito);

            if ($request->hasFile('foto_grupal') && $request->file('foto_grupal')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('foto_grupal')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('foto_grupal')->storeAs('public/fotos_grupales', $nombreArchivo);
                $prestamo->foto_grupal = $ruta;
            }
            $prestamo->activo = $request->activo ?? true;
            $prestamo->save();
            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Prestamo creado con exito',
                'prestamo' => $prestamo,
                'user' => Auth::id()
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al crear el prestamo: ' . $e
            ])->setStatusCode(500);
        }
    }
    protected function guardarCronograma($prestamo, $cliente, $request, $monto)
    {
        $fecha_desembolso = Carbon::parse($request->fecha_desembolso);
        $fechaconperiodogracia = clone $fecha_desembolso;
        $fechaconperiodogracia->addDays($request->periodo_gracia_dias);
        $tiempo = $request->tiempo_credito;
        $frecuencia = $request->tipo_producto == 'grupal' ? $request->recurrencia1 : $request->recurrencia;
        $tasaInteres = $request->tasa_interes;
        $tipo_producto = $request->tipo_producto;

        // Calcular los intereses del período de gracia
        $tasaDiaria = pow(1 + ($tasaInteres / 100), 1 / 360) - 1;
        $interesesPeriodoGracia = $monto * $tasaDiaria * $request->periodo_gracia_dias;
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $tiempo;

        $cuotas = $this->calcularCuota($monto, $tasaInteres, $tiempo, $frecuencia, $interesesMensualesPorGracia, $tipo_producto);

        $fechaCuota = $fechaconperiodogracia->copy();

        foreach ($cuotas as $cuota) {
            switch ($frecuencia) {
                case 'catorcenal':
                    $fechaCuota->addDays(14);
                    break;
                case 'quincenal':
                    $fechaCuota->addDays(15);
                    break;
                case 'veinteochenal':
                    $fechaCuota->addDays(28);
                    break;
                case 'semestral':
                    $fechaCuota->addMonths(6);
                    break;
                case 'anual':
                    $fechaCuota->addMonths(12);
                    break;
                case 'mensual':
                default:
                    $fechaCuota->addMonth();;
                    break;
            }

            $cronograma = new Cronograma();
            $cronograma->fecha = clone $fechaCuota;

            $cronograma->monto = $cuota['cuota']; // Cuota fija más intereses distribuidos y otros componentes
            $cronograma->numero = $cuota['numero_cuota'];
            $cronograma->capital = $cuota['capital'];
            $cronograma->interes = $cuota['interes'];
            $cronograma->amortizacion = $cuota['amortizacion'];
            $cronograma->saldo_deuda = $cuota['saldo_deuda'];
            $cronograma->id_prestamo = $prestamo->id;
            $cronograma->cliente_id = $cliente->id ?? null; // Asignar cliente
            $cronograma->save();
        }
    }

    public function calcularCuota($monto, $tea, $periodos, $frecuencia, $interesesMensualesPorGracia, $tipo_producto)
    {
        switch ($frecuencia) {
            case 'catorcenal':
                $n = 24;
                break;
            case 'veinteochenal':
                $n = 12;
                break;
            case 'quincenal':
                $n = 24;
                break;
            case 'semestral':
                $n = 2;
                break;
            case 'anual':
                $n = 1;
                break;
            case 'mensual':
            default:
                $n = 12;
                break;
        }

        $tasaPeriodo = pow(1 + ($tea / 100), 1 / $n) - 1;
        $cuota = ($monto * $tasaPeriodo * pow(1 + $tasaPeriodo, $periodos)) / (pow(1 + $tasaPeriodo, $periodos) - 1);

        if ($tipo_producto == 'grupal') {
            $cuota_real = $cuota + $interesesMensualesPorGracia + 0.021 * $cuota;
        } else if ($interesesMensualesPorGracia > 0) {
            $cuota_real = $cuota + $interesesMensualesPorGracia + 0.011 * $cuota;
        } else {
            $cuota_real = $cuota;
        }

        $saldo = $monto;
        $cuotas = [];
        $totalAmortizacion = 0;
        $totalSoles = 0;

        for ($i = 0; $i < $periodos; $i++) {

            if ($tipo_producto == 'grupal') {
                $interesPeriodo = $saldo * $tasaPeriodo + $interesesMensualesPorGracia + 0.021 * $cuota;
            } else if ($interesesMensualesPorGracia > 0) {
                $interesPeriodo = $saldo * $tasaPeriodo + $interesesMensualesPorGracia + 0.011 * $cuota;
            } else {
                $interesPeriodo = $saldo * $tasaPeriodo;
            }

            $amortizacion = $cuota_real - $interesPeriodo;
            $saldo -= $amortizacion;

            $cuotas[] = [
                'numero_cuota' => $i + 1,
                'capital' => round($saldo, 2),
                'interes' => round($interesPeriodo, 2),
                'amortizacion' => round($amortizacion, 2),
                'cuota' => round($cuota_real, 2),
                'saldo_deuda' => round($saldo, 2)
            ];

            $totalAmortizacion += round($amortizacion, 2);
            $totalSoles += round($cuota_real, 2);
        }


        $diferenciaAmortizacion = $monto - $totalAmortizacion;
        $diferenciaTotalSoles = ($cuota_real * $periodos) - $totalSoles;

        if ($diferenciaAmortizacion !== 0 || $diferenciaTotalSoles !== 0) {
            $ultimaCuota = $cuotas[count($cuotas) - 1];
            $ultimaCuota['amortizacion'] += round($diferenciaAmortizacion, 2);
            $ultimaCuota['cuota'] += round($diferenciaTotalSoles, 2);
            $ultimaCuota['capital'] = 0; // El saldo al final debe ser 0
            $totalAmortizacion += round($diferenciaAmortizacion, 2);
            $totalSoles += round($diferenciaTotalSoles, 2);
        }


        return $cuotas;
    }



    protected function saveArrayData(array $data, $prestamoId, $request)
    {
        if (isset($data['proyeccionesArray']) && is_array($data['proyeccionesArray'])) {
            foreach ($data['proyeccionesArray'] as $proyeccionData) {
                \App\Models\ProyeccionesVentas::create([
                    'descripcion_producto' => $proyeccionData['descripcion'],
                    'unidad_medida' => $proyeccionData['unidadMedida'],
                    'precio_compra' => $proyeccionData['precioCompra'],
                    'precio_venta' => $proyeccionData['precioVenta'],
                    'proporcion_ventas' => $proyeccionData['proporcion_ventas'],
                    'id_prestamo' => $prestamoId,
                    'estado' => 'activo',
                    'ingredientes' => isset($proyeccionData['ingredientes']) ? json_encode($proyeccionData['ingredientes']) : null,
                ]);
            }
        }

        if (isset($data['ventasdiarias']) && is_array($data['ventasdiarias'])) {
            foreach ($data['ventasdiarias'] as $venta) {
                \App\Models\VentasDiarias::create([
                    'dia' => $venta['dia'],
                    'cantidad_maxima' => $venta['max'],
                    'cantidad_minima' => $venta['min'],
                    'promedio' => $venta['promedio'],
                    'prestamo_id' => $prestamoId
                ]);
            }
        }

        if (isset($data['deudasFinancierasArray']) && is_array($data['deudasFinancierasArray'])) {
            foreach ($data['deudasFinancierasArray'] as $deudaData) {
                \App\Models\DeudasFinancieras::create([
                    'nombre_entidad' => $deudaData['entidad'],
                    'saldo_capital' => $deudaData['saldoCapital'],
                    'cuota' => $deudaData['cuota'],
                    'tiempo_restante' => $deudaData['tiempoRestante'],
                    'prestamo_id' => $prestamoId,
                    'estado' => 'activo'
                ]);
            }
        }

        if (isset($data['gastosOperativosArray']) && is_array($data['gastosOperativosArray'])) {
            foreach ($data['gastosOperativosArray'] as $gastoData) {
                \App\Models\GastosOperativos::create([
                    'descripcion' => $gastoData['descripcion'],
                    'precio_unitario' => $gastoData['precioUnitario'],
                    'cantidad' => $gastoData['cantidad'],
                    'id_prestamo' => $prestamoId,
                    'acciones' => 'activo'
                ]);
            }
        }

        // Pasó a ser gastos familiar para avanzar
        if (isset($data['inventarioArray1']) && is_array($data['inventarioArray1'])) {
            foreach ($data['inventarioArray1'] as $inventarioData) {
                \App\Models\GastosFamiliares::create([
                    'descripcion' => $inventarioData['descripcion'],
                    'precio_unitario' => $inventarioData['precioUnitario'],
                    'cantidad' => $inventarioData['cantidad'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }

        if (isset($data['inventarioprocesoArray']) && is_array($data['inventarioprocesoArray'])) {
            foreach ($data['inventarioprocesoArray'] as $inventarioData) {
                \App\Models\Inventario::create([
                    'descripcion' => $inventarioData['descripcion'],
                    'precio_unitario' => $inventarioData['precioUnitario'],
                    'cantidad' => $inventarioData['cantidad'],
                    'id_prestamo' => $prestamoId,
                    'unidad' => $inventarioData['unidad'],
                    'tipo_inventario' => 2,
                ]);
            }
        }
        if (isset($data['inventarioArray']) && is_array($data['inventarioArray'])) {
            foreach ($data['inventarioArray'] as $inventarioData) {
                \App\Models\Inventario::create([
                    'descripcion' => $inventarioData['descripcion'],
                    'precio_unitario' => $inventarioData['precioUnitario'],
                    'cantidad' => $inventarioData['cantidad'],
                    'id_prestamo' => $prestamoId,
                    'unidad' => $inventarioData['unidad'],
                    'tipo_inventario' => 1,
                ]);
            }
        }

        if (isset($data['boletasArray']) && is_array($data['boletasArray'])) {
            foreach ($data['boletasArray'] as $boletaData) {
                \App\Models\Boleta::create([
                    'numero_boleta' => $boletaData['numeroBoleta'],
                    'monto_boleta' => $boletaData['montoBoleta'],
                    'descuento_boleta' => $boletaData['descuentoBoleta'],
                    'total_boleta' => $boletaData['totalBoleta'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }


        if (isset($data['gastosProducirArray']) && is_array($data['gastosProducirArray']) && count($data['gastosProducirArray']) > 0) {
            $gasto = \App\Models\GastoProducir::create([
                'nombre_actividad' => $request->nombre_actividad,
                'cantidad_terreno' => $request->cantidad_terreno,
                'produccion_total' => $request->produccion_total,
                'precio_kg' => $request->precio_kg,
                'id_prestamo' => $prestamoId
            ]);
            foreach ($data['gastosProducirArray'] as $gastoProducirData) {
                \App\Models\GastosProducir::create([
                    'descripcion_gasto' => $gastoProducirData['descripcionGasto'],
                    'precio_unitario' => $gastoProducirData['precioUnitario'],
                    'cantidad' => $gastoProducirData['cantidad'],
                    'total_gasto' => $gastoProducirData['totalGasto'],
                    'id_prestamo' => $prestamoId,
                    'id_gasto_producir' => $gasto->id,
                ]);
            }
        }

        if (isset($data['ventasMensualesArray']) && is_array($data['ventasMensualesArray'])) {
            foreach ($data['ventasMensualesArray'] as $boletaData) {
                \App\Models\VentasMensuales::create([
                    'mes' => $boletaData['mes'],
                    'porcentaje' => $boletaData['porcentaje'],
                    'id_prestamo' => $prestamoId

                ]);
            }
        }
        if (isset($data['gastosAgricolaArray']) && is_array($data['gastosAgricolaArray'])) {
            $total = 0;

            \App\Models\ProductoAgricola::create([
                'id_prestamo' => $prestamoId,
                'nombre_actividad' => $request->nombre_actividad,
                'unidad_medida_siembra' => $request->cantidad_terreno,
                'hectareas' => $request->cantidad_cultivar ?? 0,
                'cantidad_cultivar' => $request->cantidad_cultivar,
                'unidad_medida_venta' => $request->unidad_medida_venta,
                'rendimiento_unidad_siembra' => $request->rendimiento_unidad_siembra,
                'ciclo_productivo_meses' => $request->ciclo_productivo,
                'mes_inicio' => $request->mes_inicio,
            ]);
            foreach ($data['gastosAgricolaArray'] as $gastoData) {
                $suma = 0;
                $row = \App\Models\GastosOperativos::create([
                    'descripcion' => $gastoData['gasto'],
                    'precio_unitario' => !empty($gastoData['precioUnitario']) ? $gastoData['precioUnitario'] : 0,
                    'cantidad' => 0,
                    'id_prestamo' => $prestamoId,
                    'acciones' => 'activo',
                    'unidad' => $gastoData['unidad'],
                    'mes1' => $gastoData['mes1'],
                    'mes2' => $gastoData['mes2'],
                    'mes3' => $gastoData['mes3'],
                    'mes4' => $gastoData['mes4'],
                    'mes5' => $gastoData['mes5'],
                    'mes6' => $gastoData['mes6'],
                    'mes7' => $gastoData['mes7'],
                    'mes8' => $gastoData['mes8'],
                    'mes9' => $gastoData['mes9'],
                    'mes10' => $gastoData['mes10'],
                    'mes11' => $gastoData['mes11'],
                    'mes12' => $gastoData['mes12']
                ]);
                $mes1 = !empty($gastoData['mes1']) ? $gastoData['mes1'] : 0;
                $mes2 = !empty($gastoData['mes2']) ? $gastoData['mes2'] : 0;
                $mes3 = !empty($gastoData['mes3']) ? $gastoData['mes3'] : 0;
                $mes4 = !empty($gastoData['mes4']) ? $gastoData['mes4'] : 0;
                $mes5 = !empty($gastoData['mes5']) ? $gastoData['mes5'] : 0;
                $mes6 = !empty($gastoData['mes6']) ? $gastoData['mes6'] : 0;
                $mes7 = !empty($gastoData['mes7']) ? $gastoData['mes7'] : 0;
                $mes8 = !empty($gastoData['mes8']) ? $gastoData['mes8'] : 0;
                $mes9 = !empty($gastoData['mes9']) ? $gastoData['mes9'] : 0;
                $mes10 = !empty($gastoData['mes10']) ? $gastoData['mes10'] : 0;
                $mes11 = !empty($gastoData['mes11']) ? $gastoData['mes11'] : 0;
                $mes12 = !empty($gastoData['mes12']) ? $gastoData['mes12'] : 0;

                $suma = $mes1 + $mes2 + $mes3 + $mes4 + $mes5 + $mes6 + $mes7 + $mes8 + $mes9 + $mes10 + $mes11 + $mes12;

                $row->cantidad = $suma;
                $row->save();
                $total = $total + $suma;
            }
        }
        if (isset($data['inventarioMaterialArray']) && is_array($data['inventarioMaterialArray'])) {
            foreach ($data['inventarioMaterialArray'] as $inventarioData) {
                \App\Models\Inventario::create([
                    'descripcion' => $inventarioData['descripcion'],
                    'precio_unitario' => $inventarioData['precioUnitario'],
                    'cantidad' => $inventarioData['cantidad'],
                    'id_prestamo' => $prestamoId,
                    'unidad' => $inventarioData['unidad'],
                    'tipo_inventario' => 3,
                ]);
            }
        }
        if (isset($data['tipoProductoArray']) && is_array($data['tipoProductoArray'])) {
            foreach ($data['tipoProductoArray'] as $inventarioData) {
                \App\Models\TipoProducto::create([
                    'producto' => $inventarioData['PRODUCTO'],
                    'precio' =>  !empty($inventarioData['precio_unitario']) ? $inventarioData['precio_unitario'] : 0,
                    'porcentaje' => !empty($inventarioData['procentaje_producto']) ? $inventarioData['procentaje_producto'] : 0,
                    'id_prestamo' => $prestamoId,

                ]);
            }
        }
    }
    // public function calcularCuota($monto, $tea, $periodos)
    // {
    //     $tasaMensual = pow(1 + ($tea / 100), 1 / 12) - 1;
    //     return ($monto * $tasaMensual * pow((1 + $tasaMensual), $periodos)) / (pow((1 + $tasaMensual), $periodos) - 1);
    // }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $credito = credito::find($id);
        $garantia = \App\Models\Garantia::where('id_prestamo', $id)->first();
        $clientes = CreditoCliente::with('clientes')->where('prestamo_id', $id)->get();
        $activos = \App\Models\Activos::where('prestamo_id', $id)->first();
        $proyeccionesVentas = \App\Models\ProyeccionesVentas::where('id_prestamo', $id)->get();
        $ventasDiarias = \App\Models\VentasDiarias::where('prestamo_id', $id)->get();
        $deudasFinancieras = \App\Models\DeudasFinancieras::where('prestamo_id', $id)->get();
        $gastosOperativos = \App\Models\GastosOperativos::where('id_prestamo', $id)->get();
        $gastosFamiliares = \App\Models\GastosFamiliares::where('id_prestamo', $id)->get();
        $inventario = \App\Models\Inventario::where('id_prestamo', $id)->where('tipo_inventario', 1)->get();
        $inventarioProceso = \App\Models\Inventario::where('id_prestamo', $id)->where('tipo_inventario', 2)->get();
        $boletas = \App\Models\Boleta::where('id_prestamo', $id)->get();
        $gastosProducir = \App\Models\GastoProducir::where('id_prestamo', $id)->with('gastos')->get();
        $ventasMensuales = \App\Models\VentasMensuales::where('id_prestamo', $id)->get();
        $gastosAgricolas =   \App\Models\ProductoAgricola::where('id_prestamo', $id)->first();
        $inventarioMaterial = \App\Models\Inventario::where('id_prestamo', $id)->where('tipo_inventario', 3)->get();
        $tipoProducto = \App\Models\TipoProducto::where('id_prestamo', $id)->get();

        return response()->json([
            'credito' => $credito,
            'clientes' => $clientes,
            'activos' => $activos,
            'proyeccionesVentas' => $proyeccionesVentas,
            'ventasDiarias' => $ventasDiarias,
            'deudasFinancieras' => $deudasFinancieras,
            'gastosOperativos' => $gastosOperativos,
            'gastosFamiliares' => $gastosFamiliares,
            'inventario' => $inventario,
            'boletas' => $boletas,
            'gastosProducir' => $gastosProducir,
            'ventasMensuales' => $ventasMensuales,
            'gastosAgricolas' => $gastosAgricolas,
            'inventarioMaterial' => $inventarioMaterial,
            'tipoProducto' => $tipoProducto,
            'inventarioProceso' => $inventarioProceso,
            'garantia' => $garantia
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $credito = credito::find($id);
        $tipo = $credito->tipo;
        $producto = $credito->producto;
        $subproducto = $credito->subproducto;
        if ($tipo == 'comercio' && $producto != 'grupal') {
            return view('admin.creditos.editcomercio', compact('id'));
        }
        if ($tipo == 'servicio' && $producto != 'grupal') {
            return view('admin.creditos.editservico', compact('id'));
        }
        if ($tipo == 'produccion' && $producto != 'agricola' && $producto != 'grupal') {
            return view('admin.creditos.editproduccion', compact('id'));
        }
        if ($tipo == 'produccion' && $producto == 'agricola' && $producto != 'grupal') {
            return view('admin.creditos.editagricola', compact('id'));
        }
        if ($producto == 'grupal') {
            return view('admin.creditos.editgrupal', compact('id'));
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar al credito por su ID
        $credito = credito::find($id);

        if (!$credito) {
            // Si el cliente no existe, redireccionar con un mensaje de error
            return redirect()->route('creditos.index')
                ->with('mensaje', 'El cliente que intentas eliminar no existe.')
                ->with('icono', 'error');
        }

        // Cambiar el estado activo a 0 (inactivo)
        $credito->activo = 0;
        $credito->save();

        // Redireccionar a la página de inicio
        return redirect()->route('creditos.index')
            ->with('mensaje', 'Se desactivó al cliente de manera correcta')
            ->with('icono', 'success');
    }

    public function vercuotas(string $id)
    {
        $credito = credito::findOrFail($id); // Buscar el crédito por ID
        $fechaDesembolso = $credito->fecha_desembolso; // Obtener la fecha de desembolso del crédito
        $tiempoMeses = $credito->tiempo; // Obtener el tiempo en meses del crédito

        // Calcular las cuotas
        $cuotas = [];
        $fechaCuota = $fechaDesembolso;
        for ($i = 1; $i <= $tiempoMeses; $i++) {
            $cuotas[] = [
                'numero' => $i,
                'fecha' => $fechaCuota->addMonth()->format('Y-m-d') // Agregar un mes a la fecha de desembolso
            ];
        }
    }
}
