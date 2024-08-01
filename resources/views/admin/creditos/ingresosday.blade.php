@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Transacciones de Caja Diario</h3>
    <div id="seleccionarCajaDiv" class="mb-4">
        <form id="seleccionarCajaForm" class="form-inline">
            <div class="form-group mr-3">
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
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Ingresos</h4>
            </div>
            <div class="card-body">
                <table id="ingresosTable" class="table table-striped table-bordered">
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
                <h5 class="mt-3">Total de Ingresos: <span id="totalIngresos"></span></h5>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4>Ingresos Extras</h4>
            </div>
            <div class="card-body">
                <table id="ingresosExtrasTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Hora de Ingreso</th>
                            <th>Monto</th>
                            <th>Motivo</th>
                            <th>Número de Documento</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody id="ingresosExtrasBody">
                        <!-- Aquí se llenarán los ingresos extras -->
                    </tbody>
                </table>
                <h5 class="mt-3">Total de Ingresos Extras: <span id="totalIngresosExtras"></span></h5>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h4>Egresos</h4>
            </div>
            <div class="card-body">
                <table id="egresosTable" class="table table-striped table-bordered">
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
                <h5 class="mt-3">Total de Egresos: <span id="totalEgresos"></span></h5>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h4>Gastos</h4>
            </div>
            <div class="card-body">
                <table id="gastosTable" class="table table-striped table-bordered">
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
                <h5 class="mt-3">Total de Gastos: <span id="totalGastos"></span></h5>
            </div>
        </div>

        <div id="datosCierre" style="display:none;">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h4>Datos de Cierre</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Saldo Final Esperado</th>
                            <td><span id="saldoFinalEsperado"></span></td>
                        </tr>
                        <tr>
                            <th>Saldo Final Real-Caja</th>
                            <td><span id="saldoFinalReal"></span></td>
                        </tr>
                        <tr>
                            <th>Desajuste</th>
                            <td><span id="desajuste"></span></td>
                        </tr>
                        <tr>
                            <th>Mensaje de Desajuste</th>
                            <td><span id="mensajeDesajuste" class="text-danger"></span></td>
                        </tr>
                    </table>
                    <button type="button" class="btn btn-secondary mt-3" onclick="generarPDF()">Generar PDF</button>
                    <button type="button" class="btn btn-info mt-3" onclick="resetCaja()">Resetear Caja</button>
                </div>
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
                    document.getElementById('ingresosExtrasBody').innerHTML = '';
                    document.getElementById('egresosBody').innerHTML = '';
                    document.getElementById('gastosBody').innerHTML = '';

                    let totalIngresos = 0;
                    let totalIngresosExtras = 0;
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

                    // Llenar los ingresos extras
                    data.ingresosExtras.forEach(ingresoExtra => {
                        document.getElementById('ingresosExtrasBody').innerHTML += `
                            <tr>
                                <td>${ingresoExtra.hora_ingreso}</td>
                                <td>${ingresoExtra.monto}</td>
                                <td>${ingresoExtra.motivo}</td>
                                <td>${ingresoExtra.numero_documento}</td>
                                <td>${ingresoExtra.usuario}</td>
                            </tr>
                        `;
                        totalIngresosExtras += parseFloat(ingresoExtra.monto);
                    });

                    // Mostrar el total de ingresos extras
                    document.getElementById('totalIngresosExtras').innerText = totalIngresosExtras.toFixed(2);

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

                        let mensajeDesajuste = '';
                        if (parseFloat(data.desajuste) === 0) {
                            mensajeDesajuste = 'No hay desajuste.';
                        } else if (parseFloat(data.desajuste) > 0) {
                            mensajeDesajuste = 'Sobró dinero en la caja.';
                        } else {
                            mensajeDesajuste = 'Faltó dinero en la caja.';
                        }
                        document.getElementById('mensajeDesajuste').innerText = mensajeDesajuste;
                        document.getElementById('datosCierre').style.display = 'block';
                    } else {
                        document.getElementById('datosCierre').style.display = 'none';
                    }

                    document.getElementById('seleccionarCajaDiv').style.display = 'none';
                    document.getElementById('transacciones').style.display = 'block';

                    // Inicializar DataTables
                    $('#ingresosTable').DataTable();
                    $('#ingresosExtrasTable').DataTable();
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

    function generarPDF() {
        var cajaId = document.getElementById('caja_id').value;
        window.open('/admin/generar-transacciones-pdf/' + cajaId, '_blank');
    }
    function resetCaja(){
        var cajaId = document.getElementById('caja_id').value;
        fetch('/admin/caja/resetcaja/' + cajaId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Correcto',
                        text: data.message,
                        icon: 'success'
                    });
                    window.location.reload();
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
