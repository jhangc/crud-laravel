@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h3>Transacciones de Caja Diario</h3>
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
            <table id="ingresosTable" class="table table-striped">
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
            <table id="egresosTable" class="table table-striped">
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

            <h2>Gastos</h2>
            <table id="gastosTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Hora de Gasto</th>
                        <th>Monto</th>
                        <th>Número de Documento</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody id="gastosBody">
                    <!-- Aquí se llenarán los gastos -->
                </tbody>
            </table>
            <h3>Total de Gastos: <span id="totalGastos"></span></h3>

            <div id="datosCierre" style="display:none;">
                <h2>Datos de Cierre</h2>
                <p>Saldo Final Esperado: <span id="saldoFinalEsperado"></span></p>
                <p>Saldo Final Real: <span id="saldoFinalReal"></span></p>
                <p>Desajuste: <span id="desajuste"></span></p>
            </div>
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
                    document.getElementById('gastosBody').innerHTML = '';

                    let totalIngresos = 0;
                    let totalEgresos = 0;
                    let totalGastos = 0;

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
                    document.getElementById('totalEgresos').innerText = (totalEgresos).toFixed(2);

                    // Llenar los gastos
                    data.gastos.forEach(gasto => {
                        document.getElementById('gastosBody').innerHTML += `
                            <tr>
                                <td>${gasto.hora_gasto}</td>
                                <td>${gasto.monto}</td>
                                <td>${gasto.numero_documento}</td>
                                <td>${gasto.usuario}</td>
                            </tr>
                        `;
                        totalGastos += parseFloat(gasto.monto);
                    });

                    // Mostrar el total de gastos
                    document.getElementById('totalGastos').innerText = totalGastos.toFixed(2);

                    if (data.cajaCerrada) {
                        document.getElementById('saldoFinalEsperado').innerText = data.saldoFinalEsperado;
                        document.getElementById('saldoFinalReal').innerText = data.saldoFinalReal;
                        document.getElementById('desajuste').innerText = data.desajuste;
                        document.getElementById('datosCierre').style.display = 'block';
                    } else {
                        document.getElementById('datosCierre').style.display = 'none';
                    }

                    document.getElementById('seleccionarCajaDiv').style.display = 'none';
                    document.getElementById('transacciones').style.display = 'block';

                    // Inicializar DataTables
                    $('#ingresosTable').DataTable();
                    $('#egresosTable').DataTable();
                    $('#gastosTable').DataTable();
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
