@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Nuevo usuario</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Llene los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/usuarios') }}" method="post">
                        @csrf
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nombre del usuario</label>
                                    <input type="text" value="{{ old('name') }}" name="name" class="form-control"
                                        required>
                                    @error('name')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="email" value="{{ old('email') }}" name="email" class="form-control"
                                    required>
                                @error('email')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" value="{{ old('telefono') }}" name="telefono" class="form-control" pattern="[0-9]+" title="Solo se permiten números">
                                @error('telefono')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Cargo</label>
                                <select name="role" class="form-control">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Repetir Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <hr>

                        <div class="col-md-12">
                            <a href="{{ url('admin/usuarios') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Guardar
                                registro</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
