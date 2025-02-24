@extends('layouts.admin')

@section('content')
<style>
    .container.flex {
        width: 100%;
        max-width: 1700px;
        padding: 0 15px;
        margin: 0 auto;
    }
</style>
<div class="container flex container-large">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Cuotas del Crédito #{{ $credito->id }}</h1>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#amortizarCapitalModal" onclick="calcularcuotapendiente()">
            Amortizar Capital
        </button>
    </div>
    @if ($credito->categoria == 'grupal')
    <h2>Cuotas Generales</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Cuota</th>
                <th>Monto</th>
                <th>Fecha Vencimiento</th>
                <th>Días de Mora</th>
                <th>Monto de Mora</th>
                <th>Monto Total a Pagar</th>
                <th>Estado</th>
                <th>Pagadas</th>
                <th>Pendientes</th>
                <th>Vencidas</th>
                <th>Monto Pagado</th>
                <th>Monto Pendiente</th>
                <th>Monto Vencido</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cuotasGenerales as $cuota)
            <tr>
                <td>{{ $cuota->numero }}</td>
                <td>{{ number_format($cuota->monto, 2) }}</td>
                <td>{{ $cuota->fecha }}</td>
                <td>{{ $cuota->dias_mora }}</td> <!-- Mostrar los días de mora -->
                <td>{{ number_format($cuota->monto_mora, 2) }}</td> <!-- Mostrar el monto de mora -->
                <td>{{ number_format($cuota->monto_total_pago_final, 2) }}</td> <!-- Mostrar el monto total a pagar -->
                <td>
                    @if ($cuota->estado == 'pagado')
                    <span class="badge badge-success">Pagado</span>
                    @elseif ($cuota->estado == 'vencida')
                    <span class="badge badge-danger">{{$cuota->dias_mora = 0?'VENCE-HOY':'vencida'}}</span>
                    @elseif ($cuota->estado == 'pendiente')
                    <span class="badge badge-warning">Pendiente</span>
                    @else
                    <span class="badge badge-info">Parcial</span>
                    @endif
                </td>
                <td>{{ $cuota->pagadas }}</td>
                <td>{{ $cuota->pendientes }}</td>
                <td>{{ $cuota->vencidas }}</td>
                <td>S/. {{ number_format($cuota->monto_pagado, 2) }}</td>
                <td>S/. {{ number_format($cuota->monto_pendiente, 2) }}</td>
                <td>S/. {{ number_format($cuota->monto_vencido, 2) }}</td>
                <td>
                    @if ($cuota->estado == 'pagado')
                    {{ $cuota->fecha_pago }}
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('generar.ticket.pagogrupal', ['array' => implode('-', $cuota->ingreso_ids)]) }}" target="_blank" class="btn btn-info mr-2 mb-2">Ver Ticket</a>
                            @if($cuota->pago_capital != null)
                                <a href="{{ url('/vernuevocronograma/'.$credito->id) }}" target="_blank" class="btn btn-warning mb-2">Ver Nuevo Cronograma</a>
                            @endif
                        </div>
                    @elseif ($cuota->estado == 'pendiente' || $cuota->estado == 'vencida' || $cuota->estado == 'parcial')
                    @if ($cuota->ultima == 1)
                    <button class="btn btn-info" data-toggle="modal" data-target="#pagarTodoModal" onclick="pagarTodogrupal({{ $credito->id }}, '{{ $cuota->fecha }}', '{{ $cuota->numero }}')">
                        PAGAR TODO
                    </button>
                    @endif
                    <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : 'primary' }}" onclick="pagarCuotaGeneral({{ $credito->id }}, '{{ $cuota->fecha }}')">Pagar</button>
                    @else
                    {{ $cuota->fecha_pago }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @foreach ($clientesCredito as $clienteCredito)
    <h3>Cliente: {{ $clienteCredito->clientes->nombre }}</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Cuota</th>
                <th>Monto</th>
                <th>Fecha Vencimiento</th>
                <th>Días de Mora</th>
                <th>Monto de Mora</th>
                <th>Monto Total a Pagar</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cuotasPorCliente[$clienteCredito->cliente_id] as $cuota)
            <tr>
                <td>{{ $cuota->numero }}</td>
                <td>{{ number_format($cuota->monto, 2) }}</td>
                <td>{{ $cuota->fecha }}</td>
                <td>{{ $cuota->dias_mora }}</td>
                <td>{{ number_format($cuota->monto_mora, 2) }}</td>
                <td>{{ number_format($cuota->monto_total_pago_final, 2) }}</td>
                <td>
                    @if ($cuota->estado == 'pagado')
                    <span class="badge badge-success">Pagado</span>
                    @elseif ($cuota->estado == 'vencida')
                    <span class="badge badge-danger">{{$cuota->dias_mora >1?'vencida':'VENCE-HOY'}}</span>
                    @else
                    <span class="badge badge-warning">Pendiente</span>
                    @endif
                </td>
                <td>
                    @if ($cuota->estado == 'pagado')
                    {{ $cuota->fecha_pago }}
                    <div class="d-flex flex-wrap">
                    <a href="{{ route('generar.ticket.pago', ['id' => $cuota->ingreso_id]) }}" target="_blank" class="btn btn-info">Ver Ticket</a>
                    @if($cuota->pago_capital != null)
                                <a href="{{ url('/vernuevocronograma/'.$credito->id) }}" target="_blank" class="btn btn-warning mb-2">Ver Nuevo Cronograma</a>
                    @endif
                    </div>
                    @elseif ($cuota->estado == 'pendiente' || $cuota->estado == 'vencida')
                    @if ($cuota->ultima=='1' && $credito->categoria != 'grupal')
                    <button class="btn btn-info" data-toggle="modal" data-target="#pagarTodoModal"  onclick="pagarTodoindividual({{ $credito->id }}, '{{ $cuota->fecha }}','{{ $cuota->numero }}')">PAGAR TODO</button>
                    @endif
                    <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : 'primary' }}" onclick="pagarCuota({{ $credito->id }}, {{ $clienteCredito->cliente_id }}, {{ $cuota->id }}, {{ $cuota->numero }}, {{ $cuota->monto_total_pago_final }}, {{ $cuota->monto }}, {{ $cuota->dias_mora }}, {{ $cuota->porcentaje_mora }})">Pagar</button>
                    @else
                    {{ $cuota->fecha_pago }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
    <div class="modal fade" id="pagarTodoModal" tabindex="-1" role="dialog" aria-labelledby="pagarTodoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pagarTodoModalLabel">Pagar Todo el Crédito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto (S/.)</th>
                                <th>Dias de Mora</th>
                                <th>monto de Mora</th>
                                <th>Total (S/.)</th>
                            </tr>
                        </thead>
                        <tbody id="detallePagoTodo">
                            <!-- Aquí se llenarán los datos dinámicamente -->
                        </tbody>
                    </table>
                    <h4>Total a Pagar: <span id="totalPagarTodo"></span></h4>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="amortizarCapitalModal" tabindex="-1" role="dialog" aria-labelledby="amortizarCapitalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="amortizarCapitalModalLabel">Amortización al Capital</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($cuotasVencidas > 0)
                    <div class="alert alert-warning">
                        Tienes cuotas vencidas. Debes pagarlas antes de realizar una amortización al capital.
                    </div>
                    @elseif ($puedeAmortizar < 2)
                        <div class="alert alert-warning">
                        Debes haber pagado al menos 2 cuotas para realizar una amortización al capital.
                </div>
                @else
                <form id="amortizarCapitalForm">
                    @csrf
                    <input type="hidden" name="prestamo_id" value="{{ $credito->id }}">
                    <!-- Sección de datos a enviar -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Número de Cuota-Actual</label>
                                <input type="text" class="form-control" id="numero_cuota" name="numero_cuota"  disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Intereses de cuota Actual</label>
                                <input type="text" class="form-control" id="intereses_a_pagar" name="intereses_a_pagar"  disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Capital Pendiente </label>
                                <input type="text" class="form-control" id="capital_pendiente" name="capital_pendiente"  disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Max-Total a Pagar</label>
                                <input type="text" class="form-control" id="total_a_pagar_posible" name="total_a_pagar_posible"  disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="opcionesAmortizacion" class="mb-3">
                        <label class="d-block"><strong>Opciones de Amortización:</strong></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="opcion_amortizacion" id="opcionReducirCuota" value="reducir_cuota" checked>
                            <label class="form-check-label" for="opcionReducirCuota">Reducir Cuota y Mantener Plazo</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="opcion_amortizacion" id="opcionReducirPlazo" value="reducir_plazo">
                            <label class="form-check-label" for="opcionReducirPlazo">Reducir plazo y Mantener cuota</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="monto_pago_capital">Monto a Pagar ( Pago Capital + Intereses):</label>
                        <input type="number" class="form-control" name="monto_pago_capital" id="monto_pago_capital" required >
                    </div>
                    <button type="button" class="btn btn-secondary mb-3" onclick="generarNuevoCronograma()">Ver Nuevo Cronograma</button>

                    <div id="nuevoCronograma" class="mb-3"></div>
                    <button type="button" class="btn btn-primary" id="btnAmortizar" disabled onclick="confirmarpagoCapital()" >Amortizar Capital</button>
                </form>
                @endif
            </div>
        </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function pagarCuota(prestamo_id, cliente_id, cronograma_id, numero_cuota, monto_total_pago_final, monto, dias_mora, porcentaje_mora) {
        Swal.fire({
            title: '¿Está seguro?',
            text: `Está a punto de pagar la cuota #${numero_cuota} por un monto de S/. ${monto_total_pago_final.toFixed(2)}.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, pagar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('creditos.pagocuota')}}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prestamo_id: prestamo_id,
                        cliente_id: cliente_id,
                        cronograma_id: cronograma_id,
                        numero_cuota: numero_cuota,
                        monto: monto_total_pago_final,
                        monto_mora: (monto_total_pago_final - monto).toFixed(2),
                        dias_mora: dias_mora,
                        porcentaje_mora: porcentaje_mora
                    },
                    success: function(response) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.success,
                            icon: 'success'
                        }).then(() => {
                            window.open('/admin/generar-ticket-pago/' + response.ingreso_id, '_blank');
                            location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            title: 'Error',
                            text: response.responseJSON.error,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    function pagarCuotaGeneral(prestamo_id, fecha) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Está a punto de pagar todas las cuotas vencidas en la fecha ' + fecha + '.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, pagar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('creditos.pagogrupal') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prestamo_id: prestamo_id,
                        fecha: fecha
                    },
                    success: function(response) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.success,
                            icon: 'success'
                        }).then(() => {
                            var ids = response.ingreso_ids.join('-');
                            window.open('/admin/generar-ticket-pagogrupal/' + ids, '_blank');
                            location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            title: 'Error',
                            text: response.responseJSON.error,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    function pagarTodogrupal(prestamo_id, fecha, numero_cuota) {
        console.log(prestamo_id, fecha, numero_cuota);
        $.ajax({
            url: '{{ route('credito.verpagototalgrupal')}}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prestamo_id: prestamo_id,
                fecha: fecha,
                numero_cuota: numero_cuota
            },
            success: function(response) {
                var detalle = '';
                var totalPagar = response.total_pagar;

                response.detalle_cuotas.forEach(function(cuota) {
                    detalle += `<tr>
                                    <td>Cuota ${cuota.numero}</td>
                                    <td>S/. ${parseFloat(cuota.monto).toFixed(2)}</td>
                                    <td>${cuota.dias_mora} días</td>
                                    <td>S/. ${parseFloat(cuota.monto_mora).toFixed(2)}</td>
                                    <td>S/. ${parseFloat(cuota.total_pagar).toFixed(2)}</td>
                                </tr>`;
                });

                // Mostrar el detalle en el modal
                $('#detallePagoTodo').html(detalle);
                // Mostrar el total a pagar
                $('#totalPagarTodo').text(`S/. ${totalPagar.toFixed(2)}`);

                // Añadir botón de pagar
                var botonPagar = `<button class="btn btn-warning" onclick="confirmarpagarTodogrupal(event,'${prestamo_id}','${fecha}','${numero_cuota}')">Pagar Todo</button>
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>`;
                $('#pagarTodoModal .modal-footer').html(botonPagar);
            },
            error: function(err) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudieron obtener los datos del crédito.',
                    icon: 'error'
                });
            }
        });
    }

    function pagarTodoindividual(prestamo_id, fecha, numero_cuota) {
        $.ajax({
            url: '{{ route('credito.verpagototalindividual') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prestamo_id: prestamo_id,
                fecha: fecha,
                numero_cuota: numero_cuota,
            },
            success: function(response) {
                var detalle = '';
                var totalPagar = response.total_pagar;

                response.detalle_cuotas.forEach(function(cuota) {
                    detalle += `<tr>
                                    <td>Cuota ${cuota.numero}</td>
                                    <td>S/. ${parseFloat(cuota.monto).toFixed(2)}</td>
                                    <td>${cuota.dias_mora} días</td>
                                    <td>S/. ${parseFloat(cuota.monto_mora).toFixed(2)}</td>
                                    <td>S/. ${parseFloat(cuota.total_pagar).toFixed(2)}</td>
                                </tr>`;
                });

                // Mostrar el detalle en el modal
                $('#detallePagoTodo').html(detalle);
                // Mostrar el total a pagar
                $('#totalPagarTodo').text(`S/. ${totalPagar.toFixed(2)}`);

                // Añadir botón de pagar
                var botonPagar = `<button class="btn btn-warning" onclick="confirmarpagarTodoindividual(event,'${prestamo_id}','${fecha}','${numero_cuota}')">Pagar Todo</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>`;
                $('#pagarTodoModal .modal-footer').html(botonPagar);
            },
            error: function(err) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudieron obtener los datos del crédito.',
                    icon: 'error'
                });
            }
        });
    }

    function confirmarpagarTodoindividual(event, prestamo_id, fecha, numero_cuota) {
        Swal.fire({
            title: '¿Confirmar Pago?',
            text: "Está a punto de confirmar el pago de la cuota individual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('credito.confirmarPagoIndividual') }}',  // Ruta al método de pago individual
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prestamo_id: prestamo_id,
                        fecha: fecha,
                        numero_cuota: numero_cuota
                    },
                    success: function(response) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.success,
                            icon: 'success'
                        }).then(() => {
                            window.open('/admin/generar-ticket-pago/' + response.ingreso_ids.join('-'), '_blank');
                            location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            title: 'Error',
                            text: response.responseJSON.error,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    function confirmarpagarTodogrupal(event, prestamo_id, fecha, numero_cuota) {
        Swal.fire({
            title: '¿Confirmar Pago Grupal?',
            text: "Está a punto de confirmar el pago de todas las cuotas vencidas del grupo.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('credito.confirmarPagoGrupal') }}',  // Ruta al método de pago grupal
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prestamo_id: prestamo_id,
                        fecha: fecha,
                        numero_cuota: numero_cuota
                    },
                    success: function(response) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.success,
                            icon: 'success'
                        }).then(() => {
                            window.open('/admin/generar-ticket-pagogrupal/' + response.ingreso_ids.join('-'), '_blank');
                            location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            title: 'Error',
                            text: response.responseJSON.error,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    function calcularcuotapendiente() {
        $.ajax({
            url: '{{ route("calcular.cuota.pendiente") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prestamo_id: '{{ $credito->id }}',
                fecha_amortizacion: $('#fecha_amortizacion').val(),
            },
            success: function(response) {
                if (response.cuota) {
                    $('#numero_cuota').val(response.cuota.numero);
                    $('#intereses_a_pagar').val(response.intereses);
                    $('#capital_pendiente').val(response.amortizacion_faltante);
                    $('#total_a_pagar_posible').val(response.monto_total);
                } else {
                    $('#detalleCuotaPendiente').html(`
                <div class="alert alert-success">
                    No hay cuotas pendientes.
                </div>
                `);
                }
            },
            error: function(response) {
                Swal.fire({
                    title: 'Error',
                    text: response.responseJSON.error,
                    icon: 'error'
                });
            }
        });
    }

    function generarNuevoCronograma() {
        var opcion = $('input[name="opcion_amortizacion"]:checked').val();
        var prestamo_id = '{{ $credito->id }}';
        var numero_cuota = $('#numero_cuota').val();
        var intereses_a_pagar = parseFloat($('#intereses_a_pagar').val()) || 0;
        var capital_pendiente = $('#capital_pendiente').val();
        var total_a_pagar_posible = $('#total_a_pagar_posible').val();
        var monto_pago_capital = parseFloat($('#monto_pago_capital').val()) || 0;

        if (monto_pago_capital <= intereses_a_pagar || monto_pago_capital === 0) {
            Swal.fire({
                title: 'Error en el Pago de Capital',
                text: 'El monto a pagar de capital debe ser mayor que los intereses a pagar y no puede estar vacío.',
                icon: 'warning'
            });
            return; 
        }

        // ✅ Si pasa la validación, ejecuta la petición AJAX
        $.ajax({
            url: '/generarcronogram/temp',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prestamo_id: prestamo_id,
                opcion: opcion,
                numero_cuota: numero_cuota,
                intereses_a_pagar: intereses_a_pagar,
                capital_pendiente: capital_pendiente,
                total_a_pagar_posible: total_a_pagar_posible,
                monto_pago_capital: monto_pago_capital
            },
            success: function(response) {
                mostrarNuevoCronograma(response.nuevo_principal, response.cuotas);
                $('#btnAmortizar').prop('disabled', false);
                Swal.fire({
                    title: 'Nuevo Cronograma Generado',
                    text: 'Revisa el nuevo cronograma y procede con el pago.',
                    icon: 'success'
                });
            },
            error: function(response) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo generar el nuevo cronograma.',
                    icon: 'error'
                });
            }
        });
    }


    function mostrarNuevoCronograma(nuevo_principal, cuotas) {
        var tablaHtml = `
           <h4>💰 <strong>Nuevo Capital Pendiente: S/.${nuevo_principal}</strong> </h4><br>
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th># Cuota</th>
                        <th>Fecha de Pago</th>
                        <th>Cuota Total (S/.)</th>
                        <th>Capital (S/.)</th>
                        <th>Interés (S/.)</th>
                        <th>Saldo Deuda (S/.)</th>
                    </tr>
                </thead>
                <tbody>`;
        cuotas.forEach(function(cuota) {
            tablaHtml += `
                <tr>
                    <td><strong>${cuota.numero_cuota}</strong></td>
                    <td>${new Date(cuota.fecha_pago).toLocaleDateString('es-PE')}</td>
                    <td><strong>S/. ${cuota.cuota.toFixed(2)}</strong></td>
                    <td class="text-success">S/. ${cuota.capital.toFixed(2)}</td>
                    <td class="text-danger">S/. ${cuota.interes.toFixed(2)}</td>
                    <td><strong>S/. ${cuota.saldo_deuda.toFixed(2)}</strong></td>
                </tr>`;
        });
        tablaHtml += `</tbody></table>`;
        $('#nuevoCronograma').html(tablaHtml);
    }

    function  confirmarpagoCapital(){
        var opcion = $('input[name="opcion_amortizacion"]:checked').val();
        var prestamo_id = '{{ $credito->id }}';
        var numero_cuota = $('#numero_cuota').val();
        var intereses_a_pagar = parseFloat($('#intereses_a_pagar').val()) || 0;
        var capital_pendiente = $('#capital_pendiente').val();
        var total_a_pagar_posible = $('#total_a_pagar_posible').val();
        var monto_pago_capital = parseFloat($('#monto_pago_capital').val()) || 0;

        if (monto_pago_capital <= intereses_a_pagar || monto_pago_capital === 0) {
            Swal.fire({
                title: 'Error en el Pago de Capital',
                text: 'El monto a pagar de capital debe ser mayor que los intereses a pagar y no puede estar vacío.',
                icon: 'warning'
            });
            return; 
        }
         $.ajax({
            url: '/generarcronogram/final',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prestamo_id: prestamo_id,
                opcion: opcion,
                numero_cuota: numero_cuota,
                intereses_a_pagar: intereses_a_pagar,
                capital_pendiente: capital_pendiente,
                total_a_pagar_posible: total_a_pagar_posible,
                monto_pago_capital: monto_pago_capital
            },
            success: function(response) {
                Swal.fire({
                title: 'Pago Capital Realizado',
                text: 'Revisa el Nuevo Cronograma.',
                icon: 'success'
               }).then(() => {
                // Abrir en nueva pestaña
                window.open('/vernuevocronograma/' + prestamo_id, '_blank');

                // Recargar la página actual después de abrir la nueva pestaña
                location.reload();
            });
                
            },
            error: function(response) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo generar el nuevo cronograma.',
                    icon: 'error'
                });
            }
        });


    }
</script>


@endsection