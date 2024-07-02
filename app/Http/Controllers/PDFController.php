<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // public function generatePDF(Request $request)
    // {
    //     return Pdf::loadView('pdf')
    //     // ->setPaper('a4', 'landscape')
    //     ->stream('ticket.pdf');
    // }

    public function generatecronogramaPDF(Request $request, $id)
    {
        $modulo = $request->query('modulo'); // Obtener el parámetro 'modulo' de la URL
        $prestamo = \App\Models\credito::find($id);
        $cuotas = \App\Models\Cronograma::where('id_prestamo', $id)->first();

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

        $tipo = $prestamo->tipo;

        $comentarioasesor = $prestamo->comentario_asesor;
        $comentarioadministrador = $prestamo->comentario_administrador;

        // Calcular Totales
        $factorsemana = 15 / 7;
        $factormes = $factorsemana * 2;

        $totalprestamo = $prestamo->monto_total;
        $cuotaprestamo = $cuotas->monto;

        $estado = $prestamo->estado;

        $data = compact(
            'prestamo',
            'cliente',
            'responsable',
        );

        // return view('pdf.cronogramaindividual');

        // Generar y retornar el PDF
        // $pdf = Pdf::loadView('pdf.cronogramaindividual', $data);
        // return $pdf->stream('ticket.pdf');

        $pdf = Pdf::loadView('pdf.cronogramaindividual', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('ticket.pdf');
    }

    public function generatecronogramagrupalPDF(Request $request, $id)
    {
        $modulo = $request->query('modulo'); // Obtener el parámetro 'modulo' de la URL
        $prestamo = \App\Models\credito::find($id);
        $cuotas = \App\Models\Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = \App\Models\CreditoCliente::where('prestamo_id', $id)->get();

        $responsable = auth()->user();



        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente'
        );

        // return view('pdf.cronogramaindividual');

        // Generar y retornar el PDF
        $pdf = Pdf::loadView('pdf.cronogramagrupal', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('ticket.pdf');

        // $pdf = Pdf::loadView('pdf.cronogramagrupal', $data)->setPaper('a4', 'landscape');
        // return $pdf->stream('ticket.pdf');
    }

    public function generatecrontratogrupalPDF(Request $request, $id)
    {
        $prestamo = \App\Models\credito::find($id);
        $cuotas = \App\Models\Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = \App\Models\CreditoCliente::where('prestamo_id', $id)->get();
        $responsable = auth()->user();

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente'
        );

        // Generar y retornar el PDF
        $pdf = Pdf::loadView('pdf.contratogrupal', $data);
        return $pdf->stream('ticket.pdf');

        // $pdf = Pdf::loadView('pdf.cronogramagrupal', $data)->setPaper('a4', 'landscape');
        // return $pdf->stream('ticket.pdf');
    }

    public function generatecartillaPDF(Request $request, $id)
    {
        $prestamo = \App\Models\credito::find($id);
        $cuotas = \App\Models\Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = \App\Models\CreditoCliente::where('prestamo_id', $id)->get();
        $responsable = auth()->user();

        $date = \Carbon\Carbon::now();

        $formattedDate = $date->formatLocalized('%d DE %B DEL %Y');

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente',
            'formattedDate'
        );

        // Generar y retornar el PDF
        $pdf = Pdf::loadView('pdf.cartilla', $data)->setPaper('a4');
        return $pdf->stream('ticket.pdf');

        // $pdf = Pdf::loadView('pdf.cronogramagrupal', $data)->setPaper('a4', 'landscape');
        // return $pdf->stream('ticket.pdf');
    }


    public function generatePDF(Request $request, $id)
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

        $tipo = $prestamo->tipo;

        $comentarioasesor = $prestamo->comentario_asesor;
        $comentarioadministrador = $prestamo->comentario_administrador;

        // Calcular Totales
        $factorsemana = 15 / 7;
        $factormes = $factorsemana * 2;

        $totalprestamo = $prestamo->monto_total;
        $cuotaprestamo = $cuotas->monto;

        $estado = $prestamo->estado;

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

                $data = compact(
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
                );

                // Generar y retornar el PDF
                $pdf = Pdf::loadView('pdf.evaluacionmicroempresacomercio', $data);
                return $pdf->stream('ticket.pdf');
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

                    $data = compact(
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
                    );

                    // Generar y retornar el PDF
                    $pdf = Pdf::loadView('pdf.evaluacionserviciomicroempresa', $data);
                    return $pdf->stream('ticket.pdf');
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

                    $margenventas = $margenmanual->margen_utilidad;

                    $liquidez = $pasivo != 0 ? round(($activo_corriente / $pasivo), 2) : 0;

                    $solvencia = $patrimonioneto != 0 ? round(($pasivo / $patrimonioneto), 2) : 0;

                    $capital_trabajo = $activo_corriente - $pasivo;
                    $indice_endeudamiento = $totalactivo != 0 ? round(($pasivo / $totalactivo), 2) : 0;

                    $cuotaendeudamiento = $saldo_final - $totalcuotadeuda;
                    $cuotaexcedente = $saldo_final != 0 ? round(($cuotaprestamo / $saldo_final), 2) : 0;

                    $data = compact(
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
                    );

                    // Generar y retornar el PDF
                    $pdf = Pdf::loadView('pdf.evaluacionservicioconsumo', $data);
                    return $pdf->stream('ticket.pdf');
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

                    $data = compact(
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
                    );
                    // Generar y retornar el PDF
                    $pdf = Pdf::loadView('pdf.produccionempresarial', $data);
                    return $pdf->stream('ticket.pdf');
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

                    $data = compact(
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
                    );

                    // Generar y retornar el PDF
                    $pdf = Pdf::loadView('pdf.evaluacionproduccionagricola', $data);
                    return $pdf->stream('ticket.pdf');
                }
        }
    }
    public function generateticket($id) {
        $prestamo = \App\Models\credito::find($id);
        $creditos = \App\Models\CreditoCliente::with('clientes')->where('prestamo_id', $id)->get();
        $prestamo->estado='pagado';
        $prestamo->save();
        $pdf = Pdf::loadView('pdf.ticket', compact('prestamo', 'creditos'))
            ->setPaper([0, 0, 205, 800]);
    
        return $pdf->stream('ticket.pdf');
    }
}
