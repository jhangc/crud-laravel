@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Modificación de datos del usuario</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Llene los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/usuarios', $usuario->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        <div class="row">



                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name">Nombre del usuario</label>
                                    <input type="text" value="{{ $usuario->name }}" name="name" class="form-control"
                                        required>
                                    @error('name')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" value="{{ $usuario->email }}" name="email" class="form-control"
                                        required>
                                    @error('email')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="dni">DNI</label>
                                    <input type="text" name="dni" id="dni" class="form-control"
                                        value="{{ $usuario->dni }}" pattern="[0-9]{8}" required>
                                    @error('dni')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" value="{{ $usuario->direccion }}" name="direccion"
                                        class="form-control">
                                    @error('direccion')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" value="{{ $usuario->telefono }}" name="telefono"
                                        class="form-control" pattern="[0-9]+" title="Solo se permiten números">
                                    @error('telefono')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control"
                                        value="{{ $usuario->fecha_nacimiento }}">
                                    @error('fecha_nacimiento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- Número de cuenta (solo lectura) --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="numero_cuenta">Número de cuenta</label>
                                    <input type="text" id="numero_cuenta" class="form-control"
                                        value="{{ $usuario->numero_cuenta }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="role">Cargo</label>
                                    <select name="role" class="form-control">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ $usuarioRole && $usuarioRole->id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text" onclick="togglePassword('password');">
                                                <i class="fas fa-eye" id="password-icon"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @error('password')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="password_confirmation">Repetir Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text"
                                                onclick="togglePassword('password_confirmation');">
                                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr>
                        <div class="col-md-6 mb-3">
                            <a href="{{ url('admin/usuarios') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success"><i class="bi bi-pencil-square"></i>
                                Actualizar registro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '-icon');
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        </script>
    </div>
@endsection
