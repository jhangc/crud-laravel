@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Nuevo Cliente</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Llene los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/clientes') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre del cliente</label>
                                    <input type="text" value="{{ old('nombre') }}" name="nombre" class="form-control"
                                        required>
                                    @error('nombre')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="documento_identidad">Documento de identidad</label>
                                    <input type="text" value="{{ old('documento_identidad') }}"
                                        name="documento_identidad" class="form-control" required>
                                    @error('documento_identidad')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" value="{{ old('telefono') }}" name="telefono"
                                        class="form-control">
                                    @error('telefono')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo electrónico</label>
                                    <input type="email" value="{{ old('email') }}" name="email" class="form-control">
                                    @error('email')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" value="{{ old('direccion') }}" name="direccion"
                                        class="form-control" required>
                                    @error('direccion')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="direccion_laboral">Dirección laboral</label>
                                    <input type="text" value="{{ old('direccion_laboral') }}" name="direccion_laboral"
                                        class="form-control">
                                    @error('direccion_laboral')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="lugar_nacimiento">Lugar de nacimiento</label>
                                    <input type="text" value="{{ old('lugar_nacimiento') }}" name="lugar_nacimiento"
                                        class="form-control" required>
                                    @error('lugar_nacimiento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                    <input type="date" value="{{ old('fecha_nacimiento') }}" name="fecha_nacimiento"
                                        class="form-control" required>
                                    @error('fecha_nacimiento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="profesion">Profesión</label>
                                    <input type="text" value="{{ old('profesion') }}" name="profesion"
                                        class="form-control" required>
                                    @error('profesion')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="estado_civil">Estado Civil</label>
                                    <select name="estado_civil" class="form-control" required>
                                        <option value="">Seleccione una opción...</option>
                                        <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>
                                            Soltero</option>
                                        <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>
                                            Casado</option>
                                        <option value="Divorciado"
                                            {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                                    </select>
                                    @error('estado_civil')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="conyugue">Cónyuge</label>
                                    <input type="text" value="{{ old('conyugue') }}" name="conyugue"
                                        class="form-control">
                                    @error('conyugue')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="conyugue">DNI del Cónyuge</label>
                                    <input type="text" value="{{ old('conyugue') }}" name="conyugue"
                                        class="form-control">
                                    @error('conyugue')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="foto">Foto</label>
                                    <input type="file" name="foto" accept="image/*" class="form-control-file">
                                    @error('foto')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dni_pdf">DNI en PDF</label>
                                    <input type="file" name="dni_pdf" accept=".pdf" class="form-control-file">
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
                                    registro</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
