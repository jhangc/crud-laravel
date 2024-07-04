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
use App\Models\GastoProducir;
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

            // Actualizar datos relacionados
            $this->updateArrayData($decodedData, $prestamo->id, $request);

            // Actualizar créditos de clientes y cronograma
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
        // Actualizar proyecciones
        ProyeccionesVentas::where('id_prestamo', $prestamoId)->delete();
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
                ]);
            }
        }

        // Actualizar ventas diarias
        VentasDiarias::where('prestamo_id', $prestamoId)->delete();
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

        // Actualizar deudas financieras
        DeudasFinancieras::where('prestamo_id', $prestamoId)->delete();
        if (isset($data['deudasFinancierasArray']) && is_array($data['deudasFinancierasArray'])) {
            foreach ($data['deudasFinancierasArray'] as $deudaData) {
                DeudasFinancieras::create([
                    'nombre_entidad' => $deudaData['entidad'],
                    'saldo_capital' => $deudaData['saldoCapital'],
                    'cuota' => $deudaData['cuota'],
                    'tiempo_restante' => $deudaData['tiempoRestante'],
                    'prestamo_id' => $prestamoId,
                    'estado' => 'activo'
                ]);
            }
        }

        // Actualizar gastos operativos
        GastosOperativos::where('id_prestamo', $prestamoId)->delete();
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

        // Actualizar gastos familiares
        GastosFamiliares::where('id_prestamo', $prestamoId)->delete();
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

        // Actualizar inventario
        Inventario::where('id_prestamo', $prestamoId)->where('tipo_inventario', 1)->delete();
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
    }

    protected function updateClienteYcronograma($prestamo, $request)
    {
        $cliente = cliente::where('documento_identidad', $request->documento_identidad)->where('activo', 1)->first();
        if ($cliente) {
            CreditoCliente::where('prestamo_id', $prestamo->id)->delete();
            $credito_cliente = CreditoCliente::where('prestamo_id', $prestamo->id)->where('cliente_id', $cliente->id)->first();
            if (!$credito_cliente) {
                $credito_cliente = new CreditoCliente();
                $credito_cliente->prestamo_id = $prestamo->id;
                $credito_cliente->cliente_id = $cliente->id;
            }
            $credito_cliente->monto_indivual = $request->monto;
            $credito_cliente->save();

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
        $frecuencia =  $request->tipo_producto == 'grupal' ? $request->recurrencia1 : $request->recurrencia;
        $tasaInteres = $request->tasa_interes;
        $tasaDiaria = pow(1 + ($tasaInteres / 100), 1 / 360) - 1;
        $interesesPeriodoGracia = $montoTotal * $tasaDiaria * $request->periodo_gracia_dias;
        $cuotaSinGracia = $this->calcularCuota($montoTotal, $tasaInteres, $tiempo,$frecuencia);
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $tiempo;
        switch ($frecuencia) {
            case 'catorcenal':
                $fechaCuota = $fechaconperiodogracia->copy()->addDays(14);
                for ($i = 1; $i <= $tiempo; $i++) {
                    $cronograma = new Cronograma();
                    $cronograma->fecha = $fechaCuota;
                    $cronograma->monto = $cuotaSinGracia + $interesesMensualesPorGracia+0.02*$cuotaSinGracia; // Cuota fija más intereses distribuidos
                    $cronograma->numero = $i;
                    $cronograma->id_prestamo = $prestamo->id;
                    $cronograma->cliente_id = $cliente->id; // Asignar cliente
                    $cronograma->save();
                    $fechaCuota = $fechaCuota->addDays(14);
                }
                break;
            case 'quincenal':
                $fechaCuota = $fechaconperiodogracia->copy()->addDays(15);
                for ($i = 1; $i <= $tiempo; $i++) {
                    $cronograma = new Cronograma();
                    $cronograma->fecha = $fechaCuota;
                    $cronograma->monto = $cuotaSinGracia + $interesesMensualesPorGracia; // Cuota fija más intereses distribuidos
                    $cronograma->numero = $i;
                    $cronograma->id_prestamo = $prestamo->id;
                    $cronograma->cliente_id = $cliente->id; // Asignar cliente
                    $cronograma->save();
                    $fechaCuota = $fechaCuota->addDays(14);
                }
                break;
            case 'veinteochenal':
                $fechaCuota = $fechaconperiodogracia->copy()->addDays(28);
                for ($i = 1; $i <= $tiempo; $i++) {
                    $cronograma = new Cronograma();
                    $cronograma->fecha = $fechaCuota;
                    $cronograma->monto = $cuotaSinGracia + $interesesMensualesPorGracia+0.02*$cuotaSinGracia; // Cuota fija más intereses distribuidos
                    $cronograma->numero = $i;
                    $cronograma->id_prestamo = $prestamo->id;
                    $cronograma->cliente_id = $cliente->id; // Asignar cliente
                    $cronograma->save();
                    $fechaCuota = $fechaCuota->addDays(28);
                }
                break;
            case 'semestral':
                $fechaCuota = $fechaconperiodogracia->copy()->addMonths(6);
                for ($i = 1; $i <= $tiempo; $i++) {
                    $cronograma = new Cronograma();
                    $cronograma->fecha = $fechaCuota;
                    $cronograma->monto = $cuotaSinGracia + $interesesMensualesPorGracia; // Cuota fija más intereses distribuidos
                    $cronograma->numero = $i;
                    $cronograma->id_prestamo = $prestamo->id;
                    $cronograma->cliente_id = $cliente->id; // Asignar cliente
                    $cronograma->save();
                    $fechaCuota = $fechaCuota->addMonths(6);
                }
                break;
            case 'mensual':
            default:
                $fechaCuota = $fechaconperiodogracia->copy()->addMonth();
                for ($i = 1; $i <= $tiempo; $i++) {
                    $cronograma = new Cronograma();
                    $cronograma->fecha = $fechaCuota;
                    $cronograma->monto = $cuotaSinGracia + $interesesMensualesPorGracia; // Cuota fija más intereses distribuidos
                    $cronograma->numero = $i;
                    $cronograma->id_prestamo = $prestamo->id;
                    $cronograma->cliente_id = $cliente->id; // Asignar cliente
                    $cronograma->save();
                    $fechaCuota = $fechaCuota->addMonth();;
                }
                break;
        }
    }

    public function calcularCuota($monto, $tea, $periodos, $frecuencia)
    {
    // Determinar el número de periodos por año según la frecuencia de pago
        switch ($frecuencia) {
            case 'catorcenal':
            $n = 24;
            break;
            case 'quincenal':
                $n = 12;
                break;
            case 'semestral':
                $n = 2;
                break;
            case 'veinteochenal':
                $n = 12;
                break;
            case 'mensual':
            default:
                $n = 12;
                break;
        }
    
        // Calcular la tasa efectiva del periodo
        $tasaPeriodo = pow(1 + ($tea / 100), 1 / $n) - 1;
    
        // Calcular la cuota usando la fórmula de amortización francesa
        return ($monto * $tasaPeriodo * pow(1 + $tasaPeriodo, $periodos)) / (pow(1 + $tasaPeriodo, $periodos) - 1);
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
            $prestamo = Credito::findOrFail($id);
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
}
