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
        $creditos = credito::where('activo', 1)->get();
      
        // Pasar los clientes activos a la vista
        return view('admin.creditos.index', ['creditos' => $creditos]);
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
        $request->validate([
            'tipo_credito' => 'required|max:50',
            'tipo_producto' => 'required|max:100',
            'subproducto' => 'nullable|max:100',
            'destino_credito' => 'nullable|max:100',
            // 'id_cliente' => 'required|exists:clientes,id',
            'recurrencia' => 'nullable|max:50',
            'tasa_interes' => 'nullable|numeric|min:0|max:100',
            'tiempo_credito' => 'nullable|integer|min:1',
            'monto_total' => 'nullable|numeric|min:0',
            'fecha_desembolso' => 'nullable|date',
            'descripcion_negocio' => 'nullable|max:255',
            // 'fecha_registro' => 'nullable|date',
            // 'fecha_fin' => 'nullable|date',
            // 'nombre_prestamo' => 'nullable|max:200',
            // 'cantidad_grupo' => 'nullable|integer|min:1',
            // 'estado' => 'nullable|max:20',
            // 'categoria' => 'nullable|max:20',
            // 'foto_grupal' => 'nullable|image',
            'activo' => 'boolean',
        ]);

        // Creación de una nueva instancia del modelo Prestamos
        $prestamo = new Credito();

        $credito_cliente = new CreditoCliente();

        // Asignación de los valores a los atributos del modelo
        $prestamo->tipo = $request->tipo_credito;
        $prestamo->producto = $request->tipo_producto;
        $prestamo->subproducto = $request->subproducto;
        $prestamo->destino = $request->destino_credito;
        

        $prestamo->recurrencia = $request->recurrencia;
        $prestamo->tasa = $request->tasa_interes;
        $prestamo->tiempo = $request->tiempo_credito;
        $prestamo->monto_total = $request->monto;
        $prestamo->fecha_desembolso = $request->fecha_desembolso;
        // $prestamo->fecha_registro = $request->fecha_registro;
        // $prestamo->fecha_fin = $request->fecha_fin;
        
        $prestamo->estado = "pendiente";
        // Condicional para asignar la categoría
        if ($request->tipo_producto !== 'grupal') {
            $prestamo->categoria = 'individual';
            $prestamo->nombre_prestamo = "prestamo indivual";
            $prestamo->cantidad_integrantes = 1;
            $prestamo->descripcion_negocio = $request->descripcion_negocio;
        } else {
            $prestamo->categoria = 'grupal';
            $prestamo->nombre_prestamo = $request->nombre_prestamo;
            $prestamo->cantidad_integrantes = $request->cantidad_grupo;
        }

        // Calcular la fecha de finalización
        $fecha_desembolso = Carbon::parse($request->fecha_desembolso);
        $tiempo_credito = $request->tiempo_credito;
        $prestamo->fecha_fin = $fecha_desembolso->copy()->addMonths($tiempo_credito);
        

        // Manejo de la subida de archivos para 'foto_grupal'
        if ($request->hasFile('foto_grupal') && $request->file('foto_grupal')->isValid()) {
            $nombreUnico = Str::uuid();
            $extension = $request->file('foto_grupal')->getClientOriginalExtension();
            $nombreArchivo = $nombreUnico . '.' . $extension;
            $ruta = $request->file('foto_grupal')->storeAs('public/fotos_grupales', $nombreArchivo);
           $prestamo->foto_grupal = $ruta;
        }

        // Asignación del valor de 'activo'
        $prestamo->activo = $request->activo ?? true;

        // Guardar el nuevo préstamo en la base de datos
        $prestamo->save();


        // Obtén el cliente por su DNI
        $cliente = cliente::where('documento_identidad', $request->documento_identidad)->where('activo', 1)->first();
        // $prestamo->id_cliente = 1;
        $credito_cliente->prestamo_id = $prestamo->id;
        $credito_cliente->cliente_id = $cliente->id;
        $credito_cliente->save();



        // Obtener la fecha de desembolso y el tiempo de crédito del request
      

    $fechaDesembolso = Carbon::parse($request->fecha_desembolso);
    $tiempoMeses = $request->tiempo_credito;
    // Calcular la tasa de interés mensual y quincenal
    $tasaInteresMensual = $request->tasa_interes/1200;
    $tasaInteresQuincenal = $request->tasa_interes/1200 * 2; // Convertir a tasa mensual

    $montoTotal = $request->monto;
    $montoPorCuota = ($montoTotal / $tiempoMeses);

    $fechaCuota = $fechaDesembolso->copy()->addMonth();
    for ($i = 1; $i <= $tiempoMeses; $i++) {

         //Calcular el interés dependiendo del tipo de tasa de interés
          if ($request->tasa_interes === 'mensual') {
               $interes = $montoPorCuota * $tasaInteresMensual;
           } else { // Si es quincenal
             $interes = $montoPorCuota * $tasaInteresQuincenal;
        }

        //  if ($request->tasa_interes === 'mensual') {
        //       $interes = $montoPorCuota * (pow(1 + $tasaInteresMensual, $tiempoMeses) - 1);
        //  } else { // Si es quincenal
        //       $interes = $montoPorCuota * (pow(1 + $tasaInteresQuincenal, $tiempoMeses) - 1);
        //  }

        // Crear una nueva instancia de Cronograma
        $cronograma = new Cronograma();
        
        // Asignar los datos correspondientes
        $cronograma->fecha = $fechaCuota;
        $cronograma->monto = $montoPorCuota + $interes; // Sumar el interés al monto de la cuota
        // $cronograma->monto = $montoPorCuota + $interes;
        $cronograma->numero = $i;
        $cronograma->id_prestamo = $prestamo->id;

        // Guardar el cronograma en la base de datos
        $cronograma->save();

        // Añadir un mes a la fecha de la cuota para la siguiente iteración
        $fechaCuota = $fechaCuota->addMonth();
    }

        
        // $prestamo->fecha_registro = $request->fecha_registro;
        // $prestamo->fecha_fin = $request->fecha_fin;

        // Redireccionar a la página de inicio con un mensaje de éxito
        return redirect()->route('creditos.index')
            ->with('mensaje', 'Se registró el préstamo de manera correcta')
            ->with('icono', 'success');
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
