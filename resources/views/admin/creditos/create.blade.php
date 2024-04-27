@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Nuevo Prestamo</h1>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos Generales</h3>

                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/creditos') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tipo_credito">Tipo de crédito</label>
                                    <select name="tipo_credito" id="tipo_credito" class="form-control" required
                                        onchange="toggleFields()">
                                        <option value="">Seleccione una opción...</option>
                                        <option value="individual"
                                            {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                            Individual</option>
                                        <option value="grupal" {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                            Grupal</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="individualFields" style="display:none;">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del Credito</h3>
                                </div>
                                <div class="card-body">

                                    <!-- Campos para crédito individual -->

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="tipo_credito">Tipo de credito individual</label>
                                                <select name="tipo_credito" id="tipo_credito" class="form-control" required
                                                    onchange="toggleFields()">
                                                    <option value="">Seleccione una opción...</option>
                                                    <option value="individual"
                                                        {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                                        Crédito Agricola</option>
                                                    <option value="grupal"
                                                        {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                                        Crédito Vehicular</option>
                                                    <option value="grupal"
                                                        {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                                        Crédito Hipotecario</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Recurrencia</label>
                                                <select name="tipo_credito" id="tipo_credito" class="form-control" required
                                                    onchange="toggleFields()">
                                                    <option value="">Seleccione una opción...</option>
                                                    <option value="individual"
                                                        {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                                        Mensual</option>
                                                        <option value="grupal"
                                                        {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                                        Quincenal</option>
                                                    <option value="grupal"
                                                        {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                                        Anual</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tasa de interes (%)</label>
                                                <input type="text" value="{{ old('tasa_interes') }}" name="tasa_interes"
                                                    id="tasa_interes" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tiempo del credito</label>
                                                <input type="text" value="{{ old('tiempo_credito') }}" name="nombre"
                                                    id="tiempo_credito'" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Monto total (S/.)</label>
                                                <input type="text" value="{{ old('monto') }}" name="nombre"
                                                    id="monto" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre">Descripcion de la Garantia</label>
                                                <input type="text" value="{{ old('tasa_interes') }}" name="tasa_interes"
                                                    id="" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Valorización (S/.)</label>
                                                <input type="text" value="{{ old('tasa_interes') }}" name="tasa_interes"
                                                    id="" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="dni_pdf">Archivo en pdf</label>
                                                <input type="file" name="dni_pdf" accept=".pdf"
                                                    class="form-control-file">
                                                @error('dni_pdf')
                                                    <small style="color: red">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>


                                    </div>


                                </div>
                            </div>


                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del cliente</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="documento_identidad">Documento de identidad</label>
                                                <div class="input-group">
                                                    <input type="text" id="documento_identidad"
                                                        value="{{ old('documento_identidad') }}"
                                                        name="documento_identidad" class="form-control" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="buscarCliente"><i class="fas fa-search"></i> Buscar /button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre">Nombre del cliente</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="profesion">Profesión</label>
                                                <input type="text" value="{{ old('profesion') }}" name="profesion"
                                                    id="profesion" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="text" value="{{ old('telefono') }}" name="telefono"
                                                    id="telefono" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Correo electrónico</label>
                                                <input type="email" value="{{ old('email') }}" name="email"
                                                    id="email" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <input type="text" value="{{ old('direccion') }}" name="direccion"
                                                    id="direccion" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion_laboral">Dirección laboral</label>
                                                <input type="text" value="{{ old('direccion_laboral') }}"
                                                    name="direccion_laboral" id="direccion_laboral" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Ingresos</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select name="tipo_ingreso" id="tipo_ingreso"
                                                            class="form-control" required onchange="toggleFields()">
                                                            <option value="">Seleccione una opción...</option>
                                                            <option value="diario"
                                                                {{ old('tipo_credito') == 'diario' ? 'selected' : '' }}>
                                                                Diario</option>
                                                            <option value="mensual"
                                                                {{ old('tipo_credito') == 'mensual' ? 'selected' : '' }}>
                                                                Mensual</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <input type="text" value="{{ old('direccion') }}"
                                                            name="direccion" id="inputMensual" class="form-control"
                                                            required placeholder="monto en S/.">
                                                        <button type="button" class="btn btn-primary" id="botonDiario"
                                                            data-toggle="modal" data-target="#miModal"><i
                                                                class="bi bi-floppy2"></i> Registrar Diario</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion_laboral">Otros créditos</label>
                                                <input type="number" value="{{ old('otros_credito') }}" name=""
                                                    id="otroprestamos" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div class="card card-outline card-warning">
                                <div class="card-header" style="display:flex;">
                                    <h3 class="card-title">Deudas Financieras</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="documento_identidad">Documento de identidad</label>
                                                <input type="text" id="documento_identidad"
                                                    value="{{ old('documento_identidad') }}" name="documento_identidad"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre">Nombre del cliente</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" required>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="text" value="{{ old('telefono') }}" name="telefono"
                                                    id="telefono" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <input type="text" value="{{ old('direccion') }}" name="direccion"
                                                    id="direccion" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <div id="grupalFields" style="display:none;">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del Credito</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="nombre">Nombre del Grupo</label>
                                                <input type="text" value="{{ old('nombre_grupo') }}"
                                                    name="nombre_grupo" id="nombre_grupo" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="nombre">Cantidad de integrantes</label>
                                                <input type="number" value="{{ old('cantidad_grupo') }}"
                                                    name="cantidad_grupo" id="cantidad_grupo" class="form-control"
                                                    required>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Recurrencia</label>
                                                <select name="tipo_credito" id="tipo_credito" class="form-control"
                                                    required onchange="toggleFields()">
                                                    <option value="">Seleccione una opción...</option>
                                                    <option value="individual"
                                                        {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                                        Catorcenal</option>
                                                    <option value="individual"
                                                        {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                                        Mensual</option>
                                                    <option value="grupal"
                                                        {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                                        Anual</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tasa de interes (%)</label>
                                                <input type="text" value="{{ old('tasa_interes') }}"
                                                    name="tasa_interes" id="tasa_interes" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tiempo del credito</label>
                                                <input type="text" value="{{ old('tiempo_credito') }}" name="nombre"
                                                    id="tiempo_credito'" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Monto total (S/.)</label>
                                                <input type="text" value="{{ old('monto') }}" name="nombre"
                                                    id="monto" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>


                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos de los clientes</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" id="dni" name="dni"
                                                    placeholder="Agregar por Dni" class="form-control" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button"
                                                        id="agregarButton">Agregar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Nombre del cliente</th>
                                                <th>Profesión</th>
                                                <th>Teléfono</th>
                                                <th>Dirección</th>
                                                <th>monto</th>
                                                <th>Acciones</th>

                                            </tr>
                                        </thead>
                                        <tbody id="tablaCuerpo">
                                            <!-- Las filas se agregarán aquí dinámicamente -->
                                        </tbody>
                                    </table>




                                </div>
                            </div>



                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('admin/creditos') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i>
                                    Guardar registro</button>
                            </div>
                        </div>
                        <hr>

                    </form>
                </div>

            </div>

        </div>
    </div>

    {{-- MENU FLOTANTE --}}
    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registro de Ingresos Semanal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="weeklyIncomeForm">
                        <div class="form-group">
                            <label for="monday">Lunes:</label>
                            <input type="number" class="form-control" id="monday" name="monday"
                                placeholder="Ingresos del lunes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="tuesday">Martes:</label>
                            <input type="number" class="form-control" id="tuesday" name="tuesday"
                                placeholder="Ingresos del martes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="wednesday">Miércoles:</label>
                            <input type="number" class="form-control" id="wednesday" name="wednesday"
                                placeholder="Ingresos del miércoles (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="thursday">Jueves:</label>
                            <input type="number" class="form-control" id="thursday" name="thursday"
                                placeholder="Ingresos del jueves (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="friday">Viernes:</label>
                            <input type="number" class="form-control" id="friday" name="friday"
                                placeholder="Ingresos del viernes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="saturday">Sábado:</label>
                            <input type="number" class="form-control" id="saturday" name="saturday"
                                placeholder="Ingresos del sábado (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="sunday">Domingo:</label>
                            <input type="number" class="form-control" id="sunday" name="sunday"
                                placeholder="Ingresos del domingo (S/.)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="submitWeeklyIncome()">Guardar
                        Cambios</button>
                </div>
            </div>
        </div>
    </div>




    <script>
        function toggleFields() {
            var selection = document.getElementById('tipo_credito').value;
            var individualFields = document.getElementById('individualFields');
            var grupalFields = document.getElementById('grupalFields');
            if (selection === 'individual') {
                individualFields.style.display = 'block';
                grupalFields.style.display = 'none';
            } else if (selection === 'grupal') {
                individualFields.style.display = 'none';
                grupalFields.style.display = 'block';
            } else {
                individualFields.style.display = 'none';
                grupalFields.style.display = 'none';
            }


            var selection1 = document.getElementById('tipo_ingreso').value;
            var botonDiario = document.getElementById('botonDiario');
            var inputMensual = document.getElementById('inputMensual');

            if (selection1 === 'diario') {
                botonDiario.style.display = 'block'; // Muestra el botón para 'Diario'
                inputMensual.style.display = 'none'; // Oculta el input para 'Mensual'
            } else if (selection1 === 'mensual') {
                botonDiario.style.display = 'none'; // Oculta el botón para 'Diario'
                inputMensual.style.display = 'block'; // Muestra el input para 'Mensual'
            } else {
                // Esconde ambos si se selecciona la opción por defecto o ninguna
                botonDiario.style.display = 'none';
                inputMensual.style.display = 'none';
            }
        }

        // Call toggleFields on document ready to handle form repopulation on page reload (with old inputs)
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>

    <script>
        $(document).ready(function() {
            console.log("presionar", )
            $('#buscarCliente').click(function() {
                var documentoIdentidad = $('#documento_identidad').val(); // Captura el valor del DNI
                console.log("Intentando buscar cliente con DNI:",
                    documentoIdentidad); // Muestra el valor enviado en consola

                $.ajax({
                    url: '{{ route('creditos.buscardni') }}',
                    type: 'GET',
                    data: {
                        documento_identidad: documentoIdentidad
                    },
                    success: function(response) {
                        $('#nombre').val(response.nombre || ''); // Ya existente
                        $('#telefono').val(response.telefono || ''); // Nuevo campo
                        $('#email').val(response.email || ''); // Nuevo campo
                        $('#direccion').val(response.direccion || ''); // Nuevo campo
                        $('#direccion_laboral').val(response.direccion_laboral ||
                            ''); // Nuevo campo
                        $('#profesion').val(response.profesion || ''); // Nuevo campo
                    },
                    error: function(xhr) {
                        console.error("Error al recuperar información: " + xhr.statusText);
                        // Limpiar todos los campos si hay un error
                        $('#nombre, #telefono, #email, #direccion, #direccion_laboral').val('');
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            let cantidadMaxima = 0; // Variable para almacenar el máximo de personas permitidas
            let contadorPersonas = 0; // Contador de personas agregadas

            // Actualizar la cantidad máxima cada vez que cambie el input
            $('#cantidad_grupo').change(function() {
                cantidadMaxima = parseInt($(this).val(), 10); // Actualiza la cantidad máxima permitida
                validarBotonAgregar(); // Validar si el botón debe estar habilitado o deshabilitado
            });

            $('#agregarButton').click(function() {
                if (contadorPersonas < cantidadMaxima) {
                    var documentoIdentidad = $('#dni').val();
                    $.ajax({
                        url: '{{ route('creditos.buscardni') }}',
                        type: 'GET',
                        data: {
                            documento_identidad: documentoIdentidad
                        },
                        success: function(response) {
                            $('#datosTabla tbody').append(
                                '<tr>' +
                                '<td>' + (response.nombre || '') + '</td>' +
                                '<td>' + (response.profesion || '') + '</td>' +
                                '<td>' + (response.telefono || '') + '</td>' +
                                '<td>' + (response.direccion || '') + '</td>' +
                                '<td>' + ('200') + '</td>' +
                                '<td><button class="btn btn-danger btn-sm removeRow"><i class="fa fa-trash"></i></button></td>' +
                                '</tr>'
                            ); // Añade la fila al final del cuerpo de la tabla
                            $('#dni').val(''); // Limpiar campo DNI
                            contadorPersonas++; // Incrementar el contador de personas
                            validarBotonAgregar(); // Revalidar el estado del botón
                        },
                        error: function(xhr) {
                            console.error("Error: " + xhr.statusText);
                        }
                    });
                }
            });

            $('#datosTabla').on('click', '.removeRow', function() {
                $(this).closest('tr').remove(); // Elimina la fila más cercana en el DOM a este botón
                contadorPersonas--; // Decrementa el contador al quitar una persona
                validarBotonAgregar(); // Revisa nuevamente el estado del botón Agregar
            });

            function validarBotonAgregar() {
                if (contadorPersonas >= cantidadMaxima) {
                    $('#agregarButton').prop('disabled', true); // Deshabilitar el botón si se alcanza el máximo
                } else {
                    $('#agregarButton').prop('disabled',
                        false); // Habilitar el botón si aún no se alcanza el máximo
                }
            }
        });
    </script>
@endsection
