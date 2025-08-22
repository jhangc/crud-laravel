<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
        $departamentos = Departamento::all();
        $provincias = collect();
        $distritos = collect();

        return view('admin.clientes.create', compact('departamentos', 'provincias', 'distritos'));
    }

    public function getProvincias($dep_id)
    {
        $provincias = Provincia::where('dep_id', $dep_id)->pluck('pro_nombre', 'pro_id');
        return response()->json($provincias);
    }

    public function getDistritos($prov_id)
    {
        $distritos = Distrito::where('pro_id', $prov_id)->pluck('dis_nombre', 'dis_id');
        return response()->json($distritos);
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
            'distrito' => 'required|exists:distritos,dis_id',
            'direccion' => 'required|max:255',
            'direccion_laboral' => 'nullable|max:255',
            'lugar_nacimiento' => 'nullable|max:255',
            'fecha_nacimiento' => 'nullable|max:255',
            'profesion' => 'nullable|max:100',
            'estado_civil' => 'nullable|max:50',
            'conyugue' => 'nullable|max:100',
            'dni_conyugue' => 'nullable|max:50',
            'direccion_conyugue' => 'nullable|max:255',  // Nuevo campo
            'foto' => 'nullable|image',  // Asumiendo que se cargará una imagen para el campo foto
            'dni_pdf' => 'nullable|mimes:pdf',  // Asegurando que el archivo sea un PDF
            'activo' => 'boolean',
            'actividad_economica' => 'nullable|max:255',
            'sexo' => 'nullable|max:255',
            'referencia' => 'nullable|max:255',
            'aval' => 'nullable|max:255',
            'numero_dni_aval' => 'nullable|max:50',  // Nuevo campo
            'direccion_aval' => 'nullable|max:255',  // Nuevo campo
        ]);



        // Crear un nuevo cliente en la base de datos
        $cliente = new Cliente();
        $cliente->nombre = $request->nombre;
        $cliente->documento_identidad = $request->documento_identidad;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;
        $cliente->direccion = $request->direccion;
        $cliente->distrito_id = $request->distrito; // Guardar distrito_id
        $cliente->direccion_laboral = $request->direccion_laboral;
        $cliente->lugar_nacimiento = $request->lugar_nacimiento;
        if (!empty($request->fecha_nacimiento)) {
            $cliente->fecha_nacimiento = $request->fecha_nacimiento;
        }
        $cliente->profesion = $request->profesion;
        $cliente->estado_civil = $request->estado_civil;
        $cliente->conyugue = $request->conyugue;
        $cliente->dni_conyugue = $request->dni_conyugue;

        $cliente->direccion_conyugue = $request->direccion_conyugue;  // Nuevo campo
        $cliente->numero_dni_aval = $request->numero_dni_aval;  // Nuevo campo
        $cliente->direccion_aval = $request->direccion_aval;  // Nuevo campo



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
        $cliente = Cliente::with('distrito.provincia.departamento')->findOrFail($id);
        $departamentos = Departamento::all();

        $departamentoNombre = '';
        $provinciaNombre = '';
        $distritoNombre = '';
        $provincias = collect();
        $distritos = collect();

        if ($cliente->distrito) {
            $distritoNombre = $cliente->distrito->dis_nombre;

            if ($cliente->distrito->provincia) {
                $provinciaNombre = $cliente->distrito->provincia->pro_nombre;
                $provincias = Provincia::where('dep_id', $cliente->distrito->provincia->dep_id)->get();

                if ($cliente->distrito->provincia->departamento) {
                    $departamentoNombre = $cliente->distrito->provincia->departamento->dep_nombre;
                }

                $distritos = Distrito::where('pro_id', $cliente->distrito->provincia->pro_id)->get();
            }
        }

        return view('admin.clientes.edit', [
            'cliente' => $cliente,
            'departamentos' => $departamentos,
            'provincias' => $provincias,
            'distritos' => $distritos,
            'departamentoNombre' => $departamentoNombre,
            'provinciaNombre' => $provinciaNombre,
            'distritoNombre' => $distritoNombre,
        ]);
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
            'email' => 'nullable|email|unique:clientes,email,' . $id,
            'distrito' => 'required|exists:distritos,dis_id',
            'direccion' => 'required|max:255',
            'direccion_laboral' => 'nullable|max:255',
            'lugar_nacimiento' => 'nullable|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'profesion' => 'nullable|max:100',
            'estado_civil' => 'nullable|max:50',
            'conyugue' => 'nullable|max:100',
            'dni_conyugue' => 'nullable|max:50',
            'direccion_conyugue' => 'nullable|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'dni_pdf' => 'nullable|mimes:pdf|max:2048',
            'activo' => 'boolean',
            'actividad_economica' => 'nullable|max:255',
            'sexo' => 'nullable|max:255',
            'referencia' => 'nullable|max:255',
            'aval' => 'nullable|max:255',
            'numero_dni_aval' => 'nullable|max:50',
            'direccion_aval' => 'nullable|max:255',
            'dni_aval' => 'nullable|mimes:pdf|max:2048'
        ]);

        // Buscar al cliente por su ID
        $cliente = Cliente::find($id);

        // Actualizar los datos del cliente
        $cliente->nombre = $request->nombre;
        $cliente->documento_identidad = $request->documento_identidad;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;
        $cliente->direccion = $request->direccion;
        $cliente->distrito_id = $request->distrito; // Guardar distrito_id
        $cliente->direccion_laboral = $request->direccion_laboral;
        $cliente->lugar_nacimiento = $request->lugar_nacimiento;
        if (!empty($request->fecha_nacimiento)) {
            $cliente->fecha_nacimiento = $request->fecha_nacimiento;
        }
        $cliente->profesion = $request->profesion;
        $cliente->estado_civil = $request->estado_civil;
        $cliente->conyugue = $request->conyugue;
        $cliente->dni_conyugue = $request->dni_conyugue;
        $cliente->direccion_conyugue = $request->direccion_conyugue;
        $cliente->numero_dni_aval = $request->numero_dni_aval;
        $cliente->direccion_aval = $request->direccion_aval;
        $cliente->actividad_economica = $request->actividad_economica;
        $cliente->sexo = $request->sexo;
        $cliente->referencia = $request->referencia;
        $cliente->aval = $request->aval;

        // Manejar la carga de archivos (foto, dni_pdf y dni_aval)
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

        if ($request->hasFile('dni_pdf') && $request->file('dni_pdf')->isValid()) {
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

        if ($request->hasFile('dni_aval') && $request->file('dni_aval')->isValid()) {
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

        $cliente->activo = $request->activo ?? true; // Si no se proporciona el valor de activo, se establece en true
        $cliente->sucursal_id = 1; // Asignar el ID de la sucursal

        $cliente->save();

        // Redireccionar a la página de inicio
        return redirect()->route('clientes.index')
            ->with('mensaje', 'Se actualizó al cliente de manera correcta')
            ->with('icono', 'success');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
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
        $request->validate([
            'documento_identidad' => ['required','digits:8'],
        ]);

        $dni = $request->input('documento_identidad');

        // 1) BD primero (solo activos)
        $cliente = Cliente::where('documento_identidad', $dni)
            ->where('activo', 1)
            ->first(['nombre','telefono','email','direccion','direccion_laboral','profesion']);

        if ($cliente) {
            // Devuelve en el formato que tu vista ya consume
            return response()->json([
                'nombre'             => $cliente->nombre ?? '',
                'telefono'           => $cliente->telefono ?? '',
                'email'              => $cliente->email ?? '',
                'direccion'          => $cliente->direccion ?? '',
                'direccion_laboral'  => $cliente->direccion_laboral ?? '',
                'profesion'          => $cliente->profesion ?? '',
            ],200);
        }

        // 2) Si no está en BD, intente RENIEC (DeColecta)
       /*  $base   = 'https://api.decolecta.com';
        $token  = 'sk_9795.6RFNhNAXHqOWTaRyMsABG8iPxT1i9Fl3';
        $verify =  false;
        $timeout =  8;

        $client = new Client([
            'base_uri' => $base,
            'verify'   => $verify,
            'timeout'  => $timeout,
        ]);

        try {
            $res = $client->request('GET', '/v1/reniec/dni', [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    // Usa el referer que el proveedor indique; este mantiene compatibilidad
                    'Referer'    => 'https://apis.net.pe/consulta-dni-api',
                    'Accept'     => 'application/json',
                    'User-Agent' => 'laravel/guzzle',
                ],
                'query' => ['numero' => $dni],
            ]);

            $status = $res->getStatusCode();
            $json   = json_decode($res->getBody()->getContents(), true) ?: [];

            if ($status !== 200) {
                // No lo encontramos ni en BD ni en API
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            // Mapea la respuesta de la API a tu formato UI
            $nombres  = $json['nombres'] ?? '';
            $apep     = $json['apellidoPaterno'] ?? ($json['apellido_paterno'] ?? '');
            $apem     = $json['apellidoMaterno'] ?? ($json['apellido_materno'] ?? '');

            // Formato "APEP APEM, NOMBRES" como usabas
            $nombreUI = trim(
                trim(($apep ? $apep.' ' : '').($apem ?? ''))
                . ($nombres ? ', '.$nombres : '')
            );

            return response()->json([
                'nombre'             => $nombreUI,
                'telefono'           => '',   // la API DNI no trae teléfono
                'email'              => '',   // ni email
                'direccion'          => '',   // puedes enriquecer si tu plan trae dirección
                'direccion_laboral'  => '',
                'profesion'          => '',   // no disponible en consulta DNI estándar
            ]); */

       // } catch (GuzzleException $e) {
            // Falla de red o excepción: compórtate igual que "no encontrado"
            return response()->json(['error' => 'Cliente no encontrado'], 404);
      //  }
    }

    public function agregarpordni(Request $request)
    {
        $dni = $request->input('documento_identidad');
        $cliente = cliente::where('documento_identidad', $dni)->first(['nombre', 'documento_identidad', 'telefono', 'direccion', 'profesion']);

        if ($cliente) {
            return response()->json($cliente);
        } else {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
    }
}

//dsadasdas
