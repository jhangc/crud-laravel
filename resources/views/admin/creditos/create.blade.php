@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Nuevo Prestamo</h1>
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
                                    <label for="tipo_credito">Tipo de préstamo</label>
                                    <select name="tipo_credito" id="tipo_credito" class="form-control" required
                                        onchange="toggleFields()">
                                        <option value="">Seleccione una opción...</option>
                                        <option value="individual"
                                            {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>Individual</option>
                                        <option value="grupal" {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                            Grupal</option>
                                    </select>
                                    @error('tipo_credito')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Campos para crédito individual -->
                        <div id="individualFields" style="display:none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="documento_identidad">Documento de identidad</label>
                                        <div class="input-group">
                                            <input type="text" id="documento_identidad" value="{{ old('documento_identidad') }}"
                                                   name="documento_identidad" class="form-control" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="buscarCliente">
                                                    <i class="fas fa-search"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                        @error('documento_identidad')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del cliente</label>
                                        <input type="text" value="{{ old('nombre') }}" name="nombre" id="nombre"
                                            class="form-control" required>
                                        @error('nombre')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Campos para crédito grupal -->
                        <div id="grupalFields" style="display:none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nombre_grupo">Nombre del grupo</label>
                                        <input type="text" value="{{ old('nombre_grupo') }}" name="nombre_grupo"
                                            class="form-control" required>
                                        @error('nombre_grupo')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Agrega más campos específicos para créditos grupales si es necesario -->
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
        }

        // Call toggleFields on document ready to handle form repopulation on page reload (with old inputs)
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>

<script>
    document.getElementById('buscarCliente').addEventListener('click', function() {
        var documento = document.getElementById('documento_identidad').value;
        fetch('/buscar-cliente', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ documento_identidad: documento })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('nombre').value = data.nombre;
            } else {
                alert('Cliente no encontrado');
                document.getElementById('nombre').value = '';
            }
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
    
@endsection
