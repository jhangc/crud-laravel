<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Luecano\NumeroALetras\NumeroALetras;
use App\Models\CorrelativoPagare;

use App\Models\credito;
use App\Models\CreditoCliente;
use App\Models\Cronograma;
use App\Models\cliente;
use App\Models\CorrelativoCredito;


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
        $prestamo = credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->first();

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
        $prestamo = credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->get();
        $responsable = \App\Models\User::find($prestamo->user_id);

        $sucursal = \App\Models\Sucursal::first();

        // Formatear los datos adicionales necesarios
        foreach ($cuotas as $cuota) {
            $cuota->dias = (new \DateTime($cuota->fecha))->diff(new \DateTime($prestamo->fecha_desembolso))->days;
            $cuota->detalle = $cuota->numero == 0 ? 'Credito' : 'Saldo del Capital';
            $cuota->deuda = $prestamo->monto_total;
            $cuota->total = $cuota->monto; // Incluyendo cualquier otro componente necesario
        }

        // Calcular la suma de los intereses del cronograma grupal
        $totalInteresGrupal = $cuotas->whereNull('cliente_id')->sum('interes');
        $totalAmortizacionGrupal = $cuotas->where('cliente_id')->sum('amortizacion');
        $totalMontoGrupal = $cuotas->where('cliente_id')->sum('monto');

        // Calcular las sumas de los intereses para cada cliente
        $totalInteresesIndividuales = [];
        foreach ($prestamo->clientes as $cliente) {
            $totalInteresesIndividuales[$cliente->id] = $cuotas->where('cliente_id', $cliente->id)->sum('interes');
            $totalAmortizacionIndividuales[$cliente->id] = $cuotas->where('cliente_id', $cliente->id)->sum('amortizacion');
            $totalMontoIndividuales[$cliente->id] = $cuotas->where('cliente_id', $cliente->id)->sum('monto');
        }

        // Obtener correlativos generales y de los integrantes
        $correlativosGenerales = CorrelativoCredito::where('id_prestamo', $id)
            ->whereNull('id_cliente')
            ->first();

        $correlativosIntegrantes = CorrelativoCredito::where('id_prestamo', $id)
            ->whereNotNull('id_cliente')
            ->get();

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente',
            'totalInteresGrupal',
            'totalInteresesIndividuales',
            'totalAmortizacionIndividuales',
            'totalMontoIndividuales',
            'totalAmortizacionGrupal',
            'totalMontoGrupal',
            'sucursal',
            'correlativosGenerales',
            'correlativosIntegrantes'

        );

        $pdf = Pdf::loadView('pdf.cronogramagrupal', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('ticket.pdf');
    }

    public function generatecronogramaindividualPDF(Request $request, $id)
    {
        $prestamo = credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->get();
        $responsable = \App\Models\User::find($prestamo->user_id);
        $sucursal = \App\Models\Sucursal::first();

        // Calcular la tasa de interés por período
        $frecuencia = $prestamo->recurrencia; // Asegúrate de que la frecuencia esté definida en el modelo crédito
        $tasa_interes_anual = $prestamo->tasa; // La TEA está en el modelo crédito

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

        $tasa_interes_periodo = round((pow(1 + ($tasa_interes_anual / 100), 1 / $n) - 1) * 100, 2);

        // Formatear los datos adicionales necesarios
        foreach ($cuotas as $cuota) {
            $cuota->dias = (new \DateTime($cuota->fecha))->diff(new \DateTime($prestamo->fecha_desembolso))->days;
            $cuota->detalle = $cuota->numero == 0 ? 'Credito' : 'Saldo del Capital';
            $cuota->deuda = $prestamo->monto_total;
            $cuota->total = $cuota->monto; // Incluyendo cualquier otro componente necesario
        }

        // Calcular las sumas de los intereses para cada cliente
        $totalInteresesIndividuales = [];
        $totalAmortizacionIndividuales = [];
        $totalMontoIndividuales = [];
        foreach ($prestamo->clientes as $cliente) {
            $totalInteresesIndividuales[$cliente->id] = $cuotas->where('cliente_id', $cliente->id)->sum('interes');
            $totalAmortizacionIndividuales[$cliente->id] = $cuotas->where('cliente_id', $cliente->id)->sum('amortizacion');
            $totalMontoIndividuales[$cliente->id] = $cuotas->where('cliente_id', $cliente->id)->sum('monto');
        }

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente',
            'totalInteresesIndividuales',
            'totalAmortizacionIndividuales',
            'totalMontoIndividuales',
            'tasa_interes_periodo', // Pasar la tasa de interés por período a la vista
            'sucursal'
        );

        $pdf = Pdf::loadView('pdf.cronogramaindividual', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('cronogramaindividual.pdf');
    }



    public function generatecontratogrupalPDF(Request $request, $id)
    {
        $prestamo = credito::find($id);
        if (!$prestamo) {
            return response()->json(['error' => 'Crédito no encontrado'], 404);
        }


        CorrelativoCredito::generateCorrelativosGrupales($prestamo->id);

        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->with('clientes')->get();

        // Obtener correlativos generales y de los integrantes
        $correlativosGenerales = CorrelativoCredito::where('id_prestamo', $id)
            ->whereNull('id_cliente')
            ->first();

        // $correlativosIntegrantes = CorrelativoCredito::where('id_prestamo', $id)
        //     ->whereNotNull('id_cliente')
        //     ->get();

        $data = compact(
            'prestamo',
            'cuotas',
            'credito_cliente',
            'correlativosGenerales'
        );

        $pdf = PDF::loadView('pdf.contratogrupal', $data)->setPaper('a4');
        return $pdf->stream('contratogrupal.pdf');
    }



    public function generatecrontratoindividualPDF(Request $request, $id)
    {
        $prestamo = credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->with('clientes')->first(); // Obtener un solo cliente
        //$responsable = auth()->user();
        // Usa Carbon para obtener la fecha actual
        $date = Carbon::now();

        // Formatea la fecha con la configuración regional establecida
        $formattedDate = $date->translatedFormat(' d \d\í\a\s \d\e\l \m\e\s \d\e F \d\e Y');

        $data = compact(
            'prestamo',
            //'responsable',
            'cuotas',
            'credito_cliente',
            'formattedDate'
        );

        $pdf = Pdf::loadView('pdf.contratoindividual', $data)->setPaper('a4');
        return $pdf->stream('contratoindividual.pdf');
    }




    public function generatecartillaPDF(Request $request, $id)
    {
        $prestamo = credito::find($id);
        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->get();
        $responsable = auth()->user();


        // Usa Carbon para obtener la fecha actual
        $date = Carbon::now();

        // Formatea la fecha con la configuración regional establecida
        $formattedDate = $date->translatedFormat(' d \d\e F \d\e\l Y');


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

    public function generatepagarePDF(Request $request, $id)
    {
        $prestamo = credito::find($id);
        if (!$prestamo) {
            return response()->json(['error' => 'Crédito no encontrado'], 404);
        }

        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->with('clientes')->first();
        $responsable = auth()->user();

        // Usa Carbon para obtener la fecha actual
        $date = Carbon::now();

        // Formatea la fecha con la configuración regional establecida
        $formattedDate = $date->translatedFormat('d \d\e F \d\e\l Y');

        $tasaInteres = $prestamo->tasa;

        $tasadiaria = number_format((pow(1 + ($tasaInteres / 100), 1 / 360) - 1) * 100, 2);

        // Convertir monto a letras
        $formatter = new NumeroALetras();
        $montoEnLetras = $formatter->toMoney($prestamo->monto, 2, 'soles', 'centimos');

        // Generar o obtener el correlativo
        $correlativo = CorrelativoPagare::generateCorrelativo($id);

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente',
            'formattedDate',
            'montoEnLetras',
            'tasadiaria',
            'correlativo'
        );

        // Generar y retornar el PDF
        $pdf = Pdf::loadView('pdf.pagare', $data)->setPaper('a4');
        return $pdf->stream('pagare.pdf');
    }




    public function generatecartacobranzaPDF(Request $request, $id)
    {
        $prestamo = Credito::find($id);
        if (!$prestamo) {
            return response()->json(['error' => 'Crédito no encontrado'], 404);
        }

        $cuotas = Cronograma::where('id_prestamo', $id)->get();
        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->with('clientes')->first();
        $responsable = $prestamo->user;

        // Fecha actual
        $date = Carbon::now();

        $fecha_desembolso = Carbon::parse($prestamo->fecha_desembolso);
        $formattedfechadesembolso = $fecha_desembolso->translatedFormat('d \d\e F \d\e\l Y');
        $formattedDate = $date->translatedFormat('d \d\e F \d\e\l Y');

        $tasaInteres = $prestamo->tasa;
        $tasadiaria = number_format((pow(1 + ($tasaInteres / 100), 1 / 360) - 1) * 100, 2);

        // Convertir el monto a letras
        $formatter = new NumeroALetras();
        $montoEnLetras = $formatter->toMoney($prestamo->monto, 2, 'soles', 'centimos');

        // Obtener o generar el correlativo
        $correlativo = CorrelativoPagare::generateCorrelativo($id);

        // Filtrar todas las cuotas vencidas: fecha anterior a hoy y sin ingresos registrados
        $cuotasVencidas = $cuotas->filter(function ($cuota) {
            return $cuota->fecha < Carbon::now()->toDateString() && $cuota->ingresos->isEmpty();
        });

        // Cantidad de cuotas vencidas
        $cantidad_cuotas_vencidas = $cuotasVencidas->count();

        // Calcular la cantidad de días de mora desde la primera cuota vencida hasta hoy
        if ($cantidad_cuotas_vencidas > 0) {
            $primerVencimiento = $cuotasVencidas->sortBy('fecha')->first()->fecha;
            $dias_mora_primera = Carbon::now()->diffInDays(Carbon::parse($primerVencimiento));
        } else {
            $dias_mora_primera = 0;
        }

        // Calcular la mora y el monto total por cada cuota vencida de forma individual
        $porcentaje_mora = 0.3; // 0.3% por día de mora por cada mil soles
        $total_mora = 0;
        $total_monto_con_mora = 0;

        foreach ($cuotasVencidas as $cuota) {
            // Días de atraso para la cuota individual
            $diasAtraso = Carbon::now()->diffInDays(Carbon::parse($cuota->fecha));
            // Calcular la mora individual
            $mora_individual = round($cuota->monto * ($porcentaje_mora / 1000) * $diasAtraso, 2);
            // Monto de la cuota sumándole su mora
            $monto_con_mora = $cuota->monto + $mora_individual;
            // Acumular los totales
            $total_mora += $mora_individual;
            $total_monto_con_mora += $monto_con_mora;
        }

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente',
            'formattedDate',
            'montoEnLetras',
            'tasadiaria',
            'correlativo',
            'cantidad_cuotas_vencidas',
            'total_mora',
            'total_monto_con_mora',
            'formattedfechadesembolso',
            'dias_mora_primera'
        );

        // Generar y retornar el PDF
        $pdf = PDF::loadView('pdf.cartacobranza', $data)->setPaper('a4');
        return $pdf->stream('carta-cobranza.pdf');
    }








    public function generatecartacobranzagrupalPDF(Request $request, $id)
    {
        $prestamo = Credito::find($id);
        if (!$prestamo) {
            return response()->json(['error' => 'Crédito no encontrado'], 404);
        }

        // Obtener las cuotas generales del crédito grupal (donde cliente_id es null)
        $cuotas = Cronograma::where('id_prestamo', $id)
            ->whereNull('cliente_id')
            ->get();

        $credito_cliente = CreditoCliente::where('prestamo_id', $id)->with('clientes')->first();
        $responsable = $prestamo->user;

        // Fecha actual y formateo de fechas
        $date = Carbon::now();
        $fecha_desembolso = Carbon::parse($prestamo->fecha_desembolso);
        $formattedfechadesembolso = $fecha_desembolso->translatedFormat('d \d\e F \d\e\l Y');
        $formattedDate = $date->translatedFormat('d \d\e F \d\e\l Y');

        $tasaInteres = $prestamo->tasa;
        $tasadiaria = number_format((pow(1 + ($tasaInteres / 100), 1 / 360) - 1) * 100, 2);

        // Convertir monto a letras
        $formatter = new NumeroALetras();
        $montoEnLetras = $formatter->toMoney($prestamo->monto, 2, 'soles', 'centimos');

        // Generar o obtener el correlativo
        $correlativo = CorrelativoPagare::generateCorrelativo($id);

        // Filtrar todas las cuotas vencidas: fecha anterior a hoy y sin ingresos registrados
        $cuotasVencidas = $cuotas->filter(function ($cuota) {
            return $cuota->fecha < Carbon::now()->toDateString() && $cuota->ingresos->isEmpty();
        });

        // Cantidad de cuotas vencidas
        $cantidad_cuotas_vencidas = $cuotasVencidas->count();

        // Calcular la cantidad de días de mora desde la primera cuota vencida hasta hoy
        if ($cantidad_cuotas_vencidas > 0) {
            $primerVencimiento = $cuotasVencidas->sortBy('fecha')->first()->fecha;
            $dias_mora_primera = Carbon::now()->diffInDays(Carbon::parse($primerVencimiento));
        } else {
            $dias_mora_primera = 0;
        }

        // Calcular la mora y el monto total de cada cuota vencida de forma individual
        $porcentaje_mora = 0.3; // 0.3% por día de mora por cada mil soles
        $total_mora = 0;
        $total_monto_con_mora = 0;

        foreach ($cuotasVencidas as $cuota) {
            $diasAtraso = Carbon::now()->diffInDays(Carbon::parse($cuota->fecha));
            $mora_individual = round($cuota->monto * ($porcentaje_mora / 1000) * $diasAtraso, 2);
            $monto_con_mora = $cuota->monto + $mora_individual;
            $total_mora += $mora_individual;
            $total_monto_con_mora += $monto_con_mora;
        }

        $data = compact(
            'prestamo',
            'responsable',
            'cuotas',
            'credito_cliente',
            'formattedDate',
            'montoEnLetras',
            'tasadiaria',
            'correlativo',
            'cantidad_cuotas_vencidas',
            'total_mora',
            'total_monto_con_mora',
            'formattedfechadesembolso',
            'dias_mora_primera'
        );

        // Generar y retornar el PDF
        $pdf = PDF::loadView('pdf.cartacobranzagrupal', $data)->setPaper('a4');
        return $pdf->stream('carta-cobranza.pdf');
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

        $tipo_producto = \App\Models\TipoProducto::where('id_prestamo', $id)->get();
        $venta_mensual = \App\Models\VentasMensuales::where('id_prestamo', $id)->get();
        $productos_agricolas = \App\Models\ProductoAgricola::where('id_prestamo', $id)->first();


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
                    foreach ($tipo_producto  as $producto) {
                        $montoVenta = $cantidad_cultivo * $producto->precio * ($producto->porcentaje / 100);
                        $totalVentas += $montoVenta;
                    }

                    $totalCompras = 0;
                    foreach ($gastosOperativos as $gastoOperativo) {
                        $gasto = $gastoOperativo->precio_unitario * $gastoOperativo->cantidad;
                        $totalCompras += $gasto;
                    }

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
    public function generateticket($id)
    {
        $prestamo = \App\Models\credito::find($id);
        $creditos = \App\Models\CreditoCliente::where('prestamo_id', $id)->get();

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener la sucursal del usuario autenticado
        $sucursal_id = $user->sucursal_id;

        // Obtener la última transacción de caja abierta por el asesor (usuario actual)
        $ultimaTransaccion = \App\Models\CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        // Calcular el monto total del grupo
        $montoTotalGrupo = $creditos->sum('monto_indivual');

        // Crear el egreso
        $egreso = \App\Models\Egreso::create([
            'transaccion_id' => $ultimaTransaccion->id,
            'prestamo_id' => $prestamo->id,
            'fecha_egreso' => now()->toDateString(),
            'hora_egreso' => now()->toTimeString(),
            'monto' => $montoTotalGrupo,
            'sucursal_id' => $sucursal_id,
        ]);

        // Actualizar la cantidad de egresos en la transacción de caja
        $ultimaTransaccion->cantidad_egresos = $ultimaTransaccion->cantidad_egresos + $montoTotalGrupo;
        $ultimaTransaccion->save();

        // Actualizar el estado del préstamo a 'pagado'
        $prestamo->estado = 'pagado';
        $prestamo->save();

        $pdf = Pdf::loadView('pdf.ticket', compact('prestamo', 'creditos', 'montoTotalGrupo'))
            ->setPaper([0, 0, 205, 800]);

        return $pdf->stream('ticket.pdf');
    }
    public function generarTicketDePago($id)
    {
        $ingreso = \App\Models\Ingreso::find($id);

        if (!$ingreso) {
            return response()->json(['error' => 'Pago no encontrado.'], 404);
        }

        $prestamo = \App\Models\credito::find($ingreso->prestamo_id);
        $cliente = \App\Models\cliente::find($ingreso->cliente_id);
        $cronograma = \App\Models\Cronograma::find($ingreso->cronograma_id);

        // Obtener la siguiente cuota
        $siguienteCuota = \App\Models\Cronograma::where('id_prestamo', $ingreso->prestamo_id)
            ->where('numero', '>', $ingreso->numero_cuota)
            ->orderBy('numero', 'asc')
            ->first();
        $fechaSiguienteCuota = $siguienteCuota ? $siguienteCuota->fecha : 'N/A';

        $pdf = Pdf::loadView('pdf.ticketpago', compact('prestamo', 'cliente', 'ingreso', 'cronograma', 'fechaSiguienteCuota'))
            ->setPaper([0, 0, 200, 400]); // Ajustar el tamaño del papel si es necesario
        return $pdf->stream('ticket.pdf');
    }
    public function generarTransaccionesPDF($caja_id)
    {
        $caja = \App\Models\Caja::findOrFail($caja_id);
        $today = \Carbon\Carbon::today();

        // Verificar si la caja tiene una transacción abierta o cerrada hoy
        $ultimaTransaccion = $caja->transacciones()
            // ->whereDate('created_at', $today)
            ->latest()->first();

        if (!$ultimaTransaccion) {
            return redirect()->back()->with('error', 'No hay transacciones abiertas para esta caja en el día de hoy.');
        }

        $cajaCerrada = $ultimaTransaccion->hora_cierre ? true : false;
        $ingresos = \App\Models\Ingreso::where('transaccion_id', $ultimaTransaccion->id)
            ->whereNotNull('cliente_id')
            ->with('cliente', 'transaccion.user')
            ->get();

        $egresos = \App\Models\Egreso::where('transaccion_id', $ultimaTransaccion->id)
            ->with(['prestamo.clientes', 'transaccion.user'])
            ->get();

        $gastos = \App\Models\Gasto::where('caja_transaccion_id', $ultimaTransaccion->id)
            ->with('user')
            ->get();

        $ingresosExtras = \App\Models\IngresoExtra::where('caja_transaccion_id', $ultimaTransaccion->id)
            ->with('user')
            ->get();

        // Preparar datos de egresos con clientes
        $egresosConClientes = $egresos->map(function ($egreso) {
            return [
                'hora_egreso' => $egreso->hora_egreso,
                'monto' => $egreso->monto,
                'clientes' => $egreso->prestamo->clientes->pluck('nombre')->toArray(),
                'usuario' => $egreso->transaccion->user->name
            ];
        });

        // Preparar datos de gastos
        $gastosConDetalles = $gastos->map(function ($gasto) {
            return [
                'hora_gasto' => $gasto->created_at->format('H:i:s'),
                'monto' => $gasto->monto_gasto,
                'numero_documento' => $gasto->numero_doc . '-' . $gasto->serie_doc,
                'responsable' => $gasto->numero_documento_responsable . '-' . $gasto->nombre_responsable,
                'usuario' => $gasto->user->name
            ];
        });

        // Preparar datos de ingresos extras
        $ingresosExtrasConDetalles = $ingresosExtras->map(function ($ingresoExtra) {
            return [
                'hora_ingreso' => $ingresoExtra->created_at->format('H:i:s'),
                'monto' => $ingresoExtra->monto,
                'motivo' => $ingresoExtra->motivo,
                'numero_documento' => $ingresoExtra->numero_documento,
                'usuario' => $ingresoExtra->user->name
            ];
        });

        $datosCierre = null;
        $desajuste = null;
        if ($cajaCerrada) {
            $datosCierre = json_decode($ultimaTransaccion->json_cierre, true);

            $saldoEfectivo = array_sum(array_map(function ($cantidad, $valor) {
                return $cantidad * $valor;
            }, $datosCierre['billetes'], array_keys($datosCierre['billetes'])));

            $saldoEfectivo += array_sum(array_map(function ($cantidad, $valor) {
                return $cantidad * $valor;
            }, $datosCierre['monedas'], array_keys($datosCierre['monedas'])));

            $saldoDepositos = $datosCierre['depositos'];
            $saldoFinalReal = $saldoDepositos + $saldoEfectivo;

            // Calcular el saldo final esperado
            $saldoFinalEsperado = $ultimaTransaccion->monto_apertura + $ingresos->sum('monto') + $ingresosExtras->sum('monto') - $egresos->sum('monto') - $gastos->sum('monto_gasto');

            $desajuste =  $saldoFinalReal - $saldoFinalEsperado;

            // Formatear valores a dos decimales
            $saldoFinalReal = $saldoFinalReal;
            $saldoFinalEsperado = $saldoFinalEsperado;
            $desajuste = $desajuste;
        }

        $pdf = Pdf::loadView('pdf.transacciones', compact(
            'caja',
            'ingresos',
            'ingresosExtrasConDetalles',
            'egresos',
            'egresosConClientes',
            'gastosConDetalles',
            'saldoFinalReal',
            'saldoFinalEsperado',
            'desajuste',
            'ultimaTransaccion',
            'saldoEfectivo',
            'saldoDepositos',
        ));

        return $pdf->stream('transacciones.pdf');
    }

    public function generarArqueoPDF($id)
    {
        $transaccion = \App\Models\CajaTransaccion::findOrFail($id);

        if (!$transaccion) {
            return redirect()->back()->with('error', 'Transacción no encontrada.');
        }

        $ingresos = \App\Models\Ingreso::where('transaccion_id', $transaccion->id)
            ->whereNotNull('cliente_id')
            ->with('cliente', 'transaccion.user')->get();
        $egresos = \App\Models\Egreso::where('transaccion_id', $transaccion->id)
            ->with(['prestamo.clientes', 'transaccion.user'])
            ->get();
        $gastos = \App\Models\Gasto::where('caja_transaccion_id', $transaccion->id)->with('user')->get();
        $ingresosExtras = \App\Models\IngresoExtra::where('caja_transaccion_id', $transaccion->id)->with('user')->get();

        $datosCierre = json_decode($transaccion->json_cierre, true);

        // Preparar datos de egresos con clientes
        $egresosConClientes = $egresos->map(function ($egreso) {
            return [
                'hora_egreso' => $egreso->hora_egreso,
                'monto' => $egreso->monto,
                'clientes' => $egreso->prestamo->clientes->pluck('nombre')->toArray(),
                'usuario' => $egreso->transaccion->user->name
            ];
        });

        // Preparar datos de gastos
        $gastosConDetalles = $gastos->map(function ($gasto) {
            return [
                'hora_gasto' => $gasto->created_at->format('H:i:s'),
                'monto' => $gasto->monto_gasto,
                'numero_documento' => $gasto->numero_doc . '-' . $gasto->serie_doc,
                'responsable' => $gasto->numero_documento_responsable . '-' . $gasto->nombre_responsable,
                'usuario' => $gasto->user->name
            ];
        });

        // Preparar datos de ingresos extras
        $ingresosExtrasConDetalles = $ingresosExtras->map(function ($ingresoExtra) {
            return [
                'hora_ingreso' => $ingresoExtra->created_at->format('H:i:s'),
                'monto' => $ingresoExtra->monto,
                'motivo' => $ingresoExtra->motivo,
                'numero_documento' => $ingresoExtra->numero_documento . '-' . $ingresoExtra->serie_documento,
                'usuario' => $ingresoExtra->user->name
            ];
        });

        // Calcular el saldo final real
        $saldoFinalReal = array_sum(array_map(function ($cantidad, $valor) {
            return $cantidad * $valor;
        }, $datosCierre['billetes'], array_keys($datosCierre['billetes'])));

        $saldoFinalReal += array_sum(array_map(function ($cantidad, $valor) {
            return $cantidad * $valor;
        }, $datosCierre['monedas'], array_keys($datosCierre['monedas'])));

        $saldoFinalReal += $datosCierre['depositos'];

        // Calcular el saldo final esperado
        $saldoFinalEsperado = $transaccion->monto_apertura + $ingresos->sum('monto') - $egresos->sum('monto') - $gastos->sum('monto_gasto') + $ingresosExtras->sum('monto');

        $desajuste = $saldoFinalEsperado - $saldoFinalReal;

        // Formatear valores a dos decimales
        $saldoFinalReal = number_format($saldoFinalReal, 2);
        $saldoFinalEsperado = number_format($saldoFinalEsperado, 2);
        $desajuste = number_format($desajuste, 2);

        $usuario = $transaccion->user;

        $data = compact('transaccion', 'ingresos', 'egresosConClientes', 'gastosConDetalles', 'ingresosExtrasConDetalles', 'saldoFinalReal', 'saldoFinalEsperado', 'desajuste', 'datosCierre', 'usuario');

        $pdf = Pdf::loadView('pdf.arqueo', $data);

        return $pdf->stream('arqueo.pdf');
    }

    public function generatedetalleclientePDF(Request $request, $id)
    {
        $cliente = cliente::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $data = ['cliente' => $cliente];

        $pdf = PDF::loadView('pdf.detallecliente', $data)->setPaper('a4');
        return $pdf->stream('detalle-cliente.pdf');
    }

    public function generarTicketDePagogrupal($array)
    {
        $idsArray = explode('-', $array);

        $ingresos = \App\Models\Ingreso::whereIn('id', $idsArray)->get();

        $ingresos = \App\Models\Ingreso::whereIn('id', $idsArray)->get();

        if ($ingresos->isEmpty()) {
            return response()->json(['error' => 'Pagos no encontrados.'], 404);
        }

        $data = [];

        foreach ($ingresos as $ingreso) {
            $prestamo = \App\Models\credito::find($ingreso->prestamo_id);
            $cliente = \App\Models\cliente::find($ingreso->cliente_id);
            $cronograma = \App\Models\Cronograma::find($ingreso->cronograma_id);

            // Obtener la siguiente cuota
            $siguienteCuota = \App\Models\Cronograma::where('id_prestamo', $ingreso->prestamo_id)
                ->where('numero', '>', $ingreso->numero_cuota)
                ->orderBy('numero', 'asc')
                ->first();
            $fechaSiguienteCuota = $siguienteCuota ? $siguienteCuota->fecha : 'N/A';

            $data[] = [
                'prestamo' => $prestamo,
                'cliente' => $cliente,
                'ingreso' => $ingreso,
                'cronograma' => $cronograma,
                'fechaSiguienteCuota' => $fechaSiguienteCuota
            ];
        }

        // dd($data);

        $pdf = Pdf::loadView('pdf.ticketpagogrupal', compact('data'))->setPaper([0, 0, 200, 400]);
        return $pdf->stream('tickets.pdf');
    }
}
