<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class clienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener solo los clientes activos (activo = 1)
        $clientes = Cliente::where('activo', 1)->get();

        // Pasar los clientes activos a la vista
        return view('admin.clientes.index', ['clientes' => $clientes]);
    }
    public function viewevaluar()
    {
        return view('admin.clientes.evaluar');
    }


    public function viewratios()
    {
        return view('admin.clientes.ratios');
    }

    public function viewreportecliente()
    {
        return view('admin.reportes.clientes');
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'nombre' => 'required|max:100',
            'documento_identidad' => 'required|max:50',
            'telefono' => 'nullable|max:15',
            'email' => 'nullable|email|unique:clientes,email',
            'direccion' => 'required|max:255',
            'direccion_laboral' => 'nullable|max:255',
            'lugar_nacimiento' => 'nullable|max:255',
            'fecha_nacimiento' => 'nullable|max:255',
            'profesion' => 'nullable|max:100',
            'estado_civil' => 'nullable|max:50',
            'conyugue' => 'nullable|max:100',
            'dni_conyugue' => 'nullable|max:50',
            'foto' => 'nullable|image',  // Asumiendo que se cargará una imagen para el campo foto
            'dni_pdf' => 'nullable|mimes:pdf',  // Asegurando que el archivo sea un PDF
            'activo' => 'boolean',
            'actividad_economica' => 'nullable|max:255',
            'sexo' => 'nullable|max:255',
            'referencia' => 'nullable|max:255',
            'aval' => 'nullable|max:255',
            'dni_aval' => 'nullable|mimes:pdf',
        ]);


        // Crear un nuevo cliente en la base de datos
        $cliente = new Cliente();
        $cliente->nombre = $request->nombre;
        $cliente->documento_identidad = $request->documento_identidad;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;
        $cliente->direccion = $request->direccion;
        $cliente->direccion_laboral = $request->direccion_laboral;
        $cliente->lugar_nacimiento = $request->lugar_nacimiento;
        if (!empty($request->fecha_nacimiento)) {
            $cliente->fecha_nacimiento = $request->fecha_nacimiento;
        }
        $cliente->profesion = $request->profesion;
        $cliente->estado_civil = $request->estado_civil;
        $cliente->conyugue = $request->conyugue;
        $cliente->dni_conyugue = $request->dni_conyugue;

        // $path = $request->file('foto')->store('public/fotos_clientes');
        // if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
        //     $path = $request->file('foto')->store('fotos_clientes'); // Guarda la foto en el storage público
        //     $cliente->foto = $path; // Guarda la ruta del archivo en la base de datos
        // }

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            // Genera un UUID único para el nombre del archivo
            $nombreUnico = Str::uuid();
            // Obtiene la extensión original del archivo
            $extension = $request->file('foto')->getClientOriginalExtension();
            // Construye el nombre del archivo utilizando el UUID y la extensión
            $nombreArchivo = $nombreUnico . '.' . $extension;
            // Guarda la foto en el directorio especificado con el nombre generado
            $ruta = $request->file('foto')->storeAs('public/fotos_clientes', $nombreArchivo);
            // Asigna la ruta del archivo al atributo 'foto' del cliente
            $cliente->foto = $ruta;
        }

        if ($request->hasFile('dni_pdf')) {
            // Genera un nombre único para el archivo utilizando un UUID
            $nombreUnico = Str::uuid();

            // Obtiene la extensión original del archivo
            $extension = $request->file('dni_pdf')->getClientOriginalExtension();

            // Construye el nombre del archivo utilizando el UUID y la extensión
            $nombreArchivo = $nombreUnico . '.' . $extension;

            // Guarda el PDF en el directorio especificado con el nombre generado
            $ruta = $request->file('dni_pdf')->storeAs('public/documentos_clientes', $nombreArchivo);

            // Asigna la ruta del archivo al atributo 'dni_pdf' del cliente
            $cliente->dni_pdf = $ruta;
        }

        $cliente->activo = $request->activo ?? true; // Si no se proporciona el valor de activo, se establece en true
        $cliente->sucursal_id = 1; // Asignar el ID de la sucursal recién creada

        // Asignar los nuevos campos
        $cliente->actividad_economica = $request->actividad_economica;
        $cliente->sexo = $request->sexo;
        $cliente->referencia = $request->referencia;
        $cliente->aval = $request->aval;

        if ($request->hasFile('dni_aval')) {
            // Genera un nombre único para el archivo utilizando un UUID
            $nombreUnico = Str::uuid();

            // Obtiene la extensión original del archivo
            $extension = $request->file('dni_aval')->getClientOriginalExtension();

            // Construye el nombre del archivo utilizando el UUID y la extensión
            $nombreArchivo = $nombreUnico . '.' . $extension;

            // Guarda el archivo en el directorio especificado con el nombre generado
            $ruta = $request->file('dni_aval')->storeAs('public/documentos_aval', $nombreArchivo);

            // Asigna la ruta del archivo al atributo 'dni_aval' del cliente
            $cliente->dni_aval = $ruta;
        }

        $cliente->save();
        // Redireccionar a la página de inicio
        return redirect()->route('clientes.index')
            ->with('mensaje', 'Se registró al cliente de manera correcta')
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
        $cliente = cliente::findOrFail($id);
        return view('admin.clientes.edit', ['cliente' => $cliente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|max:100',
            'documento_identidad' => 'required|max:50',
            'telefono' => 'nullable|max:15',
            'email' => 'required|unique:clientes,email,' . $id,
            'direccion' => 'required|max:255',
        ]);

        // Buscar al cliente por su ID
        $cliente = cliente::find($id);

        // Actualizar los datos del cliente
        $cliente->nombre = $request->nombre;
        $cliente->documento_identidad = $request->documento_identidad;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;
        $cliente->direccion = $request->direccion;
        $cliente->save();

        // Redireccionar a la página de inicio
        return redirect()->route('clientes.index')
            ->with('mensaje', 'Se actualizó al cliente de manera correcta')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        // Buscar al cliente por su ID
        $cliente = cliente::find($id);

        if (!$cliente) {
            // Si el cliente no existe, redireccionar con un mensaje de error
            return redirect()->route('clientes.index')
                ->with('mensaje', 'El cliente que intentas eliminar no existe.')
                ->with('icono', 'error');
        }

        // Cambiar el estado activo a 0 (inactivo)
        $cliente->activo = 0;
        $cliente->save();

        // Redireccionar a la página de inicio
        return redirect()->route('clientes.index')
            ->with('mensaje', 'Se desactivó al cliente de manera correcta')
            ->with('icono', 'success');
    }



    public function buscarPorDocumento(Request $request)
    {
        $dni = $request->input('documento_identidad');
        $cliente = Cliente::where('documento_identidad', $dni)
                      ->where('activo', 1) // Agregar condición para buscar solo clientes activos
                      ->first(['nombre', 'telefono', 'email', 'direccion', 'direccion_laboral', 'profesion']);

        if ($cliente) {
            return response()->json($cliente);
        } else {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
    }

    public function agregarpordni(Request $request)
    {
        $dni = $request->input('documento_identidad');
        $cliente = cliente::where('documento_identidad', $dni)->first(['nombre', 'documento_identidad','telefono', 'direccion', 'profesion']);

        if ($cliente) {
            return response()->json($cliente);
        } else {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
    }
}

//dsadasdas
