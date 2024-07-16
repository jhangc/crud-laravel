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
use App\Models\Garantia;
use App\Models\Activos;
use App\Models\ProyeccionesVentas;
use App\Models\VentasDiarias;
use App\Models\DeudasFinancieras;
use App\Models\GastosOperativos;
use App\Models\GastosFamiliares;
use App\Models\Boleta;

use App\Models\VentasMensuales;
use App\Models\ProductoAgricola;
use App\Models\Inventario;
use App\Models\TipoProducto;

class UpdateController extends Controller
{
    public function updatecomercio(Request $request, $id)
    {
        $decodedData = $request->all();
        foreach ([
            'proyeccionesArray', 'inventarioArray', 'deudasFinancierasArray', 'gastosOperativosArray',
            'ventasdiarias', 'inventarioArray1', 'clientesArray'
        ] as $key) {
            if ($request->filled($key)) {
                $decodedData[$key] = json_decode($request->input($key), true);
            }
        }

        DB::beginTransaction();
        try {
            // Actualización del préstamo
            $prestamo = credito::findOrFail($id);
            $prestamo->tipo = $request->tipo_credito;
            $prestamo->producto = $request->tipo_producto;
            $prestamo->subproducto = $request->subproducto;
            $prestamo->destino = $request->destino_credito;
            $prestamo->recurrencia = $request->recurrencia;
            $prestamo->tasa = $request->tasa_interes;
            $prestamo->tiempo = $request->tiempo_credito;
            $prestamo->monto_total = $request->monto;
            $prestamo->fecha_desembolso = $request->fecha_desembolso;
            $prestamo->periodo_gracia_dias = $request->periodo_gracia_dias;
            $prestamo->porcentaje_credito = $request->porcentaje_venta_credito;
            $prestamo->descripcion_negocio = $request->descripcion_negocio;
            $prestamo->nombre_prestamo = $request->nombre_prestamo;
            $prestamo->cantidad_integrantes = $request->cantidad_grupo;
            $prestamo->activo = $request->activo ?? true;
            $prestamo->user_id = Auth::id();
            $prestamo->save();
    
            // Actualización de la garantía
            $garantia = Garantia::updateOrCreate(
                ['id_prestamo' => $prestamo->id],
                [
                    'descripcion' => $request->descripcion_garantia,
                    'valor_mercado' => $request->valor_mercado,
                    'valor_realizacion' => $request->valor_realizacion,
                    'valor_gravamen' => $request->valor_gravamen
                ]
            );
            
            if ($request->hasFile('archivo_garantia') && $request->file('archivo_garantia')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo_garantia')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo_garantia')->storeAs('public/documentos_garantia', $nombreArchivo);
                $garantia->documento_pdf = $ruta;
            }
            $garantia->save();
    
            // Actualización de los activos
            $activos = Activos::updateOrCreate(
                ['prestamo_id' => $prestamo->id],
                [
                    'cuentas_por_cobrar' => $request->cuentas_por_cobrar,
                    'saldo_en_caja_bancos' => $request->saldo_caja_bancos,
                    'adelanto_a_proveedores' => $request->adelanto_a_proveedores,
                    'otros' => $request->otros
                ]
            );
    
            // Actualización de los datos relacionados
            $this->updateArrayData($decodedData, $prestamo->id, $request);
    
            // Actualización de los créditos de los clientes y cronograma
            $this->updateClienteYcronograma($prestamo, $request);
    
            DB::commit();
            return response()->json(['message' => 'Crédito actualizado con éxito', 'data' => $prestamo], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el crédito: ' . $e->getMessage()], 500);
        }
    }
    
    protected function updateArrayData(array $data, $prestamoId, $request)
    {
        // Eliminar registros antiguos
        ProyeccionesVentas::where('id_prestamo', $prestamoId)->delete();
        VentasDiarias::where('prestamo_id', $prestamoId)->delete();
        DeudasFinancieras::where('prestamo_id', $prestamoId)->delete();
        GastosOperativos::where('id_prestamo', $prestamoId)->delete();
        GastosFamiliares::where('id_prestamo', $prestamoId)->delete();
        Inventario::where('id_prestamo', $prestamoId)->where('tipo_inventario', 1)->delete();
        Inventario::where('id_prestamo', $prestamoId)->where('tipo_inventario', 2)->delete();
        Inventario::where('id_prestamo', $prestamoId)->where('tipo_inventario', 3)->delete();
        Boleta::where('id_prestamo', $prestamoId)->delete();
        \App\Models\GastoProducir::where('id_prestamo', $prestamoId)->delete();
        VentasMensuales::where('id_prestamo', $prestamoId)->delete();
        TipoProducto::where('id_prestamo', $prestamoId)->delete();
        ProductoAgricola::where('id_prestamo', $prestamoId)->delete();
    
        // Guardar nuevos registros
        if (isset($data['proyeccionesArray']) && is_array($data['proyeccionesArray'])) {
            foreach ($data['proyeccionesArray'] as $proyeccionData) {
                ProyeccionesVentas::create([
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
                VentasDiarias::create([
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
                DeudasFinancieras::create([
                    'nombre_entidad' => $deudaData['entidad'],
                    'saldo_capital' => $deudaData['saldoCapital'],
                    'cuota' => $deudaData['cuota'],
                    'tiempo_restante' => '0',
                    'prestamo_id' => $prestamoId,
                    'estado' => 'activo'
                ]);
            }
        }
    
        if (isset($data['gastosOperativosArray']) && is_array($data['gastosOperativosArray'])) {
            foreach ($data['gastosOperativosArray'] as $gastoData) {
                GastosOperativos::create([
                    'descripcion' => $gastoData['descripcion'],
                    'precio_unitario' => $gastoData['precioUnitario'],
                    'cantidad' => $gastoData['cantidad'],
                    'id_prestamo' => $prestamoId,
                    'acciones' => 'activo'
                ]);
            }
        }
    
        if (isset($data['inventarioArray1']) && is_array($data['inventarioArray1'])) {
            foreach ($data['inventarioArray1'] as $inventarioData) {
                GastosFamiliares::create([
                    'descripcion' => $inventarioData['descripcion'],
                    'precio_unitario' => $inventarioData['precioUnitario'],
                    'cantidad' => $inventarioData['cantidad'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }
    
        if (isset($data['inventarioprocesoArray']) && is_array($data['inventarioprocesoArray'])) {
            foreach ($data['inventarioprocesoArray'] as $inventarioData) {
                Inventario::create([
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
                Inventario::create([
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
                Boleta::create([
                    'numero_boleta' => $boletaData['numeroBoleta'],
                    'monto_boleta' => $boletaData['montoBoleta'],
                    'descuento_boleta' => $boletaData['descuentoBoleta'],
                    'total_boleta' => $boletaData['totalBoleta'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }
    
        if (isset($data['gastosProducirArray']) && is_array($data['gastosProducirArray']) && count($data['gastosProducirArray']) > 0) {
            $gasto =\App\Models\GastoProducir::create([
                'nombre_actividad' => $request->nombre_actividad,
                'cantidad_terreno' => $request->cantidad_terreno,
                'produccion_total' => $request->produccion_total,
                'precio_kg' => $request->precio_kg,
                'id_prestamo' => $prestamoId
            ]);
            foreach ($data['gastosProducirArray'] as $gastoProducirData) {
                \App\Models\GastoProducir::create([
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
                VentasMensuales::create([
                    'mes' => $boletaData['mes'],
                    'porcentaje' => $boletaData['porcentaje'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }
    
        if (isset($data['gastosAgricolaArray']) && is_array($data['gastosAgricolaArray'])) {
            ProductoAgricola::create([
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
                $row = GastosOperativos::create([
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
            }
        }
    
        if (isset($data['inventarioMaterialArray']) && is_array($data['inventarioMaterialArray'])) {
            foreach ($data['inventarioMaterialArray'] as $inventarioData) {
                Inventario::create([
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
                TipoProducto::create([
                    'producto' => $inventarioData['PRODUCTO'],
                    'precio' =>  !empty($inventarioData['precio_unitario']) ? $inventarioData['precio_unitario'] : 0,
                    'porcentaje' => !empty($inventarioData['procentaje_producto']) ? $inventarioData['procentaje_producto'] : 0,
                    'id_prestamo' => $prestamoId
                ]);
            }
        }
    }
    
    protected function updateClienteYcronograma($prestamo, $request)
    {
        $cliente = cliente::where('documento_identidad', $request->documento_identidad)->where('activo', 1)->first();
        if ($cliente) {
            CreditoCliente::where('prestamo_id', $prestamo->id)->delete();
            $credito_cliente = CreditoCliente::updateOrCreate(
                ['prestamo_id' => $prestamo->id, 'cliente_id' => $cliente->id],
                ['monto_indivual' => $request->monto]
            );
    
            // Actualizar cronograma
            $this->guardarCronograma($prestamo, $cliente, $request, $request->monto);
        }
    }
    
    protected function guardarCronograma($prestamo, $cliente, $request, $montoIndividual)
    {
        // Eliminar el cronograma existente
        Cronograma::where('id_prestamo', $prestamo->id)->where('cliente_id', $cliente->id)->delete();
    
        // Recalcular y guardar el nuevo cronograma
        $fecha_desembolso = Carbon::parse($request->fecha_desembolso);
        $fechaconperiodogracia = clone $fecha_desembolso;
        $fechaconperiodogracia->modify("+$request->periodo_gracia_dias days");
        $tiempo = $request->tiempo_credito;
        $montoTotal = $montoIndividual;
        $frecuencia = $request->tipo_producto == 'grupal' ? $request->recurrencia1 : $request->recurrencia;
        $tasaInteres = $request->tasa_interes;
        $tasaDiaria = pow(1 + ($tasaInteres / 100), 1 / 360) - 1;
        $interesesPeriodoGracia = $montoTotal * $tasaDiaria * $request->periodo_gracia_dias;
        $cuotaSinGracia = $this->calcularCuota($montoTotal, $tasaInteres, $tiempo, $frecuencia);
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $tiempo;
        $fechaCuota = clone $fechaconperiodogracia;
    
        for ($i = 1; $i <= $tiempo; $i++) {
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
            $cronograma->fecha = clone $fechaCuota;

            if($request->tipo_producto == 'grupal'){
                $cronograma->monto = $cuotaSinGracia[$i - 1]['cuota'] + $interesesMensualesPorGracia + 0.021 * $cuotaSinGracia[$i - 1]['cuota'];
            }else{
                $cronograma->monto = $cuotaSinGracia[$i - 1]['cuota'] + $interesesMensualesPorGracia;
            }
            //$cronograma->monto = $cuotaSinGracia[$i - 1]['cuota'] + $interesesMensualesPorGracia + 0.021 * $cuotaSinGracia[$i - 1]['cuota'];

            $cronograma->numero = $i;
            $cronograma->id_prestamo = $prestamo->id;
            $cronograma->cliente_id = $cliente->id;
            $cronograma->capital = $cuotaSinGracia[$i - 1]['capital'];
            $cronograma->interes = $cuotaSinGracia[$i - 1]['interes'];
            $cronograma->amortizacion = $cuotaSinGracia[$i - 1]['amortizacion'];
            $cronograma->saldo_deuda = $cuotaSinGracia[$i - 1]['saldo_deuda'];
            $cronograma->save();
        }
    }
    
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
                'capital' => round($monto - $saldo, 2), // Capital amortizado
                'interes' => round($interesPeriodo, 2),
                'amortizacion' => round($amortizacion, 2),
                'cuota' => round($cuota, 2),
                'saldo_deuda' => round($saldo, 2)
            ];
        }

        return $cuotas;
    }

    public function updateCreditoGrupal(Request $request, $id)
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
        DB::beginTransaction();
        try {
            $prestamo = credito::findOrFail($id);
            $prestamo->tipo = $request->tipo_credito;
            $prestamo->producto = $request->tipo_producto;
            $prestamo->subproducto = $request->subproducto;
            $prestamo->destino = $request->destino_credito;
            $prestamo->recurrencia = $request->recurrencia1;
            $prestamo->tasa = $request->tasa_interes;
            $prestamo->tiempo = $request->tiempo_credito;
            $prestamo->monto_total = $request->monto;
            $prestamo->fecha_desembolso = $request->fecha_desembolso;
            $prestamo->periodo_gracia_dias = $request->periodo_gracia_dias;
            $prestamo->porcentaje_credito = $request->porcentaje_venta_credito;
            $prestamo->estado = "pendiente";
            $prestamo->categoria = 'grupal';
            $prestamo->nombre_prestamo = $request->nombre_prestamo;
            $prestamo->cantidad_integrantes = $request->cantidad_grupo;
            $prestamo->descripcion_negocio = $request->descripcion_negocio;
            $prestamo->user_id = Auth::id();
            $prestamo->save();

            // Actualizar la garantía
            $garantia = Garantia::where('id_prestamo', $prestamo->id)->first();
            if (!$garantia) {
                $garantia = new Garantia();
                $garantia->id_prestamo = $prestamo->id;
            }
            $garantia->descripcion = $request->descripcion_garantia;
            $garantia->valor_mercado = $request->valor_mercado;
            $garantia->valor_realizacion = $request->valor_realizacion;
            $garantia->valor_gravamen = $request->valor_gravamen;
            if ($request->hasFile('archivo_garantia') && $request->file('archivo_garantia')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo_garantia')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo_garantia')->storeAs('public/documentos_garantia', $nombreArchivo);
                $garantia->documento_pdf = $ruta;
            }
            $garantia->save();

            // Actualizar activos
            $activos = Activos::where('prestamo_id', $prestamo->id)->first();
            if (!$activos) {
                $activos = new Activos();
                $activos->prestamo_id = $prestamo->id;
            }
            $activos->cuentas_por_cobrar = $request->cuentas_por_cobrar;
            $activos->saldo_en_caja_bancos = $request->saldo_caja_bancos;
            $activos->adelanto_a_proveedores = $request->adelanto_a_proveedores;
            $activos->otros = $request->otros;
            $activos->save();
            // Actualizar créditos de clientes y cronograma
            CreditoCliente::where('prestamo_id', $prestamo->id)->delete();
            $this->updateClientesYcronograma($prestamo, $decodedData['clientesArray'], $request);

            DB::commit();
            return response()->json(['message' => 'Crédito actualizado con éxito', 'data' => $prestamo], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el crédito: ' . $e->getMessage()], 500);
        }
    }

    public function updateCreditoServicio(Request $request, $id)
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
        DB::beginTransaction();
        try {
            $prestamo = credito::findOrFail($id);
            $prestamo->tipo = $request->tipo_credito;
            $prestamo->producto = $request->tipo_producto;
            $prestamo->subproducto = $request->subproducto;
            $prestamo->destino = $request->destino_credito;
            $prestamo->recurrencia = $request->recurrencia;
            $prestamo->tasa = $request->tasa_interes;
            $prestamo->tiempo = $request->tiempo_credito;
            $prestamo->monto_total = $request->monto;
            $prestamo->fecha_desembolso = $request->fecha_desembolso;
            $prestamo->periodo_gracia_dias = $request->periodo_gracia_dias;
            $prestamo->porcentaje_credito = $request->porcentaje_venta_credito;
            $prestamo->estado = "pendiente";
            $prestamo->categoria = 'servicio';
            $prestamo->nombre_prestamo = $request->nombre_prestamo;
            $prestamo->cantidad_integrantes = $request->cantidad_grupo;
            $prestamo->descripcion_negocio = $request->descripcion_negocio;
            $prestamo->user_id = Auth::id();
            $prestamo->save();

            // Actualizar la garantía
            $garantia = Garantia::where('id_prestamo', $prestamo->id)->first();
            if (!$garantia) {
                $garantia = new Garantia();
                $garantia->id_prestamo = $prestamo->id;
            }
            $garantia->descripcion = $request->descripcion_garantia;
            $garantia->valor_mercado = $request->valor_mercado;
            $garantia->valor_realizacion = $request->valor_realizacion;
            $garantia->valor_gravamen = $request->valor_gravamen;
            if ($request->hasFile('archivo_garantia') && $request->file('archivo_garantia')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo_garantia')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo_garantia')->storeAs('public/documentos_garantia', $nombreArchivo);
                $garantia->documento_pdf = $ruta;
            }
            $garantia->save();

            // Actualizar activos
            $activos = Activos::where('prestamo_id', $prestamo->id)->first();
            if (!$activos) {
                $activos = new Activos();
                $activos->prestamo_id = $prestamo->id;
            }
            $activos->cuentas_por_cobrar = $request->cuentas_por_cobrar;
            $activos->saldo_en_caja_bancos = $request->saldo_caja_bancos;
            $activos->adelanto_a_proveedores = $request->adelanto_a_proveedores;
            $activos->otros = $request->otros;
            $activos->save();
            // Actualizar créditos de clientes y cronograma
            CreditoCliente::where('prestamo_id', $prestamo->id)->delete();
            $this->updateClienteYcronograma($prestamo, $request);
            $this->updateArrayData($decodedData, $prestamo->id, $request);
            
            DB::commit();
            return response()->json(['message' => 'Crédito actualizado con éxito', 'data' => $prestamo], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el crédito: ' . $e->getMessage()], 500);
        }
    }

    protected function updateClientesYcronograma($prestamo, $clientesArray, $request)
    {
        if (is_array($clientesArray)) {
            foreach ($clientesArray as $clienteData) {
                $cliente = cliente::where('documento_identidad', $clienteData['documento'])->where('activo', 1)->first();
                if ($cliente) {
                    $credito_cliente = new CreditoCliente();
                    $credito_cliente->prestamo_id = $prestamo->id;
                    $credito_cliente->cliente_id = $cliente->id;
                    $credito_cliente->monto_indivual = $clienteData['monto'];
                    $credito_cliente->save();
                    $this->guardarCronograma($prestamo, $cliente, $request, $clienteData['monto']);
                }
            }
        }
    }
    public function updateCreditoProduccion(Request $request, $id)
    {
        $decodedData = $request->all();
        foreach ([
            'clientesArray', 'proyeccionesArray', 'inventarioArray', 'deudasFinancierasArray', 'gastosOperativosArray', 'gastosProducirArray',
            'inventarioArray1', 'ventasdiarias', 'inventarioprocesoArray'
        ] as $key) {
            if ($request->filled($key)) {
                $decodedData[$key] = json_decode($request->input($key), true);
            }
        }

        DB::beginTransaction();
        try {
            $prestamo = credito::findOrFail($id);
            $prestamo->tipo = $request->tipo_credito;
            $prestamo->producto = $request->tipo_producto;
            $prestamo->subproducto = $request->subproducto;
            $prestamo->destino = $request->destino_credito;
            $prestamo->recurrencia = $request->recurrencia;
            $prestamo->tasa = $request->tasa_interes;
            $prestamo->tiempo = $request->tiempo_credito;
            $prestamo->monto_total = $request->monto;
            $prestamo->fecha_desembolso = $request->fecha_desembolso;
            $prestamo->periodo_gracia_dias = $request->periodo_gracia_dias;
            $prestamo->porcentaje_credito = $request->porcentaje_venta_credito;
            $prestamo->estado = "pendiente";
            $prestamo->categoria = 'produccion';
            $prestamo->nombre_prestamo = $request->nombre_prestamo;
            $prestamo->cantidad_integrantes = $request->cantidad_grupo;
            $prestamo->descripcion_negocio = $request->descripcion_negocio;
            $prestamo->user_id = Auth::id();
            $prestamo->save();

            // Actualizar la garantía
            $garantia = Garantia::where('id_prestamo', $prestamo->id)->first();
            if (!$garantia) {
                $garantia = new Garantia();
                $garantia->id_prestamo = $prestamo->id;
            }
            $garantia->descripcion = $request->descripcion_garantia;
            $garantia->valor_mercado = $request->valor_mercado;
            $garantia->valor_realizacion = $request->valor_realizacion;
            $garantia->valor_gravamen = $request->valor_gravamen;
            if ($request->hasFile('archivo_garantia') && $request->file('archivo_garantia')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo_garantia')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo_garantia')->storeAs('public/documentos_garantia', $nombreArchivo);
                $garantia->documento_pdf = $ruta;
            }
            $garantia->save();

            // Actualizar activos
            $activos = Activos::where('prestamo_id', $prestamo->id)->first();
            if (!$activos) {
                $activos = new Activos();
                $activos->prestamo_id = $prestamo->id;
            }
            $activos->cuentas_por_cobrar = $request->cuentas_por_cobrar;
            $activos->saldo_en_caja_bancos = $request->saldo_caja_bancos;
            $activos->adelanto_a_proveedores = $request->adelanto_a_proveedores;
            $activos->otros = $request->otros;
            $activos->save();

            // Actualizar créditos de clientes y cronograma
            CreditoCliente::where('prestamo_id', $prestamo->id)->delete();
            $this->updateClienteYcronograma($prestamo, $request);
            $this->updateArrayData($decodedData, $prestamo->id, $request);
            DB::commit();
            return response()->json(['message' => 'Crédito actualizado con éxito', 'data' => $prestamo], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el crédito: ' . $e->getMessage()], 500);
        }
    }
    public function updateCreditoagricola(Request $request, $id)
{
    $decodedData = $request->all();
    foreach ([
        'proyeccionesArray', 'inventarioArray', 'deudasFinancierasArray', 'gastosOperativosArray', 'gastosAgricolaArray',
        'inventarioArray1', 'ventasMensualesArray', 'tipoProductoArray'
    ] as $key) {
        if ($request->filled($key)) {
            $decodedData[$key] = json_decode($request->input($key), true);
        }
    }

    DB::beginTransaction();
    try {
        $prestamo = credito::findOrFail($id);
        $prestamo->tipo = $request->tipo_credito;
        $prestamo->producto = $request->tipo_producto;
        $prestamo->subproducto = $request->subproducto;
        $prestamo->destino = $request->destino_credito;
        $prestamo->recurrencia = $request->recurrencia;
        $prestamo->tasa = $request->tasa_interes;
        $prestamo->tiempo = $request->tiempo_credito;
        $prestamo->monto_total = $request->monto;
        $prestamo->fecha_desembolso = $request->fecha_desembolso;
        $prestamo->periodo_gracia_dias = $request->periodo_gracia_dias;
        $prestamo->porcentaje_credito = $request->porcentaje_venta_credito;
        $prestamo->estado = "pendiente";
        $prestamo->categoria = 'produccion';
        $prestamo->nombre_prestamo = $request->nombre_prestamo;
        $prestamo->cantidad_integrantes = $request->cantidad_grupo;
        $prestamo->descripcion_negocio = $request->descripcion_negocio;
        $prestamo->user_id = Auth::id();
        $prestamo->save();

        // Actualizar la garantía
        $garantia = Garantia::where('id_prestamo', $prestamo->id)->first();
        if (!$garantia) {
            $garantia = new Garantia();
            $garantia->id_prestamo = $prestamo->id;
        }
        $garantia->descripcion = $request->descripcion_garantia;
        $garantia->valor_mercado = $request->valor_mercado;
        $garantia->valor_realizacion = $request->valor_realizacion;
        $garantia->valor_gravamen = $request->valor_gravamen;
        if ($request->hasFile('archivo_garantia') && $request->file('archivo_garantia')->isValid()) {
            $nombreUnico = Str::uuid();
            $extension = $request->file('archivo_garantia')->getClientOriginalExtension();
            $nombreArchivo = $nombreUnico . '.' . $extension;
            $ruta = $request->file('archivo_garantia')->storeAs('public/documentos_garantia', $nombreArchivo);
            $garantia->documento_pdf = $ruta;
        }
        $garantia->save();

        // Actualizar activos
        $activos = Activos::where('prestamo_id', $prestamo->id)->first();
        if (!$activos) {
            $activos = new Activos();
            $activos->prestamo_id = $prestamo->id;
        }
        $activos->cuentas_por_cobrar = $request->cuentas_por_cobrar;
        $activos->saldo_en_caja_bancos = $request->saldo_caja_bancos;
        $activos->adelanto_a_proveedores = $request->adelanto_a_proveedores;
        $activos->otros = $request->otros;
        $activos->save();

        // Actualizar créditos de clientes y cronograma
        CreditoCliente::where('prestamo_id', $prestamo->id)->delete();
        $this->updateClienteYcronograma($prestamo, $request);

        // Guardar datos del crédito agrícola
        $this->updateArrayData($decodedData, $prestamo->id, $request);

        DB::commit();
        return response()->json(['message' => 'Crédito actualizado con éxito', 'data' => $prestamo], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error al actualizar el crédito: ' . $e], 500);
    }
}


}

