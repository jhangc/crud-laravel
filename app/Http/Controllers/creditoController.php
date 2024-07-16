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

        $prestamo = \App\Models\credito::find($id);
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
        $cuotas = \App\Models\Cronograma::where('id_prestamo', $id)->first();
        $cuotastodas = \App\Models\Cronograma::where('id_prestamo', $id)->get();
        $inventarioterminado = \App\Models\Inventario::where('id_prestamo', $id)
            ->where('tipo_inventario', 1)
            ->get();

        $inventarioproceso = \App\Models\Inventario::where('id_prestamo', $id)
            ->where('tipo_inventario', 2)
            ->get();

        $inventariomateriales = \App\Models\Inventario::where('id_prestamo', $id)
            ->where('tipo_inventario', 3)
            ->get();

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
                    $margenventas = ($margenmanual->margen_utilidad) * 100;

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

                        $margenventas = ($margenmanual->margen_utilidad) * 100;

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

                        $margenventas = ($margenmanual->margen_utilidad) * 100;

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

                        $margenventas = ($margenmanual->margen_utilidad) * 100;

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
    
        // Obtener ingresos y egresos de la caja abierta
        $ingresos = $cajaAbierta->cantidad_ingresos;
        $egresos = $cajaAbierta->cantidad_egresos;
        $montoApertura = $cajaAbierta->monto_apertura;
    
        return view('admin.caja.arqueo', compact('cajaAbierta', 'ingresos', 'egresos', 'montoApertura'));
    }
    

    public function viewHabilitarCaja()
    {
        $user = Auth::user();
        $sucursal_id = $user->sucursal_id;
        $cajas = Caja::where('sucursal_id', $sucursal_id)
                     ->whereDoesntHave('transacciones', function($query) {
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
    public function guardarArqueo(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:caja_transacciones,id',
            'monto_apertura' => 'required|numeric',
            'billete_200' => 'required|integer|min:0',
            'billete_100' => 'required|integer|min:0',
            'billete_50' => 'required|integer|min:0',
            'billete_20' => 'required|integer|min:0',
            'billete_10' => 'required|integer|min:0',
            'moneda_5' => 'required|integer|min:0',
            'moneda_2' => 'required|integer|min:0',
            'moneda_1' => 'required|integer|min:0',
            'moneda_0_5' => 'required|integer|min:0',
            'moneda_0_2' => 'required|integer|min:0',
            'moneda_0_1' => 'required|integer|min:0',
            'depositos' => 'required|numeric|min:0',
            'saldo_final' => 'required|regex:/^S\/\. \d+(\.\d{1,2})?$/',
        ]);
    
        $billetes = [
            200 => $request->input('billete_200'),
            100 => $request->input('billete_100'),
            50 => $request->input('billete_50'),
            20 => $request->input('billete_20'),
            10 => $request->input('billete_10')
        ];
    
        $monedas = [
            5 => $request->input('moneda_5'),
            2 => $request->input('moneda_2'),
            1 => $request->input('moneda_1'),
            0.5 => $request->input('moneda_0_5'),
            0.2 => $request->input('moneda_0_2'),
            0.1 => $request->input('moneda_0_1')
        ];
    
        $totalEfectivo = array_sum(array_map(function($billete, $cantidad) {
            return $billete * $cantidad;
        }, array_keys($billetes), $billetes)) + array_sum(array_map(function($moneda, $cantidad) {
            return $moneda * $cantidad;
        }, array_keys($monedas), $monedas));
    
        $depositos = $request->input('depositos');
        $saldoFinal = floatval(str_replace('S/. ', '', $request->input('saldo_final')));
    
        $transaccion = CajaTransaccion::find($request->input('caja_id'));
        $transaccion->json_cierre = json_encode([
            'billetes' => $billetes,
            'monedas' => $monedas,
            'depositos' => $depositos
        ]);
        $transaccion->monto_cierre = $totalEfectivo;
        $transaccion->hora_cierre = now();
        $transaccion->fecha_cierre = now();
        $transaccion->save();
    
        return response()->json(['success' => true]);
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
        return view('admin.cobranza.carta');
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

        // Calcular los intereses del período de gracia
        $tasaDiaria = pow(1 + ($tasaInteres / 100), 1 / 360) - 1;
        $interesesPeriodoGracia = $monto * $tasaDiaria * $request->periodo_gracia_dias;
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $tiempo;

        $cuotas = $this->calcularCuota($monto, $tasaInteres, $tiempo, $frecuencia);

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
                case 'mensual':
                default:
                    $fechaCuota->addMonth();
                    break;
            }

            $cronograma = new Cronograma();
            $cronograma->fecha = $fechaCuota->copy();
            $cronograma->monto = $cuota['cuota'] + $interesesMensualesPorGracia + 0.021 * $cuota['cuota']; // Cuota fija más intereses distribuidos y otros componentes
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
                    'precio' =>  !empty($inventarioData['precio_unitario']) ?? 0,
                    'porcentaje' => !empty($inventarioData['procentaje_producto']) ?? 0,
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


    public function calcularCuota($monto, $tea, $periodos, $frecuencia)
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
            case 'mensual':
            default:
                $n = 12;
                break;
        }

        $tasaPeriodo = pow(1 + ($tea / 100), 1 / $n) - 1;
        $cuota = ($monto * $tasaPeriodo * pow(1 + $tasaPeriodo, $periodos)) / (pow(1 + $tasaPeriodo, $periodos) - 1);

        $saldo = $monto;
        $cuotas = [];

        for ($i = 0; $i < $periodos; $i++) {
            $interesPeriodo = $saldo * $tasaPeriodo;
            $amortizacion = $cuota - $interesPeriodo;
            $saldo -= $amortizacion;

            $cuotas[] = [
                'numero_cuota' => $i + 1,
                'capital' => round($saldo, 2),
                'interes' => round($interesPeriodo, 2),
                'amortizacion' => round($amortizacion, 2),
                'cuota' => round($cuota, 2),
                'saldo_deuda' => round($saldo, 2)
            ];
        }

        return $cuotas;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $credito = \App\Models\credito::find($id);
        $garantia = \App\Models\Garantia::where('id_prestamo', $id)->first();
        $clientes = \App\Models\CreditoCliente::with('clientes')->where('prestamo_id', $id)->get();
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
