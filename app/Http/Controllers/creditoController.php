<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\credito;
use App\Models\cliente;
use App\Models\CreditoCliente;
use App\Models\Cronograma;
use Carbon\Carbon;
use Illuminate\Support\Str;

class creditoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener solo los clientes activos (activo = 1)
        $creditos = credito::with('clientes')->where('activo', 1)->get();

        // Pasar los clientes activos a la vista
        return view('admin.creditos.index', ['creditos' => $creditos]);
    }

    public function getdescripciones(Request $request){
        $descripciones =\App\Models\MargenVenta::where('actividad_economica',$request->opcion)->get();

        return response()->json([
            'state' => '0',
            'mensaje' => 'Prestamo creado con exito',
            'data' => $descripciones
        ])->setStatusCode(200);
    }
    public function viewaprobar()
    {
        return view('admin.creditos.aprobar');
    }

    public function viewsupervisar()
    {
        return view('admin.creditos.supervisar');
    }

    public function viewarqueo()
    {
        return view('admin.caja.arqueo');
    }

    public function viewhabilitarcaja()
    {
        return view('admin.caja.habilitar');
    }

    public function viewpagarcredito()
    {
        return view('admin.caja.pagarcredito');
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
        foreach (['clientesArray', 'proyeccionesArray', 'inventarioArray', 'deudasFinancierasArray', 'gastosOperativosArray', 'boletasArray', 'gastosProducirArray'] as $key) {
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

        $prestamo = new Credito();
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
            $prestamo->descripcion_negocio = "sin descripcion";
        }

        $prestamo->save();
        //garantia
         $garantia=\App\Models\Garantia::create([
            'descripcion' =>$request->descripcion_garantia,
            'valor_mercado' =>$request->valor_mercado ,
            'valor_realizacion' => $request->valor_realizacion,
            'valor_gravamen' =>$request->valor_gravamen ,
            'id_prestamo' => $prestamo->id,
            'estado' => 'activo'
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

        if ($request->tipo_producto !== 'grupal') {
            $cliente = Cliente::where('documento_identidad', $request->documento_identidad)->where('activo', 1)->first();
            if ($cliente) {
                $credito_cliente = new CreditoCliente();
                $credito_cliente->prestamo_id = $prestamo->id;
                $credito_cliente->cliente_id = $cliente->id;
                $credito_cliente->save();
            }
        } else {
            if (is_array($decodedData['clientesArray'])) {
                foreach ($decodedData['clientesArray'] as $clienteData) {
                    $cliente = Cliente::where('documento_identidad', $clienteData['documento'])->where('activo', 1)->first();
                    if ($cliente) {
                        $credito_cliente = new CreditoCliente();
                        $credito_cliente->prestamo_id = $prestamo->id;
                        $credito_cliente->cliente_id = $cliente->id;
                        $credito_cliente->save();
                    }
                }
            }
        }
        $this->saveArrayData($decodedData, $prestamo->id);
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
        $fechaDesembolso = Carbon::parse($request->fecha_desembolso);

        // $fechaconperiodogracia = clone $fechaDesembolso;
        // $fechaconperiodogracia->addDays($request->periodo_gracia_dias);
        // $tasaInteresMensual = $request->tasa_interes / 12;
        // $tasaInteresQuincenal = $tasaInteresMensual * 2;
        // $tasaInteresdia = $request->tasa_interes / 365;
        // $montoTotal = $request->monto;
        // $monto_interes_diario = $montoTotal * (pow((1 + $tasaInteresdia / 100), $request->periodo_gracia_dias));
        // $fechaCuota = $fechaconperiodogracia->copy()->addMonth();

        // for ($i = 1; $i <= $tiempo_credito; $i++) {
        //     if ($request->recurrencia === 'mensual') {
        //         $monto_interes = $montoTotal * (pow((1 + $tasaInteresMensual / 100), $tiempo_credito));
        //     } else {
        //         $monto_interes = $montoTotal * (pow((1 + $tasaInteresQuincenal / 100), $tiempo_credito));
        //     }

        //     $cronograma = new Cronograma();
        //     $cronograma->fecha = $fechaCuota;

        //     if ($i == 1) {
        //         $cronograma->monto = ($monto_interes / $tiempo_credito) + $monto_interes_diario - $montoTotal;
        //     } else {
        //         $cronograma->monto = $monto_interes / $tiempo_credito;
        //     }

        //     $cronograma->numero = $i;
        //     $cronograma->id_prestamo = $prestamo->id;
        //     $cronograma->save();

        //     $fechaCuota->addMonth();
        // }
        $fechaDesembolso = Carbon::parse($request->fecha_desembolso);
        //fecha incluyendo periodo de gracias
        $fechaconperiodogracia = clone $fechaDesembolso;
        $fechaconperiodogracia->modify("+$request->periodo_gracia_dias days");
        $tiempo = $request->tiempo_credito;
        $montoTotal = $request->monto;
        $tasaInteres=$request->tasa_interes;
        // Calcular la tasa diaria
        $tasaDiaria = pow(1 + ($tasaInteres / 100), 1 / 360) - 1;
        // Calcular los intereses del período de gracia
        $interesesPeriodoGracia = $montoTotal * $tasaDiaria * $request->periodo_gracia_dias;
        // Calcular la cuota mensual fija sin intereses del período de gracia
        $cuotaSinGracia = $this->calcularCuota($montoTotal, $tasaInteres, $tiempo);
        // Calcular el monto adicional por intereses de gracia a agregar a cada cuota
        $interesesMensualesPorGracia = $interesesPeriodoGracia / $tiempo;
        // Generar el cronograma de pagos
        $fechaCuota = $fechaconperiodogracia->copy()->addMonth();
        for ($i = 1; $i <= $tiempo; $i++) {
            $cronograma = new Cronograma();
            $cronograma->fecha = $fechaCuota;
            $cronograma->monto = $cuotaSinGracia + $interesesMensualesPorGracia; // Cuota fija más intereses distribuidos
            $cronograma->numero = $i;
            $cronograma->id_prestamo = $prestamo->id;
            $cronograma->save();
            // Incrementar la fecha para la siguiente cuota
            $fechaCuota = $fechaCuota->addMonth();
        }
        // $cuota = $this->calcularCuota($montoTotal, $$tasaInteres, $tiempo);
        // for ($i = 1; $i <= $tiempo; $i++) {
        //     $cronograma = new Cronograma();
        //     $cronograma->fecha = $fechaCuota;
        //     $cronograma->monto = $cuota; // Sumar el interés al monto de la cuota
        //     $cronograma->numero = $i;
        //     $cronograma->id_prestamo = $prestamo->id;
        //     $cronograma->save();
        //    $fechaCuota = $fechaCuota->addMonth();
        // }
        return response()->json([
            'state' => '0',
            'mensaje' => 'Prestamo creado con exito',
            'prestamo' => $prestamo
        ])->setStatusCode(200);
    }

    protected function saveArrayData(array $data, $prestamoId)
    {
        if (is_array($data['proyeccionesArray'])) {
            foreach ($data['proyeccionesArray'] as $proyeccionData) {
                \App\Models\ProyeccionesVentas::create([
                    'descripcion_producto' => $proyeccionData['descripcion'],
                    'unidad_medida' => $proyeccionData['unidadMedida'],
                    'frecuencia_compra' => $proyeccionData['frecuenciaCompra'],
                    'unidades_compradas' => $proyeccionData['unidadesCompradas'],
                    'unidades_vendidas' => $proyeccionData['unidadesVendidas'],
                    'stock_verificado' => $proyeccionData['stockVerificado'],
                    'precio_compra' => $proyeccionData['precioCompra'],
                    'precio_venta' => $proyeccionData['precioVenta'],
                    'id_prestamo' => $prestamoId,
                    'estado' => 'activo'
                ]);
            }
        }

        if (is_array($data['deudasFinancierasArray'])) {
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

        if (is_array($data['gastosOperativosArray'])) {
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

        if (is_array($data['inventarioArray'])) {
            foreach ($data['inventarioArray'] as $inventarioData) {
                \App\Models\Inventario::create([
                    'descripcion' => $inventarioData['descripcion'],
                    'precio_unitario' => $inventarioData['precioUnitario'],
                    'cantidad' => $inventarioData['cantidad'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }

        if (is_array($data['boletasArray'])) {
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

        if (is_array($data['gastosProducirArray'])) {
            foreach ($data['gastosProducirArray'] as $gastoProducirData) {
                \App\Models\GastosProducir::create([
                    'descripcion_gasto' => $gastoProducirData['descripcionGasto'],
                    'precio_unitario' => $gastoProducirData['precioUnitario'],
                    'cantidad' => $gastoProducirData['cantidad'],
                    'total_gasto' => $gastoProducirData['totalGasto'],
                    'id_prestamo' => $prestamoId
                ]);
            }
        }
    }
    
     public function calcularCuota($monto, $tea, $periodos) {
        $tasaMensual = pow(1 + ($tea/100), 1 / 12) - 1;
        return   ($monto * $tasaMensual * pow((1 + $tasaMensual), $periodos)) / (pow((1 + $tasaMensual), $periodos) - 1);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

        // return view('creditos.index', compact('cuotas'));
        return view('creditos.index', compact('cuotas', 'credito'));
    }
}
