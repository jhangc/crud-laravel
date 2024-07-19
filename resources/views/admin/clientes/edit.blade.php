@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Editar Cliente</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edite los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/clientes/' . $cliente->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre del cliente</label>
                                    <input type="text" value="{{ old('nombre', $cliente->nombre) }}" name="nombre"
                                        class="form-control" required>
                                    @error('nombre')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="documento_identidad">Documento de identidad</label>
                                    <input type="text"
                                        value="{{ old('documento_identidad', $cliente->documento_identidad) }}"
                                        name="documento_identidad" class="form-control" required maxlength="8">
                                    @error('documento_identidad')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" value="{{ old('telefono', $cliente->telefono) }}" name="telefono"
                                        class="form-control">
                                    @error('telefono')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo electrónico</label>
                                    <input type="email" value="{{ old('email', $cliente->email) }}" name="email"
                                        class="form-control">
                                    @error('email')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="actividad_economica">Actividad económica</label>
                                    <input type="text"
                                        value="{{ old('actividad_economica', $cliente->actividad_economica) }}"
                                        name="actividad_economica" id="actividad_economica" class="form-control">
                                    @error('actividad_economica')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departamento">Departamento</label>
                                    <select name="departamento" id="departamento" class="form-control" required>
                                        <option value="">Seleccione un departamento...</option>
                                        @foreach ($departamentos as $departamento)
                                            <option value="{{ $departamento->dep_id }}"
                                                {{ old('departamento', $cliente->distrito->provincia->departamento->dep_id ?? '') == $departamento->dep_id ? 'selected' : '' }}>
                                                {{ $departamento->dep_nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departamento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="provincia">Provincia</label>
                                    <select name="provincia" id="provincia" class="form-control" required>
                                        <option value="">Seleccione una provincia...</option>
                                        @if ($provincias->isNotEmpty())
                                            @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia->pro_id }}"
                                                    {{ old('provincia', $cliente->distrito->provincia->pro_id ?? '') == $provincia->pro_id ? 'selected' : '' }}>
                                                    {{ $provincia->pro_nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('provincia')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distrito">Distrito</label>
                                    <select name="distrito" id="distrito" class="form-control" required>
                                        <option value="">Seleccione un distrito...</option>
                                        @foreach ($distritos as $distrito)
                                            <option value="{{ $distrito->dis_id }}"
                                                {{ old('distrito', $cliente->distrito_id) == $distrito->dis_id ? 'selected' : '' }}>
                                                {{ $distrito->dis_nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('distrito')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" value="{{ old('direccion', $cliente->direccion) }}"
                                        name="direccion" class="form-control" required>
                                    @error('direccion')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion_laboral">Dirección laboral</label>
                                    <input type="text"
                                        value="{{ old('direccion_laboral', $cliente->direccion_laboral) }}"
                                        name="direccion_laboral" class="form-control">
                                    @error('direccion_laboral')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="referencia">Referencia</label>
                                    <input type="text" value="{{ old('referencia', $cliente->referencia) }}"
                                        name="referencia" class="form-control">
                                    @error('referencia')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lugar_nacimiento">Lugar de nacimiento</label>
                                    <input type="text"
                                        value="{{ old('lugar_nacimiento', $cliente->lugar_nacimiento) }}"
                                        name="lugar_nacimiento" class="form-control" required>
                                    @error('lugar_nacimiento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                    <input type="date"
                                        value="{{ old('fecha_nacimiento', $cliente->fecha_nacimiento) }}"
                                        name="fecha_nacimiento" class="form-control" required>
                                    @error('fecha_nacimiento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sexo">Sexo</label>
                                    <select name="sexo" id="sexo" class="form-control" required>
                                        <option value="">Seleccione una opción...</option>
                                        <option value="Masculino"
                                            {{ old('sexo', $cliente->sexo) == 'Masculino' ? 'selected' : '' }}>Masculino
                                        </option>
                                        <option value="Femenino"
                                            {{ old('sexo', $cliente->sexo) == 'Femenino' ? 'selected' : '' }}>Femenino
                                        </option>
                                    </select>
                                    @error('sexo')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profesion">Profesión</label>
                                    <input type="text" value="{{ old('profesion', $cliente->profesion) }}"
                                        name="profesion" class="form-control" required>
                                    @error('profesion')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado_civil">Estado Civil</label>
                                    <select name="estado_civil" id="estado_civil" class="form-control" required>
                                        <option value="">Seleccione una opción...</option>
                                        <option value="Soltero"
                                            {{ old('estado_civil', $cliente->estado_civil) == 'Soltero' ? 'selected' : '' }}>
                                            Soltero</option>
                                        <option value="Casado"
                                            {{ old('estado_civil', $cliente->estado_civil) == 'Casado' ? 'selected' : '' }}>
                                            Casado</option>
                                        <option value="Conviviente"
                                            {{ old('estado_civil', $cliente->estado_civil) == 'Conviviente' ? 'selected' : '' }}>
                                            Conviviente</option>
                                        <option value="Divorciado"
                                            {{ old('estado_civil', $cliente->estado_civil) == 'Divorciado' ? 'selected' : '' }}>
                                            Divorciado</option>
                                    </select>
                                    @error('estado_civil')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="conyugueField"
                            style="display: {{ $cliente->estado_civil == 'Casado' || $cliente->estado_civil == 'Conviviente' ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="conyugue">Cónyuge</label>
                                        <input type="text" value="{{ old('conyugue', $cliente->conyugue) }}"
                                            name="conyugue" id="conyugue" class="form-control">
                                        @error('conyugue')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dni_conyugue">DNI del Cónyuge</label>
                                        <input type="text" value="{{ old('dni_conyugue', $cliente->dni_conyugue) }}"
                                            name="dni_conyugue" id="dni_conyugue" class="form-control" maxlength="8">
                                        @error('dni_conyugue')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion_conyugue">Dirección del Cónyuge</label>
                                        <input type="text"
                                            value="{{ old('direccion_conyugue', $cliente->direccion_conyugue) }}"
                                            name="direccion_conyugue" id="direccion_conyugue" class="form-control">
                                        @error('direccion_conyugue')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="aval">Nombres del aval</label>
                                    <input type="text" value="{{ old('aval', $cliente->aval) }}" name="aval"
                                        id="aval" class="form-control">
                                    @error('aval')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_dni_aval">Número de DNI del Aval</label>
                                    <input type="text" value="{{ old('numero_dni_aval', $cliente->numero_dni_aval) }}"
                                        name="numero_dni_aval" id="numero_dni_aval" class="form-control" maxlength="8">
                                    @error('numero_dni_aval')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion_aval">Dirección del Aval</label>
                                    <input type="text" value="{{ old('direccion_aval', $cliente->direccion_aval) }}"
                                        name="direccion_aval" id="direccion_aval" class="form-control">
                                    @error('direccion_aval')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni_aval">DNI del Aval (PDF)</label>
                                    <input type="file" name="dni_aval" id="dni_aval" accept=".pdf"
                                        class="form-control-file">
                                    @if ($cliente->dni_aval)
                                        <a href="{{ url('storage/' . $cliente->dni_aval) }}" target="_blank">Ver PDF
                                            actual</a>
                                    @endif
                                    @error('dni_aval')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="foto">Foto</label>
                                    <input type="file" name="foto" accept="image/*" class="form-control-file">
                                    @if ($cliente->foto)
                                        <img src="{{ url('storage/' . $cliente->foto) }}" alt="Foto del cliente"
                                            width="100">
                                    @endif
                                    @error('foto')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni_pdf">DNI en PDF</label>
                                    <input type="file" name="dni_pdf" accept=".pdf" class="form-control-file">
                                    @if ($cliente->dni_pdf)
                                        <a href="{{ url('storage/' . $cliente->dni_pdf) }}" target="_blank">Ver PDF
                                            actual</a>
                                    @endif
                                    @error('dni_pdf')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('admin/clientes') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Guardar
                                    cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var estadoCivilSelect = document.getElementById('estado_civil');
            var conyugueField = document.getElementById('conyugueField');

            estadoCivilSelect.addEventListener('change', function() {
                // Muestra los campos si el estado civil es "Casado" o "Conviviente", de lo contrario, los oculta
                if (this.value === 'Casado' || this.value === 'Conviviente') {
                    conyugueField.style.display = 'block';
                } else {
                    conyugueField.style.display = 'none';
                }
            });
        });

        $(document).ready(function() {
            var clienteDepartamento = "{{ old('departamento', $cliente->departamento) }}";
            var clienteProvincia = "{{ old('provincia', $cliente->provincia) }}";
            var clienteDistrito = "{{ old('distrito', $cliente->distrito) }}";

            if (clienteDepartamento) {
                $('#departamento').val(clienteDepartamento).trigger('change');
            }

            $('#departamento').change(function() {
                var dep_id = $(this).val();
                if (dep_id) {
                    $.ajax({
                        url: '/getProvincias/' + dep_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#provincia').empty();
                            $('#provincia').append(
                                '<option value="">Seleccione una provincia...</option>');
                            $.each(data, function(key, value) {
                                $('#provincia').append('<option value="' + key + '">' +
                                    value + '</option>');
                            });
                            if (clienteProvincia) {
                                $('#provincia').val(clienteProvincia).trigger('change');
                            }
                        }
                    });
                } else {
                    $('#provincia').empty();
                    $('#distrito').empty();
                }
            });

            $('#provincia').change(function() {
                var prov_id = $(this).val();
                if (prov_id) {
                    $.ajax({
                        url: '/getDistritos/' + prov_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#distrito').empty();
                            $('#distrito').append(
                                '<option value="">Seleccione un distrito...</option>');
                            $.each(data, function(key, value) {
                                $('#distrito').append('<option value="' + key + '">' +
                                    value + '</option>');
                            });
                            if (clienteDistrito) {
                                $('#distrito').val(clienteDistrito);
                            }
                        }
                    });
                } else {
                    $('#distrito').empty();
                }
            });
        });
    </script>
@endsection
