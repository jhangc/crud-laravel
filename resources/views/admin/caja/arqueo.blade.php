@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Arqueo de Caja</h1>
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Arqueo de Caja</h3>
            </div>
            <div class="card-body">
                <form id="arqueoCajaForm">
                    @csrf
                    <input type="hidden" id="caja_id_hidden" name="caja_id" value="{{ $cajaAbierta->id }}">

                    <div id="arqueoDetalles" class="form-group col-md-6">
                        <label for="monto_apertura">Monto de Apertura</label>
                        <input type="number" id="monto_apertura" name="monto_apertura" class="form-control" value="{{ $montoApertura }}" readonly>
                    </div>

                    <!-- Diseño de Arqueo de Caja -->
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
                                    <input type="number" class="form-control" id="billete_{{ $billete }}" name="billete_{{ $billete }}" value="0" onchange="calcularTotal()">
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
                                    <input type="number" class="form-control" id="moneda_{{ str_replace('.', '_', $moneda) }}" name="moneda_{{ str_replace('.', '_', $moneda) }}" value="0" onchange="calcularTotal()">
                                </div>
                                <label class="col-sm-2 col-form-label">= S/. </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="total_moneda_{{ str_replace('.', '_', $moneda) }}" readonly>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Depósitos -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Depósitos</h5>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Depósitos</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" id="depositos" name="depositos" value="0" onchange="calcularTotal()">
                                </div>
                                <label class="col-sm-2 col-form-label">= S/. </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="total_depositos" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <!-- Totales -->
                    <div class="row ">
                        <div class="col-md-12">
                            <div class="btn-group btn-group-justified" role="group" aria-label="Total Efectivo">
                                <input type="text" class="btn btn-info" id="total_apertura" value="Apertura: S/. {{ $montoApertura }}" disabled>
                                <input type="text" class="btn btn-info" id="total_ingresos" value="Ingresos: S/. {{ $ingresos ?? 0 }}" disabled>
                                <input type="text" class="btn btn-info" id="total_egresos" value="Egresos: S/. {{ $egresos ?? 0 }}" disabled>
                                <input type="text" class="btn btn-info" id="total_depositos_display" value="Depósitos: S/. 0.00" disabled>
                                <input type="text" class="btn btn-info" id="total_efectivo_display" value="Total Efectivo: S/. 0.00" disabled>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="saldo_final">Saldo Final(Monto incial+ingresos-egresos):</label>
                            <input type="text" class="form-control" id="saldo_final" name="saldo_final" value="S/. 0.00" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary btn-block" onclick="guardarArqueo()">Guardar Arqueo</button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary btn-block" onclick="cancelar()">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
        var depositos = parseFloat(document.getElementById('depositos').value) || 0;
        document.getElementById('total_depositos').value = depositos.toFixed(2);
        
        var totalFinalEfectivo = totalEfectivo;
        document.getElementById('total_efectivo_display').value = 'Total Efectivo: S/. ' + totalFinalEfectivo.toFixed(2);
        document.getElementById('total_depositos_display').value = 'Depósitos: S/. ' + depositos.toFixed(2);

        var montoApertura = parseFloat(document.getElementById('monto_apertura').value) || 0;
        var ingresos = parseFloat('{{ $ingresos ?? 0 }}');
        var egresos = parseFloat('{{ $egresos ?? 0 }}');
        var saldoFinal =  montoApertura +depositos+ ingresos - egresos;
        
        document.getElementById('saldo_final').value = 'S/. ' + saldoFinal.toFixed(2);
    }

    function guardarArqueo() {
        var saldoFinal = parseFloat(document.getElementById('saldo_final').value.replace('S/. ', '')) || 0;
        var montoApertura = parseFloat(document.getElementById('monto_apertura').value) || 0;
        var ingresos = parseFloat('{{ $ingresos ?? 0 }}');
        var egresos = parseFloat('{{ $egresos ?? 0 }}');

        var totalEsperado = montoApertura + ingresos - egresos;
        // if (saldoFinal !== totalEsperado) {
        //     Swal.fire({
        //         title: 'Error',
        //         text: 'El saldo final no cuadra con el total esperado. No se puede cerrar la caja.',
        //         icon: 'error'
        //     });
        //     return;
        // }

        var formData = new FormData(document.getElementById('arqueoCajaForm'));

        fetch("{{ route('caja.guardarArqueo') }}", {
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
                        text: 'Arqueo guardado correctamente.',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al guardar el arqueo.',
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
