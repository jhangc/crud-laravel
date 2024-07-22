@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Transacciones de Caja</h1>
        <div id="seleccionarCajaDiv">
            <form id="seleccionarCajaForm" class="form-inline">
                <div class="form-group mr-2">
                    <label for="caja_id" class="mr-2">Seleccionar Caja</label>
                    <select id="caja_id" name="caja_id" class="form-control">
                        @foreach($cajas as $caja)
                        <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="mostrarTransacciones()">Continuar</button>
            </form>
        </div>

        <div id="transacciones" style="display:none;">
            <h2>Ingresos</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hora de Pago</th>
                        <th>Monto</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Cuota</th>
                    </tr>
                </thead>
                <tbody id="ingresosBody">
                    <!-- Aquí se llenarán los ingresos -->
                </tbody>
            </table>
            <h3>Total de Ingresos: <span id="totalIngresos"></span></h3>

            <h2>Egresos</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hora de Egreso</th>
                        <th>Monto</th>
                        <th>Clientes</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody id="egresosBody">
                    <!-- Aquí se llenarán los egresos -->
                </tbody>
            </table>
            <h3>Total de Egresos: <span id="totalEgresos"></span></h3>
        </div>
    </div>
</div>

<script>
    function mostrarTransacciones() {
        var cajaId = document.getElementById('caja_id').value;

        // Realizar la petición AJAX para obtener las transacciones de la caja seleccionada
        fetch('/admin/caja/obtener-transacciones/' + cajaId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Limpiar las tablas
                    document.getElementById('ingresosBody').innerHTML = '';
                    document.getElementById('egresosBody').innerHTML = '';

                    let totalIngresos = 0;
                    let totalEgresos = 0;

                    // Llenar los ingresos
                    data.ingresos.forEach(ingreso => {
                        document.getElementById('ingresosBody').innerHTML += `
                            <tr>
                                <td>${ingreso.hora_pago}</td>
                                <td>${ingreso.monto}</td>
                                <td>${ingreso.cliente.nombre}</td>
                                <td>${ingreso.transaccion.user.name}</td>
                                <td>${ingreso.numero_cuota}</td>
                            </tr>
                        `;
                        totalIngresos += parseFloat(ingreso.monto);
                    });

                    // Mostrar el total de ingresos
                    document.getElementById('totalIngresos').innerText = totalIngresos.toFixed(2);

                    // Llenar los egresos
                    data.egresos.forEach(egreso => {
                        document.getElementById('egresosBody').innerHTML += `
                            <tr>
                                <td>${egreso.hora_egreso}</td>
                                <td>${egreso.monto}</td>
                                <td>${egreso.clientes.join(', ')}</td>
                                <td>${egreso.usuario}</td>
                            </tr>
                        `;
                        totalEgresos += parseFloat(egreso.monto);
                    });

                    // Mostrar el total de egresos
                    document.getElementById('totalEgresos').innerText = totalEgresos.toFixed(2);

                    document.getElementById('seleccionarCajaDiv').style.display = 'none';
                    document.getElementById('transacciones').style.display = 'block';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error'
                    });
                }
            });
    }
</script>

@endsection
