@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h1 class="card-title"><b>Simulador De Cronograma</b></h1>
                </div>
                <div class="card-body">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Crédito</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" id="recurencia_individual">
                                        <label for="recurrencia">Recurrencia</label>
                                        <select name="recurrencia" id="recurrencia" class="form-control">
                                            <option value="">Seleccione una opción...</option>
                                            <option value="mensual">Mensual</option>
                                            <option value="quincenal">Quincenal</option>
                                            <option value="trimestral">Trimestral</option>
                                            <option value="semestral">Semestral</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tasa_interes">Tasa de interés anual (%)</label>
                                        <input type="text" name="tasa_interes" id="tasa_interes" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tiempo_credito">Tiempo del crédito</label>
                                        <input type="text" name="tiempo_credito" id="tiempo_credito" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_desembolso">Fecha de desembolso</label>
                                        <input type="date" name="fecha_desembolso" id="fecha_desembolso"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periodo_gracia_dias">Periodo de Gracia (días)</label>
                                        <input type="number" name="periodo_gracia_dias" id="periodo_gracia_dias"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="monto">Monto total (S/.)</label>
                                        <input type="text" name="monto" id="monto" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="calcularCronograma()"
                                        class="btn btn-info btnprestamo">Calcular</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Tabla de Cronograma</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                    <thead class="thead-blue">
                                        <tr>
                                            <th>Nº CUOTA</th>
                                            <th>Fecha de Vencimiento</th>
                                            <th>Capital</th>
                                            <th>Interes</th>
                                            <th>Amortización</th>
                                            <th>Total Soles</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProyecciones">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"><strong>Total:</strong></td>
                                            <td id="totalInteres">0.00</td>
                                            <td id="totalAmortizacion">0.00</td>
                                            <td id="totalSoles">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function calcularCronograma() {
            const monto = parseFloat(document.getElementById('monto').value);
            const tea = parseFloat(document.getElementById('tasa_interes').value);
            const periodos = parseInt(document.getElementById('tiempo_credito').value);
            const frecuencia = document.getElementById('recurrencia').value;
            const fechaDesembolso = document.getElementById('fecha_desembolso').value;

            const periodoGraciaDias = parseInt(document.getElementById('periodo_gracia_dias').value);
            let fechaInicio = new Date(new Date(fechaDesembolso).setDate(new Date(fechaDesembolso).getDate() +
                periodoGraciaDias));

            // Calcular los intereses del período de gracia
            const tasaDiaria = Math.pow(1 + (tea / 100), 1 / 360) - 1;
            const interesesPeriodoGracia = monto * tasaDiaria * periodoGraciaDias;
            const interesesMensualesPorGracia = interesesPeriodoGracia / periodos;

            let n;
            switch (frecuencia) {
                case 'catorcenal':
                    n = 24;
                    break;
                case 'veinteochenal':
                    n = 12;
                    break;
                case 'quincenal':
                    n = 24;
                    break;
                case 'semestral':
                    n = 2;
                    break;
                case 'trimestral':
                    n = 4;
                    break;
                case 'mensual':
                default:
                    n = 12;
                    break;
            }

            const tasaPeriodo = Math.pow(1 + (tea / 100), 1 / n) - 1;
            const cuota = (monto * tasaPeriodo * Math.pow(1 + tasaPeriodo, periodos)) / (Math.pow(1 + tasaPeriodo,
                periodos) - 1);
            const cuota_real = cuota + interesesMensualesPorGracia;

            let saldo = monto;
            let cuotas = [];
            let totalCapital = 0;
            let totalInteres = 0;
            let totalAmortizacion = 0;
            let totalSoles = 0;

            for (let i = 0; i < periodos; i++) {
                const interesPeriodo = saldo * tasaPeriodo + interesesMensualesPorGracia;
                const amortizacion = cuota_real - interesPeriodo;
                saldo -= amortizacion;

                let fechaVencimiento;
                switch (frecuencia) {
                    case 'quincenal':
                        fechaVencimiento = new Date(new Date(fechaInicio).setDate(fechaInicio.getDate() + ((i + 1) * 15)));
                        break;
                    case 'mensual':
                        fechaVencimiento = new Date(new Date(fechaInicio).setMonth(fechaInicio.getMonth() + (i + 1)));
                        break;
                    case 'trimestral':
                        fechaVencimiento = new Date(new Date(fechaInicio).setMonth(fechaInicio.getMonth() + ((i + 1) * 3)));
                        break;
                    case 'semestral':
                        fechaVencimiento = new Date(new Date(fechaInicio).setMonth(fechaInicio.getMonth() + ((i + 1) * 6)));
                        break;
                    default:
                        fechaVencimiento = new Date(new Date(fechaInicio).setMonth(fechaInicio.getMonth() + (i + 1)));
                        break;
                }

                cuotas.push({
                    numero_cuota: i + 1,
                    fecha_vencimiento: fechaVencimiento,
                    capital: saldo.toFixed(2),
                    interes: interesPeriodo.toFixed(2),
                    amortizacion: amortizacion.toFixed(2),
                    cuota: cuota_real.toFixed(2)
                });

                totalInteres += parseFloat(interesPeriodo.toFixed(2));
                totalAmortizacion += parseFloat(amortizacion.toFixed(2));
                totalSoles += parseFloat(cuota_real.toFixed(2));
            }

            let tablaProyecciones = document.getElementById('tablaProyecciones');
            tablaProyecciones.innerHTML = '';
            cuotas.forEach(cuota => {
                let row = `
            <tr>
                <td>${cuota.numero_cuota}</td>
                <td>${cuota.fecha_vencimiento.toISOString().split('T')[0]}</td>
                <td>${cuota.capital}</td>
                <td>${cuota.interes}</td>
                <td>${cuota.amortizacion}</td>
                <td>${cuota.cuota}</td>
            </tr>
        `;
                tablaProyecciones.innerHTML += row;
            });

            document.getElementById('totalInteres').textContent = totalInteres.toFixed(2);
            document.getElementById('totalAmortizacion').textContent = totalAmortizacion.toFixed(2);
            document.getElementById('totalSoles').textContent = totalSoles.toFixed(2);
        }
    </script>
@endsection
