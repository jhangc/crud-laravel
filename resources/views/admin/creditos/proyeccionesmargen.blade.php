@extends('layouts.admin')

@section('content')
    <div class="row evaluacion">
        <h3 class="titulo">EVALUACION FINANCIERA</h3>
        <h6><b><span>TIPO DE CREDITO:</span></b> {{ $prestamo->tipo }}</h6>
        <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
        <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
        <h6><b>CLIENTES:</b>
            @foreach ($prestamo->clientes as $cliente)
                {{ $cliente->nombre }}@if (!$loop->last)
                    ,
                @endif
            @endforeach
        </h6>
        <h6><b>ACTIVIDAD:</b> {{ $prestamo->descripcion_negocio }}</h6>
        <h6><b>RESPONSABLE:</b> {{ $responsable->name }}</h6>
        <h6><b>TOTAL PRESTAMO:</b> S/.{{ $totalprestamo }}</h6>
        <h6><b>CUOTA A EVALUAR:</b> S/.{{ $cuotaprestamo }}</h6>

    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Resumen del negocio</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" value="<?= $prestamo->id ?>" id="credito_id">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Indicador</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <!-- suma de total ventas al mes -->
                                <td>Total de Ventas</td>
                                <td>{{ $totalVentas }}</td>
                            </tr>
                            <tr>
                                <!-- suma de total compras al mes -->
                                <td>Costo de Ventas</td>
                                <td>{{ $totalCompras }}</td>
                            </tr>
                            <tr>
                                <!-- (Ventas - costo)/ventas -->
                                <td>Margen</td>
                                <td>
                                    {{ $margen }}
                                </td>
                            </tr>
                            <tr>
                                <!-- suma de todo el % participacion -->
                                <td>Participación</td>
                                <td>{{ $proporcion_ventas }}%</td>
                            </tr>
                            <tr>
                                <!-- Ventas - costo -->
                                <td>Utilidad</td>
                                <td>{{ $utilidadBruta }}</td>
                            </tr>
                            <tr>
                                <!-- total gastos operativos -->
                                <td>Gastos operativos</td>
                                <td>{{ $totalGastosOperativos }}</td>
                            </tr>
                            <tr>
                                <!-- Total sventa al credito -->
                                <td>Venta al credito</td>
                                <td>{{ $total_venta_credito }}</td>
                            </tr>
                            <tr>
                                <!-- Total de inventario -->
                                <td>Productos terminados</td>
                                <td>{{ $total_inventario }}</td>
                            </tr>
                            {{-- estos dos ultimos colocar 0.00 por el momento --}}
                            <tr>
                                <!-- Total de inventario en proceso -->
                                <td>Productos en proceso</td>
                                <td>0.0</td>
                            </tr>
                            <tr>
                                <!-- Total de materiales -->
                                <td>Materiales</td>
                                <td>0.0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Resumen de cuentas</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Cuenta</th>
                                <th>Monto en S/.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- activo corriente mas fijo --}}
                                <td><b>Activo</b></td>
                                <td>{{ $activo }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Activo corriente (circulante)</td>
                                <td> {{ $activo_corriente }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Disponible (caja y bancos)</td>
                                <td>{{ $activos->saldo_en_caja_bancos }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Cuentas por cobrar</td>
                                <td>{{ $activos->cuentas_por_cobrar }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Adelanto a proveedores</td>
                                <td>{{ $activos->adelanto_a_proveedores }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Inventario</td>
                                <td>{{ $total_inventario }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px;">Productos terminados</td>
                                <td>{{ $total_inventario }}</td>
                            </tr>
                            {{-- <tr>
                            <td style="padding-left: 50px;">Productos terminados</td>
                            <td></td>
                        </tr> --}}
                            <tr>
                                <td style="padding-left: 50px;">En proceso productivo</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px;">Materiales</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Activo fijo</td>
                                <td>{{ $garantias->sum('valor_mercado') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Garantia</td>
                                <td>{{ $garantias->sum('valor_mercado') }}</td>
                            </tr>
                            <tr>
                                <td><b>Pasivo</b></td>
                                <td>{{ $deudas->sum('saldo_capital') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Deudas Financieras</td>
                                <td>{{ $deudas->sum('saldo_capital') }}</td>
                            </tr>
                            <tr>
                                <td><b>Patrimonio neto</b></td>
                                <td>{{ $patrimonio }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Saldo Total negocio</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto en S/.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ingresos ventas</td>
                                <td>{{ $totalVentas }}</td>
                            </tr>
                            <tr>
                                <td>Costo de ventas</td>
                                <td>{{ $totalCompras }}</td>
                            </tr>
                            <tr>
                                <td>Utilidad</td>
                                <td>{{ $totalVentas - $totalCompras }}</td>
                            </tr>
                            <tr>
                                <td>Gastos operativos</td>
                                <td>{{ $totalGastosOperativos }}</td>
                            </tr>
                            <tr>
                                <td>Utilidad operativa</td>
                                <td>{{ $utilidadOperativa }}</td>
                            </tr>
                            <tr>
                                <td>Gastos financieros</td>
                                <td>{{ $totalcuotadeuda }}</td>
                            </tr>
                            <tr>
                                <td>Saldo disponible del negocio</td>
                                <td>{{ $saldo_disponible_negocio }}</td>
                            </tr>
                            <tr>
                                <td>Gastos familiares</td>
                                <td>{{ number_format($totalgastosfamiliares, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Saldo final dsiponible</td>
                                <td>{{ $saldo_final }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Ratios Financieros</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Valor</th>
                                <th>resultado esperado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- colocar dle cuadro del excel --}}
                                <td>Rentabilidad del negocio (%)</td>
                                <td>{{ $margenventas }}%</td>
                                <td>Es el margen generico segun actividad</td>
                            </tr>
                            <tr>
                                {{-- division entre saldo total disponible y ventas total --}}
                                <td>Rentabilidad de las ventas</td>
                                <td>{{ $rentabilidad_ventas }}%</td>
                                <td>tiene que ser +- 5 de la rentabilidad</td>
                            </tr>
                            <tr>
                                {{-- total costo / total inventario --}}
                                <td>Rotación de inventario (en días)</td>
                                <td>{{ $rotacion_inventario }}</td>
                                <td>cada que tiempo se compra producto en días</td>
                            </tr>
                            <tr>
                                {{-- activo corriente / pasivo corriente --}}
                                <td>Liquidez</td>
                                <td>{{ $liquidez }}</td>
                                <td>tiene que ser (>1)</td>
                            </tr>
                            <tr>
                                {{-- UTILIDAD NETA / ACTIVOS TOTALES --}}
                                <td>ROA (%)</td>
                                <td>{{ $roa }}%</td>
                                <td>tiene que ser (>5%)</td>
                            </tr>
                            <tr>
                                {{-- ACTIVO CORRIENTE - PASIVO CORRIENTE --}}
                                <td>Capital de trabajo (S/.)</td>
                                <td>{{ $capital_trabajo }}</td>
                                <td>tiene que ser mayor al prestamo</td>
                            </tr>
                            <tr>
                                {{-- UTILIDAD NETA / PATRIMONIO NETO --}}
                                <td>ROE (%)</td>
                                <td>{{ $roe }}%</td>
                                <td>tiene que ser (>10%)</td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / PATRIMONIO NETO --}}
                                <td>Solvencia</td>
                                <td>{{ $solvencia }}</td>
                                <td>tiene que ser (<=1)< /td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / ACTIVO TOTAL --}}
                                <td>Indice de endeudamiento</td>
                                <td>{{ $indice_endeudamiento }}%</td>
                                <td>tiene que ser (<=40%)< /td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL + prestamos / patrimonio --}}
                                <td>Endeudamiento patrimonial</td>
                                <td>{{ $Endeudamientopatrimonial }}</td>
                                <td>tiene que ser (<=1)< /td>
                            </tr>
                            <tr>
                                {{-- cuota de prestamo / saldo final --}}
                                <td>cuotaexcedente</td>
                                <td>{{ $cuotaexcedente }}</td>
                                <td>tiene que ser (<1)< /td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="text-align:center;">
        <div class="col-md-12 mb-5">
            <div class="form-group">
                <input type="hidden" value="<?= $prestamo->id ?>" id="credito_id">
                <label for="comentario">Comentario:</label>
                <textarea name="comentario" id="comentario" class="form-control" rows="3" style="color: black;" required><?php if (isset($comentarioasesor) && !empty($comentarioasesor)) {
                    echo htmlspecialchars($comentarioasesor, ENT_QUOTES, 'UTF-8');
                } ?></textarea>
            </div>
            <button type="button" onclick="confirmarAccion('guardar')" class="btn btn-primary btnprestamo">Guardar</button>
            <a href="{{ url('admin/creditos') }}" class="btn btn-warning btnprestamo">Cancelar</a>
        </div>
    </div>


    <script>
        function confirmarAccion(accion) {
            const comentario = document.getElementById('comentario').value;
            const creditoid = document.getElementById('credito_id').value;

            $.ajax({
                url: '{{ url('/admin/credito') }}/' + accion,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: creditoid,
                    comentario: comentario
                },
                success: function(response) {
                    alert(response.mensaje);
                    window.location.href = response.redirect;
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Ocurrió un error al realizar la acción.');
                }
            });
        }
    </script>
@endsection
