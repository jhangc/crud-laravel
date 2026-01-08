<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Credito;
use App\Models\Cliente;
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
use App\Models\DepositoCts;
use App\Models\Reprogramacion;

class CreditoController extends Controller
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
            $creditos = Credito::with('clientes')->where('activo', 1)->get();
        } else {
            // Si no es administrador, obtener solo los créditos registrados por el usuario
            $creditos = Credito::with('clientes')->where('activo', 1)->where('user_id', $user->id)->get();
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
    public function joya()
    {
        return view('admin.creditos.joya');
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
        $creditos = Credito::with('clientes')
            ->where('activo', 1)
            ->where('estado', 'revisado')
            ->get();
        return view('admin.creditos.aprobar', ['creditos' => $creditos]);
    }

    public function proyecciones(Request $request, $id)
    {

        $modulo = $request->query('modulo'); // Obtener el parámetro 'modulo' de la URL

        //gsgfgd

        $prestamo = Credito::find($id);
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

        $prestamo = Credito::find($id);
        $responsable = $prestamo->user;



        $tipo = $prestamo->tipo;

        $comentarioasesor = $prestamo->comentario_asesor;
        $comentarioadministrador = $prestamo->comentario_administrador;

        // Calcular Totales
        $factorsemana = 15 / 7;
        $factormes = $factorsemana * 2;

        $totalprestamo = $prestamo->monto_total;
        $cuotaprestamo = $cuotas->monto;

        $estado = $prestamo->estado;

        if ($prestamo->producto != "grupal" && ($prestamo->categoria != 'credijoya')) {
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
                    $totalGastosOperativos = $gastosOperativos->sum(fn($gasto) => $gasto->precio_unitario * $gasto->cantidad);
                    $total_venta_credito = (($prestamo->porcentaje_credito) * $totalVentas) / 100;

                    $total_inventario = $inventario->sum(fn($item) => $item->precio_unitario * $item->cantidad);

                    $activo_corriente = $activos->saldo_en_caja_bancos + $activos->cuentas_por_cobrar + $activos->adelanto_a_proveedores + $total_inventario;
                    $activofijo = $garantias->sum('valor_mercado');
                    $activo = $activo_corriente + $activofijo;
                    $pasivo = $deudas->sum('saldo_capital');
                    $totalcuotadeuda = $deudas->sum('cuota');

                    $utilidadOperativa = $utilidadBruta - $totalGastosOperativos;
                    $saldo_disponible_negocio = $utilidadOperativa - $totalcuotadeuda;
                    $totalgastosfamiliares = round(($gastosfamiliares->sum(fn($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                    $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                    $rentabilidad_ventas = $totalVentas != 0 ? round((($saldo_disponible_negocio / $totalVentas) * 100), 2) : 0;
                    $rotacion_inventario = $total_inventario != 0 ? round(($totalCompras / $total_inventario), 2) : 0;
                    $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : $activo_corriente;
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

                        $totalGastosOperativos = round(($gastosOperativos->sum(fn($gasto) => $gasto->precio_unitario * $gasto->cantidad)), 2);
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
                            $totalinventario = round(($inventario->sum(fn($inven) => $inven->precio_unitario * $inven->cantidad)), 2);
                        }

                        $activo_corriente = $saldo_en_caja_bancos + $cuenta_cobrar + $adelanto_proveedores + $totalinventario;
                        $totalgarantia = $garantias->sum('valor_mercado');
                        $activofijo = $totalgarantia;

                        $totalactivo = $activo_corriente + $activofijo;
                        $totaldeudas = $deudas->sum('saldo_capital');
                        $totalcuotadeuda = $deudas->sum('cuota');
                        $pasivo = $totaldeudas;
                        $patrimonioneto = $totalactivo - $pasivo;

                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
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
                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : $activo_corriente;
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

                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
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

                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : $activo_corriente;

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
                        $totalGastosOperativos = $gastosOperativos->sum(fn($gasto) => $gasto->precio_unitario * $gasto->cantidad);
                        $total_venta_credito = (($prestamo->porcentaje_credito) * $totalVentas) / 100;

                        $totalinventarioterminado = $inventarioterminado->sum(fn($item) => $item->precio_unitario * $item->cantidad);
                        $totalinventarioproceso = $inventarioproceso->sum(fn($item) => $item->precio_unitario * $item->cantidad);

                        $totalinventariomateriales = $inventariomateriales !== null ? $inventariomateriales->sum(fn($item) => $item->precio_unitario * $item->cantidad) : 0;

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
                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                        $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                        $rentabilidad_ventas = $totalVentas != 0 ? round((($saldo_disponible_negocio / $totalVentas) * 100), 2) : 0;
                        $rotacion_inventario = $total_inventario != 0 ? round(($totalCompras / $total_inventario), 2) : 0;
                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : $activo_corriente;
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

                        $cantidad_cultivo = $cicloproductivo * $productos_agricolas->cantidad_cultivar * $productos_agricolas->rendimiento_unidad_siembra;

                        //$totalVentas = round((($ventasdiarias->sum('promedio')) * $factormes), 2);

                        // Inicializar variables
                        $totalVentas = 0;
                        //$sumaPonderadaRelacion = 0;

                        // Recorrer las proyecciones para calcular el monto total de ventas y la relación de compra-venta promedio ponderada
                        foreach ($tipo_producto_agricola  as $producto) {
                            $montoVenta = $cantidad_cultivo * $producto->precio * ($producto->porcentaje / 100);
                            $totalVentas += $montoVenta;
                        }


                        $totalCompras = 0;
                        foreach ($gastosOperativos as $gastoOperativo) {
                            $gasto = $cicloproductivo * $gastoOperativo->precio_unitario * $gastoOperativo->cantidad;
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


                        $totalinventarioterminado = $inventarioterminado->sum(fn($item) => $item->precio_unitario * $item->cantidad);
                        $totalinventarioproceso = $inventarioproceso->sum(fn($item) => $item->precio_unitario * $item->cantidad);

                        $totalinventariomateriales = $inventariomateriales !== null ? $inventariomateriales->sum(fn($item) => $item->precio_unitario * $item->cantidad) : 0;

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
                        $totalgastosfamiliares = round(($gastosfamiliares->sum(fn($gastos) => $gastos->precio_unitario * $gastos->cantidad)), 2);
                        $saldo_final = $saldo_disponible_negocio - $totalgastosfamiliares;

                        $rentabilidad_ventas = $totalVentas != 0 ? round((($saldo_disponible_negocio / $totalVentas) * 100), 2) : 0;
                        $rotacion_inventario = $total_inventario != 0 ? round(($totalCompras / $total_inventario), 2) : 0;
                        $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : $activo_corriente;
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
        } else if ($prestamo && ($prestamo->categoria == 'credijoya')) {
            $modulo  = $request->query('modulo'); // 'aprobar' o null
            $cliente = $prestamo->clientes()->first();
            $responsable = $prestamo->user;

            $joyas = \App\Models\CredijoyaJoya::where('prestamo_id', $prestamo->id)->get();
            $cronograma = \App\Models\Cronograma::where('id_prestamo', $prestamo->id)
                ->orderBy('numero')->get();

            // Resumen
            $tasacionTotal = (float) ($prestamo->tasacion_total ?? $joyas->sum('valor_tasacion'));
            $max80         = round($tasacionTotal * 0.80, 2);
            $montoAprobado = (float) ($prestamo->monto_total ?? 0);
            $tea           = (float) ($prestamo->tasa ?? 0);
            $itf           = (float) ($prestamo->itf_desembolso ?? 0);
            $deudaPrev     = (float) ($prestamo->deuda_prev_monto ?? 0);
            $deudaPrevModo = (string) ($prestamo->deuda_prev_modo ?? '');
            $descDeuda     = $deudaPrevModo === 'pagar_con_desembolso' ? $deudaPrev : 0;
            $neto          = max($montoAprobado - $itf - $descDeuda, 0);

            // Cuota “a evaluar”: 1ra cuota si existe
            $cuotaEvaluar  = optional($cronograma->first())->monto ?? 0;

            $comentarioasesor        = $prestamo->comentario_asesor;
            $comentarioadministrador = $prestamo->comentario_administrador;
            $estado                  = $prestamo->estado;

            return view('admin.creditos.evaluacion_credijoya', compact(
                'prestamo',
                'cliente',
                'responsable',
                'joyas',
                'cronograma',
                'tasacionTotal',
                'max80',
                'montoAprobado',
                'tea',
                'itf',
                'neto',
                'cuotaEvaluar',
                'comentarioasesor',
                'comentarioadministrador',
                'estado',
                'modulo'
            ));
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
        $pago_cts = DepositoCts::where('caja_transaccion_id', $cajaAbierta->id)
            ->where('estado', 1)               // Solo movimientos pagados
            ->where('tipo_transaccion', 2)     // Solo retiros (egresos)
            ->sum('monto');


        return view('admin.caja.arqueo', compact('cajaAbierta', 'ingresos', 'egresos', 'montoApertura', 'gastos', 'ingresosExtras', 'pago_cts'));
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
        $creditos = Credito::with('clientes')
            ->where('activo', 1)
            ->where('estado', 'aprobado')
            ->get();

        return view('admin.caja.pagarcredito', ['creditos' => $creditos]);
    }

    public function viewcobrar()
    {
        // Obtener solo los clientes activos (activo = 1)
        $creditos = Credito::with('clientes')
            ->where('activo', 1)
            ->where('estado', 'pagado')
            ->get();

        return view('admin.caja.cobrar', ['creditos' => $creditos]);
    }

    public function pagar(Request $request, $id)
    {
        $prestamo = Credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->first();
        $cliente = $prestamo->clientes->first();
        $responsable = $prestamo->user->first();
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
        $reprogramacion = Reprogramacion::where('credito_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();
        $cuotasGenerales = [];
        $cuotasPorCliente = [];

        // Variables de control para la última cuota en créditos grupales e individuales
        $controlUltimaGeneral = 0;
        $controlUltimaIndividual = 0;

        // Obtener todas las cuotas para calcular el estado de las generales
        $todasLasCuotas = Cronograma::where('id_prestamo', $id)->get();

        $puedeAmortizar = null;
        $cuotasVencidas = null;

        foreach ($clientesCredito as $clienteCredito) {
            $cuotas = Cronograma::where('id_prestamo', $id)
                ->where('cliente_id', $clienteCredito->cliente_id)
                ->get();

            foreach ($cuotas as $cuota) {
                // Asegurarse de que fecha_vencimiento sea una instancia de Carbon
                $fecha_vencimiento = Carbon::parse($cuota->fecha);

                //esta la antigua consulta
                // $ingreso = Ingreso::where('prestamo_id', $id)
                //     ->where('numero_cuota', $cuota->numero)
                //     ->where('cliente_id', $clienteCredito->cliente_id)
                //     ->first();

                $ingreso = Ingreso::where('prestamo_id', $cuota->id_prestamo)
                    ->where('numero_cuota', $cuota->numero)
                    ->where('cliente_id', $clienteCredito->cliente_id)
                    ->where('cronograma_id', $cuota->id)
                    ->first();

                if ($ingreso) {
                    $cuota->estado = 'pagado';
                    $cuota->fecha_pago = $ingreso->fecha_pago;
                    $cuota->dias_mora = $ingreso->dias_mora;
                    $cuota->monto_mora = $ingreso->monto_mora;
                    $cuota->porcentaje_mora = $ingreso->porcentaje_mora;
                    $cuota->monto_total_pago_final = round($ingreso->monto, 2);
                    $cuota->ingreso_id = $ingreso->id;
                    $cuota->diferencia = $ingreso->diferencia;
                    $puedeAmortizar++;
                } elseif (now()->greaterThan($fecha_vencimiento)) {
                    $cuota->estado = 'vencida';
                    $cuota->dias_mora = now()->diffInDays($fecha_vencimiento);
                    $cuota->porcentaje_mora = 1.5; // 0.3% por día de mora por cada mil soles
                    $cuota->monto_mora = round(($cuota->monto * $cuota->porcentaje_mora / 1000) * $cuota->dias_mora
                        // * 5
                        ,
                        2
                    );
                    $cuota->monto_total_pago_final = round($cuota->monto + $cuota->monto_mora, 2);
                    if ($controlUltimaIndividual == 0) {
                        $cuota->ultima = 1;
                        $controlUltimaIndividual = 1;
                    }
                    $cuotasVencidas++;
                } else {
                    $cuota->estado = 'pendiente';
                    $cuota->dias_mora = 0;
                    $cuota->monto_mora = 0;
                    $cuota->porcentaje_mora = 0;
                    $cuota->monto_total_pago_final = round($cuota->monto, 2);
                    if ($controlUltimaIndividual == 0) {
                        $cuota->ultima = 1;
                        $controlUltimaIndividual = 1;
                    }
                }
            }

            $cuotasPorCliente[$clienteCredito->cliente_id] = $cuotas;
        }

        if ($credito->categoria == 'grupal') {
            $puedeAmortizar = null;
            $cuotasVencidas = null;

            foreach ($todasLasCuotas as $cuotaGeneral) {
                if (is_null($cuotaGeneral->cliente_id)) {
                    $fecha_vencimiento = Carbon::parse($cuotaGeneral->fecha);
                    $cuotasRelacionadas = Cronograma::where('id_prestamo', $cuotaGeneral->id_prestamo)
                        ->where('fecha', $cuotaGeneral->fecha)
                        ->where('numero', $cuotaGeneral->numero)
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
                    $fecha_pago = null;
                    $dias_mora_general = 0;
                    $monto_mora_general = 0;

                    foreach ($cuotasRelacionadas as $cuotaRelacionada) {
                        //esta la antigua consulta
                        // $ingresoRelacionado = Ingreso::where('prestamo_id', $id)
                        //     ->where('numero_cuota', $cuotaRelacionada->numero)
                        //     ->where('cliente_id', $cuotaRelacionada->cliente_id)
                        //     ->first();

                        $ingresoRelacionado = Ingreso::where('prestamo_id', $cuotaRelacionada->id_prestamo)
                            ->where('numero_cuota', $cuotaRelacionada->numero)
                            ->where('cliente_id', $cuotaRelacionada->cliente_id)
                            ->where('cronograma_id', $cuotaRelacionada->id)
                            ->first();

                        if (!$ingresoRelacionado) {
                            if (now()->greaterThan($fecha_vencimiento)) {
                                $estadoGeneral = 'vencida';
                                $vencidas++;
                                $montoVencido += $cuotaRelacionada->monto;

                                // Cálculo del monto de mora por cada cuota relacionada
                                $diasMoraRelacionada = now()->diffInDays($fecha_vencimiento);
                                $montoMoraRelacionada = round(($cuotaRelacionada->monto * 1.5 / 1000) * $diasMoraRelacionada
                                    // * 5
                                    ,
                                    2
                                );
                                $diasMora = max($diasMora, $diasMoraRelacionada); // Usar el máximo de días de mora
                                $montoMoraTotal += $montoMoraRelacionada;
                            } else {
                                $estadoGeneral = 'pendiente';
                                $pendientes++;
                                $montoPendiente += $cuotaRelacionada->monto;
                            }
                        } else {
                            $pagadas++;
                            $montoPagado += $ingresoRelacionado->monto;
                            $ingreso_ids[] = $ingresoRelacionado->id;
                            $fecha_pago = $ingresoRelacionado->fecha_pago;
                            $diasMora = $ingresoRelacionado->dias_mora;
                            $montoMoraTotal = $ingresoRelacionado->monto_mora;
                        }
                    }

                    if ($estadoGeneral == 'pagado') {
                        //esta la antigua consulta
                        // $ingresoGeneral = Ingreso::where('prestamo_id', $id)
                        //     ->where('numero_cuota', $cuotaGeneral->numero)
                        //     ->whereNull('cliente_id')
                        //     ->first();

                        $ingresoGeneral = Ingreso::where('prestamo_id', $cuotaGeneral->id_prestamo)
                            ->where('numero_cuota', $cuotaGeneral->numero)
                            ->where('cronograma_id', $cuotaGeneral->id)
                            ->whereNull('cliente_id')
                            ->first();

                        if ($ingresoGeneral) {
                            $ingreso_ids[] = $ingresoGeneral->id;
                            $fecha_pago = $ingresoGeneral->fecha_pago;
                            $diasMora = $ingresoGeneral->dias_mora;
                            $montoMoraTotal = $ingresoGeneral->monto_mora;
                        } else {
                            //si todas als relacionadas estan pagadas  si  no existe ingresod ela gneral si ,
                            $newIngreso = new Ingreso();
                            $newIngreso->transaccion_id = $ingresoRelacionado->transaccion_id;
                            $newIngreso->cliente_id = null;
                            $newIngreso->prestamo_id = $ingresoRelacionado->prestamo_id;
                            $newIngreso->cronograma_id = $cuotaGeneral->id;
                            $newIngreso->numero_cuota = $cuotaGeneral->numero;
                            $newIngreso->monto_cuota = null;
                            $newIngreso->fecha_pago = $ingresoRelacionado->fecha_pago;
                            $newIngreso->hora_pago = $ingresoRelacionado->hora_pago;
                            $newIngreso->monto = $cuotaGeneral->monto;
                            $newIngreso->monto_mora = $montoMoraTotal;          // Nuevo campo
                            $newIngreso->dias_mora = $diasMora;           // Nuevo campo
                            $newIngreso->porcentaje_mora = $diasMora > 0 ? 1.5 : 0;   // Nuevo campo
                            $newIngreso->monto_total_pago_final = $montoMoraTotal + $cuotaGeneral->monto; // Nuevo campo
                            $newIngreso->sucursal_id = $ingresoRelacionado->sucursal_id;
                            $newIngreso->save();
                        }
                        $puedeAmortizar++;
                    }
                    if ($vencidas > 0) {
                        $cuotasVencidas++;
                    }

                    if ($pagadas > 0 && ($pendientes > 0 || $vencidas > 0)) {
                        $estadoGeneral = 'parcial';
                    }

                    // Asignar los valores calculados a la cuota general
                    $cuotaGeneral->estado = $estadoGeneral;
                    $cuotaGeneral->pagadas = $pagadas;
                    $cuotaGeneral->pendientes = $pendientes;
                    $cuotaGeneral->vencidas = $vencidas;
                    $cuotaGeneral->monto_pagado = $montoPagado;
                    $cuotaGeneral->monto_pendiente = $montoPendiente;
                    $cuotaGeneral->monto_vencido = $montoVencido;
                    $cuotaGeneral->dias_mora = $diasMora; // Usar el máximo de días de mora calculado
                    $cuotaGeneral->ingreso_ids = $ingreso_ids;
                    $cuotaGeneral->fecha_pago = $fecha_pago;
                    $cuotaGeneral->monto_mora = $montoMoraTotal; // Monto de mora total calculado
                    $cuotaGeneral->monto_total_pago_final = $montoMoraTotal + $cuotaGeneral->monto;

                    // Marcar la última cuota
                    if ($controlUltimaGeneral == 0 && $estadoGeneral != 'pagado') {
                        $cuotaGeneral->ultima = 1;
                        $controlUltimaGeneral = 1;
                    }

                    $cuotasGenerales[] = $cuotaGeneral;
                }
            }
        }

        return view('admin.creditos.verpagocuota', compact('credito', 'clientesCredito', 'cuotasGenerales', 'cuotasPorCliente', 'puedeAmortizar', 'cuotasVencidas', 'reprogramacion'));
    }


    public function verpagototalindividual(Request $request)
    {
        $prestamo_id = $request->prestamo_id;
        $fecha_cuota = Carbon::parse($request->fecha);
        $numero_cuota = $request->numero_cuota;

        // Buscar la cuota actual
        $cuotaActual = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('fecha', $fecha_cuota)
            ->whereNotNull('cliente_id')
            ->where('numero', $numero_cuota)
            // Filtrar por cliente
            ->first();

        if (!$cuotaActual) {
            return response()->json(['error' => 'No se encontró la cuota actual'], 404);
        }

        $detalleCuotas = [];
        $totalMora = 0;
        $totalPagar = 0;

        // Calcular mora y total a pagar para la cuota actual
        $diasMora = 0;
        $montoMora = 0;

        if (now()->greaterThan($fecha_cuota)) {
            $diasMora = now()->diffInDays($fecha_cuota);
            $montoMora = round(($cuotaActual->monto * 1.5 / 1000) * $diasMora
                // * 5
                ,
                2
            );
        }

        $totalCuotaActual = $cuotaActual->monto + $montoMora;

        $detalleCuotas[] = [
            'numero' => $cuotaActual->numero,
            'monto' => $cuotaActual->monto,
            'dias_mora' => $diasMora,
            'monto_mora' => $montoMora,
            'total_pagar' => $totalCuotaActual,
            'fecha' => $cuotaActual->fecha
        ];

        $totalMora += $montoMora;
        $totalPagar += $totalCuotaActual;

        // Obtener todas las cuotas restantes a partir de la cuota actual
        $cuotasRestantes = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>', $numero_cuota)
            ->whereNotNull('cliente_id') // Filtrar por cliente
            ->get();

        foreach ($cuotasRestantes as $cuota) {
            $montoMoraRestante = 0;
            $diasMoraRestante = 0;

            if (now()->greaterThan(Carbon::parse($cuota->fecha))) {
                $diasMoraRestante = now()->diffInDays(Carbon::parse($cuota->fecha));
                $montoMoraRestante = round(($cuota->monto * 1.5 / 1000) * $diasMoraRestante
                    // * 5
                    ,
                    2
                );
            }
            if (now()->greaterThan(Carbon::parse($cuota->fecha))) {
                $totalCuotaRestante = $cuota->monto + $montoMoraRestante;

                $detalleCuotas[] = [
                    'numero' => $cuota->numero,
                    'monto' => $cuota->monto,
                    'dias_mora' => $diasMoraRestante,
                    'monto_mora' => $montoMoraRestante,
                    'total_pagar' => $totalCuotaRestante,
                    'fecha' => $cuota->fecha
                ];

                $totalMora += $montoMoraRestante;
                $totalPagar += $totalCuotaRestante;
            } else {
                $totalCuotaRestante = $cuota->amortizacion + $montoMoraRestante;

                $detalleCuotas[] = [
                    'numero' => $cuota->numero,
                    'monto' => $cuota->amortizacion,
                    'dias_mora' => $diasMoraRestante,
                    'monto_mora' => $montoMoraRestante,
                    'total_pagar' => $totalCuotaRestante,
                    'fecha' => $cuota->fecha
                ];

                $totalMora += $montoMoraRestante;
                $totalPagar += $totalCuotaRestante;
            }
        }

        // Devolver los datos al usuario
        return response()->json([
            'detalle_cuotas' => $detalleCuotas,
            'total_pagar' => $totalPagar
        ]);
    }

    public function verpagototalgrupal(Request $request)
    {
        $prestamo_id = $request->prestamo_id;
        $fecha_cuota = Carbon::parse($request->fecha);
        $numero_cuota = $request->numero_cuota;

        // Buscar la cuota actual
        $cuotaActual = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('fecha', $fecha_cuota)
            ->where('numero', $numero_cuota)
            ->whereNull('cliente_id')
            ->first();

        if (!$cuotaActual) {
            return response()->json(['error' => 'No se encontró la cuota actual'], 404);
        }

        $detalleCuotas = [];
        $totalMora = 0;
        $totalPagar = 0;

        // Calcular mora y total a pagar para la cuota actual
        $diasMora = 0;
        $montoMora = 0;

        if (now()->greaterThan($fecha_cuota)) {
            $diasMora = now()->diffInDays($fecha_cuota);
            $montoMora = round(($cuotaActual->monto * 1.5 / 1000) * $diasMora
                // * 5
                ,
                2
            );
        }

        $totalCuotaActual = $cuotaActual->monto + $montoMora;

        $detalleCuotas[] = [
            'numero' => $cuotaActual->numero,
            'monto' => $cuotaActual->monto,
            'dias_mora' => $diasMora,
            'monto_mora' => $montoMora,
            'total_pagar' => $totalCuotaActual,
            'fecha' => $cuotaActual->fecha
        ];

        $totalMora += $montoMora;
        $totalPagar += $totalCuotaActual;

        // Obtener todas las cuotas restantes a partir de la cuota actual
        $cuotasRestantes = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>', $numero_cuota)
            ->whereNull('cliente_id')
            ->get();

        foreach ($cuotasRestantes as $cuota) {
            $montoMoraRestante = 0;
            $diasMoraRestante = 0;

            if (now()->greaterThan(Carbon::parse($cuota->fecha))) {
                $diasMoraRestante = now()->diffInDays(Carbon::parse($cuota->fecha));
                $montoMoraRestante = round(($cuota->monto * 1.5 / 1000) * $diasMoraRestante
                    // * 5
                    ,
                    2
                );
            }
            if (now()->greaterThan(Carbon::parse($cuota->fecha))) {
                $totalCuotaRestante = $cuota->monto + $montoMoraRestante;

                $detalleCuotas[] = [
                    'numero' => $cuota->numero,
                    'monto' => $cuota->monto,
                    'dias_mora' => $diasMoraRestante,
                    'monto_mora' => $montoMoraRestante,
                    'total_pagar' => $totalCuotaRestante,
                    'fecha' => $cuota->fecha
                ];

                $totalMora += $montoMoraRestante;
                $totalPagar += $totalCuotaRestante;
            } else {
                $totalCuotaRestante = $cuota->amortizacion + $montoMoraRestante;

                $detalleCuotas[] = [
                    'numero' => $cuota->numero,
                    'monto' => $cuota->amortizacion,
                    'dias_mora' => $diasMoraRestante,
                    'monto_mora' => $montoMoraRestante,
                    'total_pagar' => $totalCuotaRestante,
                    'fecha' => $cuota->fecha
                ];

                $totalMora += $montoMoraRestante;
                $totalPagar += $totalCuotaRestante;
            }
        }

        // Devolver los datos al usuario
        return response()->json([
            'detalle_cuotas' => $detalleCuotas,
            'total_pagar' => $totalPagar
        ]);
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

        $montoPagado  = $request->mpc_monto_pagado;
        $montoTotal   = $request->monto;
        $diferencia   = 0;



        // Si paga más, la diferencia se reserva
        if ($montoPagado > $montoTotal) {
            $diferencia = round($montoPagado - $montoTotal, 2);
            $montoAplicado = $montoTotal;
        } else {
            // igual o menor, aplicamos lo que pagó
            $montoAplicado = $montoPagado;
        }



        // Register the income
        $ingreso = Ingreso::create([
            'transaccion_id' => $ultimaTransaccion->id,
            'prestamo_id' => $request->prestamo_id,
            'cliente_id' => $request->cliente_id,
            'cronograma_id' => $request->cronograma_id,
            'numero_cuota' => $request->numero_cuota,
            'monto' => $montoAplicado, // Monto cuota
            'monto_mora' => $request->monto_mora,
            'dias_mora' => $request->dias_mora,
            'diferencia' => $diferencia,
            'porcentaje_mora' => $request->porcentaje_mora,
            'fecha_pago' => now()->toDateString(),
            'hora_pago' => now()->toTimeString(),
            'sucursal_id' => $user->sucursal_id,
            'monto_total_pago_final' => round($montoAplicado - $request->monto_mora, 2), // Monto total a pagar (incluyendo mora)
        ]);

        // Update the total income in the cash transaction
        $ultimaTransaccion->cantidad_ingresos += $montoAplicado;
        $ultimaTransaccion->save();

        // 6. Si hay sobra, ajustar la siguiente cuota
        $nextInfo = null;
        if ($diferencia > 0) {
            $nextCuota = \App\Models\Cronograma::where('id_prestamo', $request->prestamo_id)
                ->where('numero', '>', $request->numero_cuota)
                ->orderBy('numero')
                ->first();

            if ($nextCuota) {
                // Restamos la diferencia al monto total de esa siguiente cuota
                $nextCuota->monto = round($nextCuota->monto - $diferencia, 2);
                $nextCuota->save();

                $nextInfo = [
                    'cronograma_id'            => $nextCuota->id,
                    'nuevo_monto_total'        => $nextCuota->monto,
                ];
            }
        }

        $response = [
            'success'    => 'Cuota pagada con éxito',
            'ingreso_id' => $ingreso->id,
            'diferencia' => $diferencia,
        ];

        if ($nextInfo) {
            $response['siguiente_cuota'] = $nextInfo;
        }

        return response()->json($response);
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
                    $porcentaje_mora =  1.5; // Asumiendo 0.3% de mora por día por cada mil soles

                    // Calcular mora si está vencida
                    if (Carbon::now()->greaterThan(Carbon::parse($cuota->fecha))) {
                        $dias_mora = Carbon::now()->diffInDays(Carbon::parse($cuota->fecha));
                        $monto_mora = round(($cuota->monto * $porcentaje_mora / 1000) * $dias_mora
                            // * 5
                            ,
                            2
                        );
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
                    if ($ingreso->cliente_id != null) {
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

    public function confirmarPagoIndividual(Request $request)
    {
        $user = auth()->user();

        // Obtener la última transacción de caja abierta
        $ultimaTransaccion = \App\Models\CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ultimaTransaccion) {
            return response()->json(['error' => 'No hay una caja abierta para el usuario actual'], 400);
        }

        $prestamo_id = $request->prestamo_id;
        $numero_cuota = $request->numero_cuota;

        // Obtener la cuota actual y las restantes
        $cuotas = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>=', $numero_cuota)
            ->whereNotNull('cliente_id')
            ->get();

        $ingreso_ids = [];
        $totalPago = 0; // Para actualizar la caja con el total ingresado

        DB::beginTransaction();
        try {
            foreach ($cuotas as $cuota) {
                // Verificar si la cuota ya tiene un ingreso asociado
                $ingresoExistente = Ingreso::where('prestamo_id', $prestamo_id)
                    ->where('cronograma_id', $cuota->id)
                    ->first();

                if (!$ingresoExistente) {
                    $dias_mora = 0;
                    $monto_mora = 0;
                    $porcentaje_mora = 1.5; // 0.3% de mora por día por cada mil soles

                    // Determinar si la cuota está vencida
                    $fecha_cuota = Carbon::parse($cuota->fecha);
                    $vencida = Carbon::now()->greaterThan($fecha_cuota);

                    if ($vencida) {
                        $dias_mora = Carbon::now()->diffInDays($fecha_cuota);
                        $monto_mora = round(($cuota->monto * $porcentaje_mora / 1000) * $dias_mora, 2);
                    }

                    // Si la cuota está vencida, se paga toda la cuota más la mora
                    // Si no está vencida, solo se paga la cuota (sin mora)
                    // Para cuotas futuras, se paga solo la amortización
                    if ($cuota->numero == $numero_cuota || $vencida) {
                        $monto_pago = $cuota->monto;
                    } else {
                        $monto_pago = $cuota->amortizacion;
                    }

                    $totalPagar = round($monto_pago + $monto_mora, 2);

                    // Registrar el ingreso
                    $ingreso = Ingreso::create([
                        'transaccion_id' => $ultimaTransaccion->id,
                        'prestamo_id' => $cuota->id_prestamo,
                        'cliente_id' => $cuota->cliente_id,
                        'cronograma_id' => $cuota->id,
                        'numero_cuota' => $cuota->numero,
                        'monto' => $totalPagar,
                        'monto_mora' => $monto_mora,
                        'dias_mora' => $dias_mora,
                        'porcentaje_mora' => $porcentaje_mora,
                        'fecha_pago' => now()->toDateString(),
                        'hora_pago' => now()->toTimeString(),
                        'sucursal_id' => $user->sucursal_id,
                        'monto_total_pago_final' => $totalPagar,
                    ]);

                    $ingreso_ids[] = $ingreso->id;
                    $totalPago += $totalPagar;
                }
            }

            // Actualizar la cantidad total de ingresos en la caja
            $ultimaTransaccion->cantidad_ingresos += $totalPago;
            $ultimaTransaccion->save();

            DB::commit();

            return response()->json(['success' => 'Pago individual realizado con éxito', 'ingreso_ids' => $ingreso_ids]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al realizar el pago individual: ' . $e->getMessage()], 500);
        }
    }


    public function confirmarPagoGrupal(Request $request)
    {
        $user = auth()->user();

        // Obtener la última transacción de caja abierta
        $ultimaTransaccion = \App\Models\CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ultimaTransaccion) {
            return response()->json(['error' => 'No hay una caja abierta para el usuario actual'], 400);
        }

        $prestamo_id = $request->prestamo_id;
        $numero_cuota = $request->numero_cuota;

        // Obtener la cuota actual y las restantes
        $cuotas = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>=', $numero_cuota)
            // ->whereNotNull('cliente_id')
            ->get();

        $ingreso_ids = [];

        DB::beginTransaction();
        try {
            foreach ($cuotas as $cuota) {
                // Verificar si la cuota ya tiene un ingreso asociado
                $ingresoExistente = Ingreso::where('prestamo_id', $prestamo_id)
                    ->where('cronograma_id', $cuota->id)
                    ->first();

                if (!$ingresoExistente) {
                    $dias_mora = 0;
                    $monto_mora = 0;
                    $porcentaje_mora = 1.5; // 0.3% de mora por día por cada mil soles

                    // Determinar si la cuota está vencida
                    $fecha_cuota = Carbon::parse($cuota->fecha);
                    $vencida = Carbon::now()->greaterThan($fecha_cuota);
                    if ($vencida) {
                        $dias_mora = Carbon::now()->diffInDays($fecha_cuota);
                        $monto_mora = round(($cuota->monto * $porcentaje_mora / 1000) * $dias_mora, 2);
                    }
                    if ($cuota->numero == $numero_cuota || $vencida) {
                        $monto_pago = $cuota->monto;
                    } else {
                        $monto_pago = $cuota->amortizacion;
                    }

                    $totalPagar = round($monto_pago + $monto_mora, 2);


                    $ingreso = Ingreso::create([
                        'transaccion_id' => $ultimaTransaccion->id,
                        'prestamo_id' => $cuota->id_prestamo,
                        'cliente_id' => $cuota->cliente_id,
                        'cronograma_id' => $cuota->id,
                        'numero_cuota' => $cuota->numero,
                        'monto' => $totalPagar,
                        'monto_mora' => $monto_mora,
                        'dias_mora' => $dias_mora,
                        'porcentaje_mora' => $porcentaje_mora,
                        'fecha_pago' => now()->toDateString(),
                        'hora_pago' => now()->toTimeString(),
                        'sucursal_id' => $user->sucursal_id,
                        'monto_total_pago_final' => $totalPagar,
                    ]);
                    $ingreso_ids[] = $ingreso->id;
                    $ultimaTransaccion->cantidad_ingresos += $ingreso->monto;
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



    public function solicitarReprogramacion(Request $request)
    {
        $prestamoId = $request->prestamo_id;
        $hoy        = now()->toDateString();
        $credito    = Credito::findOrFail($prestamoId);

        // Base de la consulta: cuotas futuras o vencidas sin pagar
        $baseQuery = Cronograma::where('id_prestamo', $prestamoId)
            ->whereNotIn('id', function ($q) {
                $q->select('cronograma_id')->from('ingresos');
            });

        // Filtrar según tipo de crédito
        if ($credito->categoria === 'grupal') {
            // Sólo la parte “general” (cliente_id IS NULL)
            $baseQuery->whereNull('cliente_id');
        } else {
            // Crédito individual: las cuotas con cliente_id definido
            $baseQuery->whereNotNull('cliente_id');
        }

        // Cuotas pendientes (>= hoy)
        $pendientes = (clone $baseQuery)
            ->where('fecha', '>=', $hoy)
            ->get();

        // Cuotas en mora (< hoy)
        $vencidas = (clone $baseQuery)
            ->where('fecha', '<', $hoy)
            ->count();

        return response()->json([
            'cuotas_restantes'  => $pendientes->count(),
            'intereses_totales' => round($pendientes->sum('interes'), 2),
            'capital_pendiente' => round($pendientes->sum('amortizacion'), 2),
            'periodo_pago'      => $credito->recurrencia,
            'tasa_interes'      => $credito->tasa,
            'cuotas_vencidas'   => $vencidas,
        ]);
    }


    public function calcularCuotaPendiente(Request $request)
    {
        $prestamo_id = $request->prestamo_id;
        $detalles = $this->calcularCuotaPendienteEIntereses($prestamo_id);
        if (!$detalles) {
            return response()->json(['error' => 'No hay cuotas pendientes.'], 200);
        }
        return response()->json($detalles);
    }



    public function calcularCuotaPendienteEIntereses($prestamo_id)
    {
        // Obtener el crédito y su frecuencia
        $credito = Credito::find($prestamo_id);
        $categoria_c = $credito->categoria;
        $frecuencia = $credito->frecuencia;

        $query = Ingreso::where('prestamo_id', $prestamo_id);
        if ($categoria_c != 'grupal') {
            $query->whereNotNull('cliente_id');
        } else {
            $query->whereNull('cliente_id');
        }
        $ultimaCuotaPagada = $query->orderBy('numero_cuota', 'desc')->first();

        if (!$ultimaCuotaPagada) {
            return null;
        }

        $ultimo_cronograma_pagado = Cronograma::find($ultimaCuotaPagada->cronograma_id);

        $query2 = Cronograma::where('id_prestamo', $prestamo_id);
        if ($categoria_c != 'grupal') {
            $query2->whereNotNull('cliente_id');
        } else {
            $query2->whereNull('cliente_id');
        }
        $query2->where('numero', '>', $ultimo_cronograma_pagado->numero)
            ->whereNotIn('id', function ($query) {
                $query->select('cronograma_id')->from('ingresos');
            });
        $cuotaPendiente = $query2->orderBy('numero', 'asc')->first();

        if (!$cuotaPendiente) {
            return null;
        }

        $fechaUltimaCuota = Carbon::parse($ultimo_cronograma_pagado->fecha);
        $diasTranscurridos = $fechaUltimaCuota->isPast()
            ? $fechaUltimaCuota->diffInDays(now())
            : 0;

        switch ($frecuencia) {
            case 'catorcenal':
                $diasPeriodo = 14;
                break;
            case 'quincenal':
                $diasPeriodo = 15;
                break;
            case 'veinteochenal':
                $diasPeriodo = 28;
                break;
            case 'semestral':
                $diasPeriodo = 182;
                break;
            case 'anual':
                $diasPeriodo = 365;
                break;
            case 'mensual':
            default:
                $diasPeriodo = 30;
                break;
        }

        $tasaInteresDiaria = $cuotaPendiente->interes / $diasPeriodo;

        $intereses = $tasaInteresDiaria * $diasTranscurridos;

        $cronogramasPendientes = Cronograma::where('id_prestamo', $prestamo_id)
            ->when($categoria_c != 'grupal', function ($query) {
                $query->whereNotNull('cliente_id');
            }, function ($query) {
                $query->whereNull('cliente_id');
            })
            ->where('fecha', '>=', $cuotaPendiente->fecha)
            ->whereNotIn('id', function ($query) {
                $query->select('cronograma_id')->from('ingresos');
            })
            ->orderBy('fecha', 'asc')
            ->get();
        $amortizacionFaltante = $cronogramasPendientes->sum('amortizacion');

        $proximaCuota = $cronogramasPendientes->firstWhere('numero', $ultimo_cronograma_pagado->numero + 1);

        $fechaProxima = Carbon::parse($proximaCuota->fecha);
        if ($proximaCuota) {
            $fechaProxima = Carbon::parse($proximaCuota->fecha);
            $puedeamortizar = $fechaProxima->diffInDays(now()) < 8 ? 0 : 1;
        } else {
            $puedeamortizar = 0;
        }

        return [
            'cuota'                 => $cuotaPendiente,
            'intereses'             => round($intereses, 2),
            'amortizacion_faltante' => round($amortizacionFaltante, 2),
            'monto_total'           => round($amortizacionFaltante + $intereses, 2),
            'dias_transcurridos' => $diasTranscurridos,
            'dias_periodo' => $diasPeriodo,
            'puedeamortizar' => $puedeamortizar,
            'dias_fal' => $fechaProxima->diffInDays(now())
        ];
    }
    public function generarNuevoCronograma(Request $request)
    {
        $prestamo_id = $request->prestamo_id;
        $credito = Credito::find($prestamo_id);

        if (!$credito) {
            return response()->json(['error' => 'Crédito no encontrado.'], 404);
        }

        // Datos enviados desde el formulario
        $opcion = $request->opcion; // 'reducir_cuota' o 'reducir_plazo'
        $numero_cuota = $request->numero_cuota;
        $intereses_a_pagar = $request->intereses_a_pagar;
        $capital_pendiente = $request->capital_pendiente;
        $total_a_pagar_posible = $request->total_a_pagar_posible;
        $monto_pago_capital = $request->monto_pago_capital;

        $categoria_c = $credito->categoria;

        // Nuevo principal es el total pendiente menos lo que se va a abonar a capital
        $nuevoPrincipal = $total_a_pagar_posible - $monto_pago_capital;

        // Contar la cantidad de cuotas pendientes a partir de la cuota actual (excluyendo la ya pagada)
        $pendingQuery = Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>', $numero_cuota);
        if ($categoria_c != 'grupal') {
            $pendingQuery->whereNotNull('cliente_id');
        } else {
            $pendingQuery->whereNull('cliente_id');
        }
        $remainingPeriods = $pendingQuery->count();

        if ($remainingPeriods <= 0) {
            return response()->json(['error' => 'No quedan cuotas pendientes.'], 400);
        }

        // Determinar la frecuencia y calcular la tasa periódica
        // Se asume que en el crédito se guarda la recurrencia en $credito->recurrencia
        $frecuencia = $credito->recurrencia;
        $tea = $credito->tasa; // Tasa anual (en %)

        // Definir número de periodos por año y el intervalo en días para calcular la fecha
        $intervalDays = 30; // valor por defecto (mensual)
        switch ($frecuencia) {
            case 'catorcenal':
                $nPeriodsPerYear = 26;
                $intervalDays = 14;
                break;
            case 'quincenal':
                $nPeriodsPerYear = 24;
                $intervalDays = 15;
                break;
            case 'veinteochenal':
                $nPeriodsPerYear = 12;
                $intervalDays = 28;
                break;
            case 'semestral':
                $nPeriodsPerYear = 2;
                $intervalDays = 182;
                break;
            case 'anual':
                $nPeriodsPerYear = 1;
                $intervalDays = 365;
                break;
            case 'mensual':
            default:
                $nPeriodsPerYear = 12;
                $intervalDays = 30;
                break;
        }
        $i = pow(1 + $tea / 100, 1 / $nPeriodsPerYear) - 1;
        $tasaDiaria = pow(1 + ($tea / 100), 1 / 360) - 1;
        $cuotas = [];
        $periodoGraciaDias = $credito->periodo_gracia_dias ?? 0;
        $fechaInicio = Carbon::now()->addDays($periodoGraciaDias);

        $interesesPeriodoGracia = $nuevoPrincipal * $tasaDiaria * $periodoGraciaDias;
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $remainingPeriods;

        if ($opcion == 'reducir_cuota') {
            $n = $remainingPeriods;
            $cuota = ($nuevoPrincipal * $i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1);
            if ($credito->producto == 'grupal') {
                $cuota_real = $cuota + 0.021 * $cuota;
            } else if ($periodoGraciaDias > 0) {
                $cuota_real = $cuota + $interesesMensualesPorGracia + 0.011 * $cuota;
            } else {
                $cuota_real = $cuota;
            }

            $saldo = $nuevoPrincipal;
            for ($j = 0; $j < $n; $j++) {
                $interesPeriodo = $saldo * $i;
                $amortizacion = $cuota_real - $interesPeriodo;
                if ($saldo < $amortizacion) {
                    $amortizacion = $saldo;
                    $cuota_real = $amortizacion + $interesPeriodo;
                }
                $saldo -= $amortizacion;

                $cuotas[] = [
                    // La numeración se ajusta: si la cuota actual pagada es $numero_cuota, la primera pendiente es $numero_cuota + 1
                    'numero_cuota' => $numero_cuota + $j + 1,
                    'cuota'        => round($cuota_real, 2),
                    'capital'      => round($amortizacion, 2),
                    'interes'      => round($interesPeriodo, 2),
                    'saldo_deuda'  => round($saldo, 2),
                    'fecha_pago'   => $fechaInicio->copy()->addDays(($intervalDays *  ($j + 1)))->format('Y-m-d')
                ];
            }
        }
        if ($opcion == 'reducir_plazo') {
            // Se mantiene la cuota fija de la primera cuota pendiente y se recalcula el número de periodos necesarios
            $query = Cronograma::where('id_prestamo', $prestamo_id)
                ->where('numero', '>=', $numero_cuota);
            if ($categoria_c != 'grupal') {
                $query->whereNotNull('cliente_id');
            } else {
                $query->whereNull('cliente_id');
            }
            $firstPending = $query->orderBy('numero', 'asc')->first();

            if (!$firstPending) {
                return response()->json(['error' => 'No quedan cuotas pendientes.'], 400);
            }

            $cuotaFija = $firstPending->monto;

            if ($cuotaFija * $i >= $nuevoPrincipal) {
                $n = 1;
            } else {
                $n = -log(1 - ($nuevoPrincipal * $i / $cuotaFija)) / log(1 + $i);
                $n = ceil($n);
            }
            $saldo = $nuevoPrincipal;
            for ($j = 0; $j < $n; $j++) {
                $interesPeriodo = $saldo * $i;
                $amortizacion = $cuotaFija - $interesPeriodo;
                if ($saldo < $amortizacion) {
                    $amortizacion = $saldo;
                    $cuotaFija = $amortizacion + $interesPeriodo;
                }
                $saldo -= $amortizacion;
                $cuotas[] = [
                    'numero_cuota' => $numero_cuota + $j + 1,
                    'cuota'        => round($cuotaFija, 2),
                    'capital'      => round($amortizacion, 2),
                    'interes'      => round($interesPeriodo, 2),
                    'saldo_deuda'  => round($saldo, 2),
                    'fecha_pago'   => $fechaInicio->copy()->addDays(($intervalDays *  ($j + 1)))->format('Y-m-d')
                ];
            }
        }

        return response()->json([
            'cuotas' => $cuotas,
            'nuevo_principal' => round($nuevoPrincipal, 2)
        ]);
    }

    public function amortizarCapital(Request $request)
    {
        $user = auth()->user();

        // Verificar que exista una caja abierta
        $ultimaTransaccion = \App\Models\CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ultimaTransaccion) {
            return response()->json(['error' => 'No hay una caja abierta para el usuario actual'], 400);
        }

        $prestamo_id = $request->prestamo_id;
        $credito = Credito::find($prestamo_id);
        if (!$credito) {
            return response()->json(['error' => 'Crédito no encontrado.'], 404);
        }
        $opcion = $request->opcion;                   // 'reducir_cuota' o 'reducir_plazo'
        $numero_cuota = $request->numero_cuota;         // Número de la cuota actual (ya pagada)
        $intereses_a_pagar = $request->intereses_a_pagar;       // Total de intereses de la cuota actual
        $capital_pendiente = $request->capital_pendiente;       // Total de capital pendiente de la cuota actual
        $total_a_pagar_posible = $request->total_a_pagar_posible; // Suma de ambos (normalmente = intereses + capital)
        $monto_pago_capital = $request->monto_pago_capital;       // Abono a capital

        $categoria_c = $credito->categoria;

        $nuevoPrincipal = $total_a_pagar_posible - $monto_pago_capital;

        $ingresoGeneral = \App\Models\Ingreso::create([
            'transaccion_id'           => $ultimaTransaccion->id,
            'prestamo_id'              => $prestamo_id,
            'cliente_id'               => ($categoria_c == 'grupal') ? null : $credito->clientes()->first()->id,
            'cronograma_id'            => null,
            'numero_cuota'             => $numero_cuota,
            'monto'                    => $monto_pago_capital, // monto total pagado (intereses + capital)
            'monto_mora'               => 0,
            'dias_mora'                => 0,
            'porcentaje_mora'          => 0,
            'fecha_pago'               => Carbon::now()->toDateString(),
            'hora_pago'                => Carbon::now()->toTimeString(),
            'sucursal_id'              => $user->sucursal_id,
            'monto_total_pago_final'   => $monto_pago_capital,
            'interes_pago_capital'       => $intereses_a_pagar,
            'pago_capital'             => $opcion == 'reducir_cuota' ? 1 : 2,

        ]);

        $cronograma = \App\Models\Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', $numero_cuota)
            ->where('cliente_id', $ingresoGeneral->cliente_id)
            ->first();

        $cronograma->monto_capital = $monto_pago_capital - $intereses_a_pagar;
        $cronograma->intereses_capital = $intereses_a_pagar;
        $cronograma->pago_capital = $opcion == 'reducir_cuota' ? 1 : 2;
        $cronograma->nuevo_saldo_deuda = $nuevoPrincipal;
        $cronograma->save();
        $ingresoGeneral->cronograma_id = $cronograma->id;
        $ingresoGeneral->save();

        if ($categoria_c == 'grupal') {

            $totalIndividual = $credito->creditoClientes()->sum('monto_indivual');
            foreach ($credito->creditoClientes as $cc) {
                $proporcion = ($totalIndividual > 0) ? $cc->monto_indivual / $totalIndividual : 0;
                $interes_individual = round($intereses_a_pagar * $proporcion, 2);
                $capital_individual = round($monto_pago_capital * $proporcion, 2);
                $total_individual = $interes_individual + $capital_individual;

                $ingresoindividual = \App\Models\Ingreso::create([
                    'transaccion_id'           => $ultimaTransaccion->id,
                    'prestamo_id'              => $prestamo_id,
                    'cliente_id'               => $cc->cliente_id,
                    'cronograma_id'            => null,
                    'numero_cuota'             => $numero_cuota,
                    'monto'                    => $total_individual,
                    'monto_mora'               => 0,
                    'dias_mora'                => 0,
                    'porcentaje_mora'          => 0,
                    'fecha_pago'               => Carbon::now()->toDateString(),
                    'hora_pago'                => Carbon::now()->toTimeString(),
                    'sucursal_id'              => $user->sucursal_id,
                    'monto_total_pago_final'   => $total_individual,

                    'interes_pago_capital'       => $interes_individual,
                    'pago_capital'             => $opcion == 'reducir_cuota' ? 1 : 2,
                ]);

                $cronograma = \App\Models\Cronograma::where('id_prestamo', $prestamo_id)
                    ->where('numero', $numero_cuota)
                    ->where('cliente_id', $cc->cliente_id)
                    ->first();

                $cronograma->monto_capital =  $capital_individual - $interes_individual;
                $cronograma->intereses_capital = $interes_individual;
                $cronograma->pago_capital = $opcion == 'reducir_cuota' ? 1 : 2;
                $cronograma->nuevo_saldo_deuda = ($cronograma->saldo_deuda + $cronograma->amortizacion) - $cronograma->monto_capital;
                $cronograma->save();

                $ingresoindividual->cronograma_id = $cronograma->id;
                $ingresoindividual->save();
            }
        }
        $resultado = $this->generarNuevoCronogramaInterno(
            $prestamo_id,
            $opcion,
            $numero_cuota,
            $intereses_a_pagar,
            $capital_pendiente,
            $total_a_pagar_posible,
            $monto_pago_capital
        );

        return response()->json([
            'success' => 'Amortización al capital realizada con éxito.',
            'cronograma' => $resultado ?? ''
        ]);
    }
    protected function generarNuevoCronogramaInterno(
        $prestamo_id,
        $opcion,
        $numero_cuota,
        $intereses_a_pagar,
        $capital_pendiente,
        $total_a_pagar_posible,
        $monto_pago_capital
    ) {
        $credito = Credito::find($prestamo_id);
        $categoria_c = $credito->categoria;
        $frecuencia = $credito->recurrencia;
        $tea = $credito->tasa;

        // Nuevo principal calculado
        $nuevoPrincipal = $total_a_pagar_posible - $monto_pago_capital;

        // Cuotas pendientes a partir de la cuota actual (excluyendo la cuota pagada)
        $pendingQuery = \App\Models\Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>', $numero_cuota);
        if ($categoria_c != 'grupal') {
            $pendingQuery->whereNotNull('cliente_id');
        } else {
            $pendingQuery->whereNull('cliente_id');
        }
        $remainingPeriods = $pendingQuery->count();
        if ($remainingPeriods <= 0) {
            // Si no quedan cuotas, asumimos al menos 1 período nuevo
            $remainingPeriods = 1;
        }

        // Definir número de periodos por año y el intervalo en días según la recurrencia
        $intervalDays = 30;
        switch ($frecuencia) {
            case 'catorcenal':
                $nPeriodsPerYear = 26;
                $intervalDays = 14;
                break;
            case 'quincenal':
                $nPeriodsPerYear = 24;
                $intervalDays = 15;
                break;
            case 'veinteochenal':
                $nPeriodsPerYear = 12;
                $intervalDays = 28;
                break;
            case 'semestral':
                $nPeriodsPerYear = 2;
                $intervalDays = 182;
                break;
            case 'anual':
                $nPeriodsPerYear = 1;
                $intervalDays = 365;
                break;
            case 'mensual':
            default:
                $nPeriodsPerYear = 12;
                $intervalDays = 30;
                break;
        }
        $i = pow(1 + $tea / 100, 1 / $nPeriodsPerYear) - 1;
        $tasaDiaria = pow(1 + ($tea / 100), 1 / 360) - 1;
        $cuotas = [];
        $periodoGraciaDias = $credito->periodo_gracia_dias ?? 0;
        $fechaInicio = Carbon::now()->addDays($periodoGraciaDias);

        $interesesPeriodoGracia = $nuevoPrincipal * $tasaDiaria * $periodoGraciaDias;
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $remainingPeriods;
        $cuotas = [];

        if ($opcion == 'reducir_cuota') {
            $n = $remainingPeriods;
            $cuota = ($nuevoPrincipal * $i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1);
            if ($credito->producto == 'grupal') {
                $cuota_real = $cuota + 0.021 * $cuota;
            } else if ($periodoGraciaDias > 0) {
                $cuota_real = $cuota + $interesesMensualesPorGracia + 0.011 * $cuota;
            } else {
                $cuota_real = $cuota;
            }
            $saldo = $nuevoPrincipal;
            for ($j = 0; $j < $n; $j++) {
                $interesPeriodo = $saldo * $i;
                $amortizacion = $cuota_real - $interesPeriodo;
                if ($saldo < $amortizacion) {
                    $amortizacion = $saldo;
                    $cuota_real = $amortizacion + $interesPeriodo;
                }
                $saldo -= $amortizacion;
                $cuotas[] = [
                    'numero_cuota' => $numero_cuota + $j + 1,
                    'cuota'        => round($cuota_real, 2),
                    'capital'      => round($amortizacion, 2),
                    'interes'      => round($interesPeriodo, 2),
                    'saldo_deuda'  => round($saldo, 2),
                    'fecha_pago'   => $fechaInicio->copy()->addDays($intervalDays * ($j + 1))->format('Y-m-d')
                ];
            }
        } else if ($opcion == 'reducir_plazo') {
            $query = \App\Models\Cronograma::where('id_prestamo', $prestamo_id)
                ->where('numero', '>=', $numero_cuota);
            if ($categoria_c != 'grupal') {
                $query->whereNotNull('cliente_id');
            } else {
                $query->whereNull('cliente_id');
            }
            $firstPending = $query->orderBy('numero', 'asc')->first();
            if (!$firstPending) {
                return response()->json(['error' => 'No quedan cuotas pendientes.'], 400);
            }
            $cuotaFija = $firstPending->monto;
            if ($cuotaFija * $i >= $nuevoPrincipal) {
                $n = 1;
            } else {
                $n = -log(1 - ($nuevoPrincipal * $i / $cuotaFija)) / log(1 + $i);
                $n = ceil($n);
            }
            $saldo = $nuevoPrincipal;
            for ($j = 0; $j < $n; $j++) {
                $interesPeriodo = $saldo * $i;
                $amortizacion = $cuotaFija - $interesPeriodo;
                if ($saldo < $amortizacion) {
                    $amortizacion = $saldo;
                    $cuotaFija = $amortizacion + $interesPeriodo;
                }
                $saldo -= $amortizacion;
                $cuotas[] = [
                    'numero_cuota' => $numero_cuota + $j + 1,
                    'cuota'        => round($cuotaFija, 2),
                    'capital'      => round($amortizacion, 2),
                    'interes'      => round($interesPeriodo, 2),
                    'saldo_deuda'  => round($saldo, 2),
                    'fecha_pago'   => $fechaInicio->copy()->addDays($intervalDays * ($j + 1))->format('Y-m-d')
                ];
            }
            if ($saldo > 0.01) {
                $interesExtra = $saldo * $i;
                $cuotaExtra = $saldo + $interesExtra;
                $cuotas[] = [
                    'numero_cuota' => $numero_cuota + $n + 1,
                    'cuota'        => round($cuotaExtra, 2),
                    'capital'      => round($saldo, 2),
                    'interes'      => round($interesExtra, 2),
                    'saldo_deuda'  => 0,
                    'fecha_pago'   => $fechaInicio->copy()->addDays($intervalDays * ($n + 1))->format('Y-m-d')
                ];
                $n++;
                $saldo = 0;
            }
        }
        \App\Models\Cronograma::where('id_prestamo', $prestamo_id)
            ->where('numero', '>', $numero_cuota)
            ->delete();

        if ($categoria_c != 'grupal') {
            $cliente = $credito->clientes()->first();
            foreach ($cuotas as $c) {
                $cronograma = new \App\Models\Cronograma();
                $cronograma->fecha = $c['fecha_pago'];
                $cronograma->monto = $c['cuota'];
                $cronograma->capital = $c['capital'];
                $cronograma->interes = $c['interes'];
                $cronograma->amortizacion = $c['capital'];
                $cronograma->saldo_deuda = $c['saldo_deuda'];
                $cronograma->numero = $c['numero_cuota'];
                $cronograma->id_prestamo = $prestamo_id;
                $cronograma->cliente_id = $cliente ? $cliente->id : null;
                $cronograma->save();
            }
        } else {
            // Para crédito grupal, insertar el cronograma general
            foreach ($cuotas as $c) {
                $cronograma = new \App\Models\Cronograma();
                $cronograma->fecha = $c['fecha_pago'];
                $cronograma->monto = $c['cuota'];
                $cronograma->capital = $c['capital'];
                $cronograma->interes = $c['interes'];
                $cronograma->amortizacion = $c['capital'];
                $cronograma->saldo_deuda = $c['saldo_deuda'];
                $cronograma->numero = $c['numero_cuota'];
                $cronograma->id_prestamo = $prestamo_id;
                $cronograma->cliente_id = null;
                $cronograma->save();
            }
            $totalIndividual = $credito->creditoClientes()->sum('monto_indivual');
            foreach ($credito->creditoClientes as $cc) {
                $proporcion = $totalIndividual > 0 ? $cc->monto_indivual / $totalIndividual : 0;
                foreach ($cuotas as $c) {
                    $cronograma = new \App\Models\Cronograma();
                    $cronograma->fecha = $c['fecha_pago'];
                    $cronograma->monto = round($c['cuota'] * $proporcion, 2);
                    $cronograma->capital = round($c['capital'] * $proporcion, 2);
                    $cronograma->interes = round($c['interes'] * $proporcion, 2);
                    $cronograma->amortizacion = round($c['capital'] * $proporcion, 2);
                    $cronograma->saldo_deuda = round($c['saldo_deuda'] * $proporcion, 2);
                    $cronograma->numero = $c['numero_cuota'];
                    $cronograma->id_prestamo = $prestamo_id;
                    $cronograma->cliente_id = $cc->cliente_id;
                    $cronograma->save();
                }
            }
        }

        return [
            'success' => 'Nuevo cronograma generado y almacenado correctamente.',
            'cuotas' => $cuotas,
            'nuevo_principal' => round($nuevoPrincipal, 2)
        ];
    }

    public function generarreprogramacion(Request $request)
    {
        $repId       = $request->reprogramacionId;
        $rep         = Reprogramacion::findOrFail($repId);
        $credito     = Credito::findOrFail($rep->credito_id);
        $categoria   = $credito->categoria;
        $frecuencia  = $credito->recurrencia;
        $tea         = $rep->tasa_interes;

        // 1) Identificar primera cuota no pagada
        $primera = Cronograma::where('id_prestamo', $credito->id)
            ->whereNotIn('id', function ($q) {
                $q->select('cronograma_id')->from('ingresos');
            })
            ->orderBy('numero')
            ->first();
        $startNum = $primera->numero;

        // 2) Calcular nuevo principal
        $nuevoPrincipal = $rep->interes_restante + $rep->capital_restante;


        // 3) Contar cuántas cuotas quedan
        // $pending = Cronograma::where('id_prestamo', $credito->id)
        //     ->where('numero', '>=', $startNum);

        // if ($categoria !== 'grupal') {
        //     $pending->whereNotNull('cliente_id');
        // } else {
        //     $pending->whereNull('cliente_id');
        // }
        // $n = $pending->count() ?: 1;
        $n = $rep->nuevo_numero_cuotas;

        // 4) Determinar i e intervalDays según recurrencia
        switch ($frecuencia) {
            case 'catorcenal':
                $nPerYear = 26;
                $intervalDays = 14;
                break;
            case 'quincenal':
                $nPerYear = 24;
                $intervalDays = 15;
                break;
            case 'veinteochenal':
                $nPerYear = 12;
                $intervalDays = 28;
                break;
            case 'semestral':
                $nPerYear = 2;
                $intervalDays = 182;
                break;
            case 'anual':
                $nPerYear = 1;
                $intervalDays = 365;
                break;
            case 'mensual':
            default:
                $nPerYear = 12;
                $intervalDays = 30;
                break;
        }

        $i            = pow(1 + $tea / 100, 1 / $nPerYear) - 1;
        $fechaInicio  = Carbon::parse($rep->fecha_reprogramar);

        // 5) Borrar cuotas pendientes
        Cronograma::where('id_prestamo', $credito->id)
            ->where('numero', '>=', $startNum)
            ->delete();

        // 6) Generar arreglo de cuotas usando tu lógica original
        // 6) Generar cuotas usando calcularCuota()
        $interesesMensualesPorGracia = 0; // como no quieres gracia
        $tipoProducto                = $credito->producto; // 'grupal' o 'individual'

        // Llamada única a tu función de amortización “francesa”
        $raw = $this->calcularCuota(
            $nuevoPrincipal,
            $tea,
            $n,
            $frecuencia,
            $interesesMensualesPorGracia,
            $tipoProducto
        );

        // Ajustar números de cuota y fechas
        $cuotas = [];
        foreach ($raw as $idx => $c) {
            $cuotas[] = [
                'numero_cuota' => $startNum + $idx,
                'cuota'        => $c['cuota'],
                'capital'      => $c['amortizacion'],
                'interes'      => $c['interes'],
                'saldo_deuda'  => $c['saldo_deuda'],
                'fecha_pago'   => Carbon::parse($rep->fecha_reprogramar)
                    ->addDays($intervalDays * $idx)
                    ->toDateString(),
            ];
        }

        // 7) Insertar nuevas cuotas
        if ($categoria !== 'grupal') {
            $cliente = $credito->clientes()->first();
            foreach ($cuotas as $c) {
                Cronograma::create([
                    'id_prestamo'  => $credito->id,
                    'cliente_id'   => $cliente ? $cliente->id : null,
                    'numero'       => $c['numero_cuota'],
                    'fecha'        => $c['fecha_pago'],
                    'monto'        => $c['cuota'],
                    'capital'      => $c['capital'],
                    'interes'      => $c['interes'],
                    'amortizacion' => $c['capital'],
                    'saldo_deuda'  => $c['saldo_deuda'],
                ]);
            }
        } else {
            // Cronograma general
            foreach ($cuotas as $c) {
                Cronograma::create([
                    'id_prestamo'  => $credito->id,
                    'cliente_id'   => null,
                    'numero'       => $c['numero_cuota'],
                    'fecha'        => $c['fecha_pago'],
                    'monto'        => $c['cuota'],
                    'capital'      => $c['capital'],
                    'interes'      => $c['interes'],
                    'amortizacion' => $c['capital'],
                    'saldo_deuda'  => $c['saldo_deuda'],
                ]);
            }
            // Desglose por cliente
            $totalInd = $credito->creditoClientes()->sum('monto_indivual');
            foreach ($credito->creditoClientes as $cc) {
                $prop = $totalInd > 0 ? $cc->monto_indivual / $totalInd : 0;
                foreach ($cuotas as $c) {
                    Cronograma::create([
                        'id_prestamo'  => $credito->id,
                        'cliente_id'   => $cc->cliente_id,
                        'numero'       => $c['numero_cuota'],
                        'fecha'        => $c['fecha_pago'],
                        'monto'        => round($c['cuota'] * $prop, 2),
                        'capital'      => round($c['capital'] * $prop, 2),
                        'interes'      => round($c['interes'] * $prop, 2),
                        'amortizacion' => round($c['capital'] * $prop, 2),
                        'saldo_deuda'  => round($c['saldo_deuda'] * $prop, 2),
                    ]);
                }
            }
        }

        // 8) Actualizar reprogramación
        $rep->estado = 'generado';
        $rep->save();


        return response()->json([
            'success'         => 'Cronograma reprogramado correctamente.',
            'prestamo_id'     => $credito->id,
        ]);
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
        } else {
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
            // Filtrar todas las cuotas vencidas del cronograma
            $cuotasVencidas = $credito->cronograma->filter(function ($cuota) use ($today) {
                return $cuota->fecha < $today && $cuota->ingresos->isEmpty();
            });

            if ($cuotasVencidas->isNotEmpty()) {
                return [
                    'id'              => $credito->id,
                    'nombre_cliente'  => $credito->producto == 'grupal'
                        ? $credito->nombre_prestamo
                        : $credito->clientes->first()->nombre,
                    'producto'        => $credito->producto,
                    // Se unen los números de cuota separados por coma
                    'cuota'           => $cuotasVencidas->pluck('numero')->implode(', '),
                    // Se unen las fechas; si deseas formatear la fecha, podrías aplicar un ->map()
                    'fecha'           => $cuotasVencidas->pluck('fecha')->implode(', '),
                    // Se calculan y unen los días de atraso
                    'dias_de_atraso'  => $cuotasVencidas->map(function ($cuota) {
                        return Carbon::now()->diffInDays(Carbon::parse($cuota->fecha));
                    })->implode(', '),
                ];
            }
            return null;
        })->filter();


        return view('admin.cobranza.carta',  compact('result'));
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
        foreach (
            [
                'clientesArray',
                'proyeccionesArray',
                'inventarioArray',
                'deudasFinancierasArray',
                'gastosOperativosArray',
                'boletasArray',
                'gastosProducirArray',
                'inventarioArray1',
                'ventasdiarias',
                'inventarioprocesoArray',
                'ventasMensualesArray',
                'tipoProductoArray',
                'gastosAgricolaArray',
                'inventarioMaterialArray'
            ] as $key
        ) {
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
        $credito = Credito::find($id);
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
        $credito = \App\Models\Credito::findOrFail($id);

        // Si es CrediJoya -> ir a su vista de edición
        if ($credito->producto === 'individual' && $credito->subproducto === 'credijoya' || $credito->categoria === 'credijoya') {
            $cliente = optional($credito->clientes)->first(); // relación many-to-many
            $joyas   = \App\Models\CredijoyaJoya::where('prestamo_id', $credito->id)
                ->get(['id', 'kilate as kilataje', 'precio_gramo', 'peso_bruto', 'peso_neto', 'piezas', 'descripcion', 'valor_tasacion', 'codigo']);

            // totales de referencia (recalcular también en el front)
            $tasacion_total = (float) $joyas->sum('valor_tasacion');
            $monto_max_80   = round($tasacion_total * 0.80, 2);

            return view('admin.creditos.editcredijoya', compact(
                'id',
                'credito',
                'cliente',
                'joyas',
                'tasacion_total',
                'monto_max_80'
            ));
        }

        // Ramas existentes
        $tipo       = $credito->tipo;
        $producto   = $credito->producto;
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

        abort(404);
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
        $credito = Credito::find($id);

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
        $credito = Credito::findOrFail($id); // Buscar el crédito por ID
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
