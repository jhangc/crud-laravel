<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class PdfController extends Controller
{
    public function generatePDF(Request $request)
    {
        // Obtener el ID del préstamo desde la solicitud
        $ids = $request->id;
        $modulo = 'aprobar'; // Asignar el parámetro 'modulo' de la URL

        // Obtener los datos necesarios utilizando el ID del préstamo
        $prestamo = \App\Models\Credito::find($ids);
        $proyecciones = \App\Models\ProyeccionesVentas::where('id_prestamo', $ids)->get();
        $deudas = \App\Models\DeudasFinancieras::where('prestamo_id', $ids)->get();
        $gastosOperativos = \App\Models\GastosOperativos::where('id_prestamo', $ids)->get();
        $inventario = \App\Models\Inventario::where('id_prestamo', $ids)->get();
        $boletas = \App\Models\Boleta::where('id_prestamo', $ids)->get();
        $gastosProducir = \App\Models\GastosProducir::where('id_prestamo', $ids)->get();
        $garantias = \App\Models\Garantia::where('id_prestamo', $ids)->get();
        $gastosfamiliares = \App\Models\GastosFamiliares::where('id_prestamo', $ids)->get();
        $activos = \App\Models\Activos::where('prestamo_id', $ids)->first();
        $ventasdiarias = \App\Models\VentasDiarias::where('prestamo_id', $ids)->get();
        $cuotas = \App\Models\Cronograma::where('id_prestamo', $ids)->first();

        $inventarioterminado = \App\Models\Inventario::where('id_prestamo', $ids)
            ->where('tipo_inventario', 1)
            ->get();

        $inventarioproceso = \App\Models\Inventario::where('id_prestamo', $ids)
            ->where('tipo_inventario', 2)
            ->get();

        $inventariomateriales = \App\Models\Inventario::where('id_prestamo', $ids)
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

        return Pdf::loadView('pdf')
        // ->setPaper('a4', 'landscape')
        ->stream('ticket.pdf');
        
    }
}
