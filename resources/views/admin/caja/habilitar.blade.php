@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Apertura de Caja</h1>
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
                <button type="button" class="btn btn-primary" onclick="mostrarArqueo()">Continuar</button>
            </form>
        </div>

        <div id="arqueoCajaDiv" style="display:none;">
            <form id="abrirCajaForm">
                @csrf
                <input type="hidden" id="caja_id_hidden" name="caja_id">

                <div id="arqueoDetalles" class="form-group col-md-6">
                    <label for="monto_apertura">Monto de Apertura</label>
                    <input type="number" id="monto_apertura" name="monto_apertura" class="form-control" required readonly>
                </div>

                <!-- Diseño de Arqueo de Caja -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">ARQUEO DE CAJA</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Billetes -->
                            <div class="col-md-6">
                                <h5>Billetes</h5>
                                @php
                                $billetes = [200, 100, 50, 20, 10];
                                @endphp
                                @foreach ($billetes as $billete)
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ $billete }}.00</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="billete_{{ $billete }}" name="billete_{{ $billete }}" value="0" readonly>
                                    </div>
                                    <label class="col-sm-2 col-form-label">= S/. </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="total_billete_{{ $billete }}" readonly>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <!-- Monedas -->
                            <div class="col-md-6">
                                <h5>Monedas</h5>
                                @php
                                $monedas = [5, 2, 1, 0.5, 0.2, 0.1];
                                @endphp
                                @foreach ($monedas as $moneda)
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ number_format($moneda, 2) }}</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="moneda_{{ str_replace('.', '_', $moneda) }}" name="moneda_{{ str_replace('.', '_', $moneda) }}" value="0" readonly>
                                    </div>
                                    <label class="col-sm-2 col-form-label">= S/. </label>
                                    <div class="col-sm4">
                                        <input type="text" class="form-control" id="total_moneda_{{ str_replace('.', '_', $moneda) }}" readonly>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Totales -->
                        <br>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group row ">
                                    <label for="total_apertura" class="col-sm-2 col-form-label">Apertura: S/.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="total_apertura" readonly>
                                    </div>
                                    <label for="total_ingresos" class="col-sm-2 col-form-label">Ingresos: S/.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="total_ingresos" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="total_egresos" class="col-sm-2 col-form-label">Egresos: S/.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="total_egresos" readonly>
                                    </div>
                                    <label for="total_efectivo1" class="col-sm-2 col-form-label">Total Efectivo: S/.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="total_efectivo" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary btn-block" onclick="abrirCaja()">Abrir Caja</button>
                    </div>
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-secondary btn-block" onclick="cancelar()">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function mostrarArqueo() {
        var cajaId = document.getElementById('caja_id').value;

        // Realizar la petición AJAX para obtener los detalles de la última transacción
        fetch('/admin/caja/ultima-transaccion/' + cajaId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('caja_id_hidden').value = cajaId;
                    document.getElementById('monto_apertura').value = data.transaccion.monto_apertura;
                    document.getElementById('total_apertura').value = data.transaccion.monto_apertura;
                    document.getElementById('total_ingresos').value = data.transaccion.cantidad_ingresos;
                    document.getElementById('total_egresos').value = data.transaccion.cantidad_egresos;
                    document.getElementById('total_efectivo').value = data.transaccion.monto_cierre;
                    // Llenar los detalles de la transacción en el div de arqueo
                    document.getElementById('arqueoDetalles').innerHTML = `
                      <div class="row">
                        <div class="form-group col-md-6">
                            <label for="monto_cierre">Monto de Cierre Anterior</label>
                            <input type="number" id="monto_cierre" name="monto_cierre" class="form-control" value="${data.transaccion.monto_cierre}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="usuario_cierre">Usuario que Cerró</label>
                            <input type="text" id="usuario_cierre" name="usuario_cierre" class="form-control" value="${data.transaccion.user.name}" readonly>
                        </div>
                      </div>
                    `;

                    // Llenar los valores de billetes y monedas
                    var billetes = data.transaccion.json_cierre && data.transaccion.json_cierre.billetes ? data.transaccion.json_cierre.billetes : {200: 0, 100: 0, 50: 0, 20: 0, 10: 0};
                    var monedas = data.transaccion.json_cierre && data.transaccion.json_cierre.monedas ? data.transaccion.json_cierre.monedas : {5: 0, 2: 0, 1: 0, 0.5: 0, 0.2: 0, 0.1: 0};

                    for (var billete in billetes) {
                        document.getElementById('billete_' + billete).value = billetes[billete];
                        document.getElementById('total_billete_' + billete).value = (billetes[billete] * billete).toFixed(2);
                    }

                    for (var moneda in monedas) {
                        document.getElementById('moneda_' + moneda.replace('.', '_')).value = monedas[moneda];
                        document.getElementById('total_moneda_' + moneda.replace('.', '_')).value = (monedas[moneda] * moneda).toFixed(2);
                    }

                    calcularTotal();

                    document.getElementById('seleccionarCajaDiv').style.display = 'none';
                    document.getElementById('arqueoCajaDiv').style.display = 'block';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron obtener los detalles de la transacción.',
                        icon: 'error'
                    });
                }
            });
    }

    function calcularTotal() {
        var billetes = [200, 100, 50, 20, 10];
        var monedas = [5, 2, 1, 0.5, 0.2, 0.1];

        var totalBilletes = 0;
        var totalMonedas = 0;

        billetes.forEach(function(billete) {
            var cantidad = parseFloat(document.getElementById('billete_' + billete).value) || 0;
            var total = cantidad * billete;
            document.getElementById('total_billete_' + billete).value = total.toFixed(2);
            totalBilletes += total;
        });

        monedas.forEach(function(moneda) {
            var cantidad = parseFloat(document.getElementById('moneda_' + moneda.toString().replace('.', '_')).value) || 0;
            var total = cantidad * moneda;
            document.getElementById('total_moneda_' + moneda.toString().replace('.', '_')).value = total.toFixed(2);
            totalMonedas += total;
        });

        var totalEfectivo = totalBilletes + totalMonedas;
        document.getElementById('total_efectivo').value = totalEfectivo.toFixed(2);
    }

    function abrirCaja() {
        var formData = new FormData(document.getElementById('abrirCajaForm'));

        fetch("{{ url('/admin/caja/abrir') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Caja abierta correctamente.',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '/admin/caja/pagarcredito';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al abrir la caja.',
                        icon: 'error'
                    });
                }
            });
    }

    function cancelar() {
        window.location.href = '/';
    }
</script>
@endsection
