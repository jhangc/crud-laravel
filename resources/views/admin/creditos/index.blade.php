@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Listado de Creditos</h1>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3 ">
                            <div class="card-tools ">
                                <a href="{{ url('/admin/creditos/create') }}" class="btn btn-primary"><i
                                        class="bi bi-person-fill-add"></i> Crear Prestamo</a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select name="tipo_ingreso" id="tipo_ingreso" class="form-control" required
                                    onchange="filtrarPorTipo()">
                                    <option value="">Tipo de crédito...</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Grupal">Grupal</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th><center>Nro</center></th>
                            <th><center>Tipo de credito</center></th>
                            <th><center>Solicitante</center></th>
                            <th><center>Monto</center></th>
                            <th><center>Intervalo</center></th>
                            <th><center>Tasa (S/.)</center></th>
                            <th><center>Estado</center></th>
                            <th><center>Acciones</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplo de fila 1 -->
                        <tr>
                            <td style="text-align: center">1</td>
                            <td>Individual</td>
                            <td>Jhan Garcia</td>
                            <td>10000</td>
                            <td>mensual</td>
                            <td>15%</td>
                            <td style="color: green">Pendiente</td>
                            <td style="text-align:center">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-success"><i class="bi bi-pencil"></i>
                                        Editar</button>
                                    <button type="button" class="btn btn-danger" onclick="preguntar(1)"><i
                                            class="bi bi-trash"></i> Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        <!-- Ejemplo de fila 2 -->
                        <tr>
                            <td style="text-align: center">2</td>
                            <td>Grupal</td>
                            <td>Los emprendedores</td>
                            <td>50000</td>
                            <td>mensual</td>
                            <td>13%</td>
                            <td style="color:dodgerblue">Aprobado</td>
                            <td style="text-align:center">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-success"><i class="bi bi-pencil"></i>
                                        Editar</button>
                                    <button type="button" class="btn btn-danger" onclick="preguntar(2)"><i
                                            class="bi bi-trash"></i> Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center">2</td>
                            <td>Individual</td>
                            <td>Benito laporta</td>
                            <td>3000</td>
                            <td>mensual</td>
                            <td>20%</td>
                            <td style="color:red">Rechazado</td>
                            <td style="text-align:center">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-success"><i class="bi bi-pencil"></i>
                                        Editar</button>
                                    <button type="button" class="btn btn-danger" onclick="preguntar(2)"><i
                                            class="bi bi-trash"></i> Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
        function preguntar(id) {
            Swal.fire({
                title: 'Eliminar registro',
                text: '¿Desea eliminar este registro?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: 'Eliminar',
                confirmButtonColor: '#a5161d',
                denyButtonColor: '#270a0a',
                denyButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Agrega la lógica para eliminar el registro, por ejemplo, mediante una petición POST
                    console.log("Eliminar registro con ID:", id);
                    // Aquí podrías hacer una petición AJAX para eliminar el registro
                }
            });
        }
    </script>

    <script>
        function filtrarPorTipo() {
            var seleccion = document.getElementById('tipo_ingreso').value;
            var filas = document.querySelectorAll("table tbody tr"); // Selecciona todas las filas de la tabla

            filas.forEach(fila => {
                var tipoCredito = fila.cells[1]
                .textContent; // Asume que el tipo de crédito está en la segunda columna
                if (seleccion === "" || tipoCredito.includes(seleccion)) {
                    fila.style.display =
                    ""; // Muestra la fila si coincide con el filtro o si el filtro está en blanco
                } else {
                    fila.style.display = "none"; // Oculta la fila si no coincide con el filtro
                }
            });
        }
    </script>
@endsection
