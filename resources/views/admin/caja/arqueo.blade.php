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
                                    <input type="number" class="form-control" id="billete_{{ $billete }}" name="billete_{{ $billete }}" value="0" onchange="calcularTotalcaja()">
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
                                    <input type="number" class="form-control" id="moneda_{{ str_replace('.', '_', $moneda) }}" name="moneda_{{ str_replace('.', '_', $moneda) }}" value="0" onchange="calcularTotalcaja()">
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
                                    <input type="number" class="form-control" id="depositos" name="depositos" value="0" onchange="calcularTotalcaja()">
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
                            <div class="btn-group btn-group-justified" role="group" aria-label="Total Efectivo" style="background: ;">
                                <!-- <input type="text" class="btn btn-info" id="total_apertura" value="Apertura: S/. {{ $montoApertura }}" disabled> -->
                                <input type="text" class="btn btn-success" id="total_ingresos" value="Ingresos: S/. {{ $ingresos ?? 0 }}"  style="font-weight: bold;" >
                                <input type="text" class="btn btn-warning" id="total_egresos" value="Egresos: S/. {{ $egresos ?? 0 }}"  style="font-weight: bold;" >
                                <input type="text" class="btn btn-danger" id="total_gastos" value="Gastos: S/. {{ $gastos ?? 0 }}"  style="font-weight: bold;" >
                                <input type="text" class="btn btn-info" id="total_depositos_display" value="Depósitos: S/. 0.00"  style="font-weight: bold;" >
                                <input type="text" class="btn btn-primary" id="total_efectivo_display" value="Total Efectivo: S/. 0.00"  style="font-weight: bold;" >
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-3">
                        <!-- <div class="col-md-6"> -->
                            <!-- <label for="saldo_final">Saldo Final (Monto de apertura + Ingresos - Egresos - Gastos):</label> -->
                            <input type="hidden" class="form-control" id="saldo_final" name="saldo_final" value="S/. 0.00" readonly>
                        <!-- </div> -->
                        <div class="col-md-6">
                            <label for="saldo_finalcaja">Saldo Final en Caja (Total efectivo + Depósitos):</label>
                            <input type="text" class="form-control" id="saldo_finalcaja" name="saldo_finalcaja" value="S/. 0.00" readonly>
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
        var montoApertura = parseFloat(document.getElementById('monto_apertura').value) || 0;
        var ingresos = parseFloat('{{ $ingresos ?? 0 }}');
        var egresos = parseFloat('{{ $egresos ?? 0 }}');
        var gastos = parseFloat('{{ $gastos ?? 0 }}');
        var saldoFinal = montoApertura + ingresos - egresos - gastos;
        
        document.getElementById('saldo_final').value = 'S/. ' + saldoFinal.toFixed(2);
    }
    calcularTotal();

    function calcularTotalcaja() {
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
        
        var totalFinalEfectivo = totalEfectivo + depositos;
        document.getElementById('total_efectivo_display').value = 'Total Efectivo: S/. ' + totalEfectivo.toFixed(2);
        document.getElementById('total_depositos_display').value = 'Depósitos: S/. ' + depositos.toFixed(2);

        var saldoFinal = totalFinalEfectivo;
        document.getElementById('saldo_finalcaja').value = 'S/. ' + saldoFinal.toFixed(2);
    }

    function guardarArqueo() {
        var saldoFinalCaja = parseFloat(document.getElementById('saldo_finalcaja').value.replace('S/. ', '')) || 0;
        var saldoFinal = parseFloat(document.getElementById('saldo_final').value.replace('S/. ', '')) || 0;

        // if (saldoFinalCaja !== saldoFinal) {
        //     Swal.fire({
        //         title: 'Error',
        //         text: 'El saldo final en caja no cuadra con el saldo final calculado. No se puede cerrar la caja.',
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
                    var newWindow = window.open('/admin/caja/generar-arqueo-pdf/' + data.transaccion_id, '_blank');
                    if (newWindow) {
                        newWindow.focus();
                        window.location.href = '/';
                    } else {
                        Swal.fire({
                            title: 'Aviso',
                            text: 'Por favor, permite las ventanas emergentes para ver el PDF.',
                            icon: 'warning'
                        });
                    }
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
