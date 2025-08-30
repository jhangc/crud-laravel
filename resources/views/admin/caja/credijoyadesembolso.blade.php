@extends('layouts.admin')

@section('content')
<style>
  .gap-2{ gap:.5rem; }
  .text-sm{ font-size: .95rem; }
  .card { border-radius: 14px; }
  .table th, .table td { vertical-align: middle; }
  .fila-cuotas.d-none { display: none !important; }
  .table-sm td, .table-sm th { padding: .45rem .5rem; }

  
  .badge-rule{ font-size:.75rem; font-weight:600; padding:.25rem .5rem; border-radius:10px; }
  .badge-vencida{ background:#ffe6e6; color:#b30000; }
  .badge-total{ background:#e6f0ff; color:#0047b3; }
  .badge-parcial{ background:#e6fff2; color:#006633; }

</style>
<div class="container-fluid">
  <!-- Encabezado -->
  <div class="row mb-3">
    <div class="col-12 d-flex align-items-center justify-content-between">
      <h2 class="mb-0">Desembolso CrediJoya</h2>
      <span class="badge badge-primary">Crédito #{{ $prestamo->id }}</span>
    </div>
  </div>

  <!-- Cabecera del crédito -->
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="row text-sm">
            <div class="col-md-4 mb-2">
              <div class="d-flex flex-column">
                <span class="text-muted">Tipo</span>
                <strong>{{ $prestamo->tipo }}</strong>
              </div>
              <div class="d-flex flex-column mt-2">
                <span class="text-muted">Producto</span>
                <strong>{{ $prestamo->producto }}</strong>
              </div>
            </div>

            <div class="col-md-4 mb-2">
              <div class="d-flex flex-column">
                <span class="text-muted">Cliente(s)</span>
                <strong>
                  @foreach ($prestamo->clientes as $cliente)
                    {{ $cliente->nombre }}@if (!$loop->last), @endif
                  @endforeach
                </strong>
              </div>
              <div class="d-flex flex-column mt-2">
                <span class="text-muted">Responsable</span>
                <strong>{{ optional($responsable)->name }}</strong>
              </div>
            </div>

            <div class="col-md-4 mb-2">
              <div class="d-flex flex-column">
                <span class="text-muted">Actividad</span>
                <strong>{{ $prestamo->descripcion_negocio }}</strong>
              </div>
              <div class="d-flex flex-column mt-2">
                <span class="text-muted">Total Préstamo</span>
                <strong>S/ {{ number_format($prestamo->monto_total,2,'.','') }}</strong>
              </div>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
  <!-- Joyas del crédito actual -->
  <div class="row mt-3">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <h5 class="mb-0">Joyas en garantía (crédito actual)</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
              <thead class="thead-light">
                <tr class="text-center">
                  <th style="width:80px">Kilataje</th>
                  <th>Precio/g</th>
                  <th>Peso neto (g)</th>
                  <th>Piezas</th>
                  <th>Descripción</th>
                  <th>Valor tasación</th>
                </tr>
              </thead>
              <tbody>
                @forelse($prestamo->joyas as $j)
                  @php
                    $precio = $j->precio_gramo ?? $j->precio ?? 0;
                    $neto   = $j->peso_neto ?? $j->peso ?? 0;
                    $valor  = $neto * $precio;
                  @endphp
                  <tr class="align-middle">
                    <td class="text-center">{{ $j->kilataje }}K</td>
                    <td class="text-right">{{ number_format($precio,2,'.','') }}</td>
                    <td class="text-right">{{ number_format($neto,2,'.','') }}</td>
                    <td class="text-center">{{ $j->piezas ?? 1 }}</td>
                    <td>{{ $j->descripcion }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($valor,2,'.','') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-4">Sin joyas registradas.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-3 py-2 border-top">
            <small class="text-muted">Valor ítem = peso neto × precio por gramo.</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cronograma del crédito actual -->
  <div class="row mt-3">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Cronograma del crédito</h5>
          <span class="badge badge-light">Cuotas: {{ $cronograma->count() }}</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
              <thead class="thead-light">
                <tr class="text-center">
                  <th style="width:60px">#</th>
                  <th>Vencimiento</th>
                  <th>Amortización</th>
                  <th>Interés</th>
                  <th>Total cuota</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $totCapital = $totInteres = $totCuota = 0;
                @endphp
                @forelse($cronograma as $c)
                  @php
                    $capital  = (float) ($c->amortizacion ?? 0);
                    $interes  = (float) ($c->interes ?? 0);
                    $cuota    = (float) ($c->monto);
                    $totCapital  += $capital;
                    $totInteres  += $interes;
                    $totCuota    += $cuota;
                    $vto = \Carbon\Carbon::parse($c->fecha_vencimiento ?? $c->fecha);
                  @endphp
                  <tr class="text-right">
                    <td class="text-center">{{ $c->nro_cuota ?? $loop->iteration }}</td>
                    <td class="text-center">{{ $vto->format('d/m/Y') }}</td>
                    <td>{{ number_format($capital,2,'.','') }}</td>
                    <td>{{ number_format($interes,2,'.','') }}</td>
                    <td class="font-weight-bold">{{ number_format($cuota,2,'.','') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No hay cronograma registrado.</td></tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr class="font-weight-bold text-right">
                  <td colspan="2" class="text-center">Totales</td>
                  <td>{{ number_format($totCapital,2,'.','') }}</td>
                  <td>{{ number_format($totInteres,2,'.','') }}</td>
                  <td>{{ number_format($totCuota,2,'.','') }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="px-3 py-2 border-top">
            <small class="text-muted">Cuota = Amortización + Interés.</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Deudas anteriores del cliente (con selector de CUOTAS) -->
  <div class="row mt-3">
    <!-- Modo de cálculo -->
   
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-body d-flex flex-wrap align-items-center">
            <label class="mb-0 mr-3 text-muted"><strong>Modo de Pago :</strong></label>
            <div class="custom-control custom-radio mr-4">
              <input type="radio" id="modoParcial" name="modoCalculo" class="custom-control-input" value="parcial" checked>
              <label class="custom-control-label" for="modoParcial">Seleccionar cuotas(Parcial)</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="modoTotal" name="modoCalculo" class="custom-control-input" value="total">
              <label class="custom-control-label" for="modoTotal">Pagar TODO</label>
            </div>
          </div>
        </div>
      </div>
   

    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <h5 class="mb-0">Deudas anteriores del cliente (individual y CrediJoya)</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaDeudas">
              <thead class="thead-light">
                <tr class="text-center">
                  <th>Crédito</th>
                  <th>Estado</th>
                  <th style="width:170px">Monto a cancelar</th>
                  <th>Joyas asociadas</th>
                  <th style="width:120px">Cuotas</th>
                </tr>
              </thead>
              <tbody>
                @forelse($deudasPrevias as $d)
                  <tr data-credito="{{ $d->id }}">
                    <td class="align-middle">#{{ $d->id }}</td>
                    <td class="align-middle">{{ $d->estado }}</td>
                    <td class="align-middle" style="max-width:190px;">
                      <input type="text" class="form-control form-control-sm monto-cancelar text-right" value="0.00" readonly>
                      <small class="text-muted d-block">Se llena al seleccionar cuotas.</small>
                    </td>
                    <td class="align-middle">
                      @if($d->joyas && $d->joyas->count())
                        <ul class="mb-0 pl-3">
                          @foreach($d->joyas as $jj)
                            <li>
                              {{ $jj->descripcion }} ({{ $jj->kilataje }}K)
                              – {{ number_format(($jj->peso_neto ?? 0)*($jj->precio_gramo ?? 0),2,'.','') }}
                            </li>
                          @endforeach
                        </ul>
                        <small class="text-muted">Al cancelar, estas joyas se marcan para devolución.</small>
                      @else
                        <span class="text-muted">–</span>
                      @endif
                    </td>
                    <td class="align-middle text-center">
                      <button type="button" class="btn btn-sm btn-outline-info btn-cuotas">Ver cuotas</button>
                    </td>
                  </tr>
                  <!-- FILA DETALLE CUOTAS -->
                  <tr class="fila-cuotas d-none">
                    <td colspan="5">
                      <div class="p-2 border rounded cuotas-wrapper">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="text-muted">Seleccione cuotas a cancelar para este crédito:</div>
                          <div>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-select-all">Marcar todo</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-unselect-all">Quitar todo</button>
                          </div>
                        </div>
                        <div class="table-responsive">
                          <table class="table table-sm table-striped mb-0 tabla-cuotas">
                            <thead>
                              <tr class="text-center">
                                <th style="width:50px">Sel</th>
                                <th>#</th>
                                <th>Vence</th>
                                <th class="text-right">Monto cuota</th>
                                <th class="text-right">Amort.</th>
                                <th class="text-right">Interés prog.</th>
                                <th class="text-right">Interés hoy</th>
                                <th class="text-center">Días mora</th>
                                <th class="text-right">Mora</th>
                                <th class="text-center">Cálculo</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                          </table>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No hay deudas anteriores activas.</td></tr>
                @endforelse
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Resumen de desembolso -->
  <div class="row mt-3 mb-5">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <h5 class="mb-0">Resumen de desembolso</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="text-muted mb-1">Monto aprobado (S/.)</label>
              <input type="number" class="form-control" id="monto_aprobado" value="{{ number_format($prestamo->monto_total,2,'.','') }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
              <label class="text-muted mb-1">Cancelar deudas (S/.)</label>
              <input type="text" class="form-control" id="total_cancelar" value="0.00" readonly>
            </div>
              <input type="hidden" step="0.01" class="form-control" id="gastos" value="0.00">
             
            <div class="col-md-4 mb-3">
              <label class="text-muted mb-1">Neto a entregar (S/.)</label>
              <input type="text" class="form-control font-weight-bold" id="neto_entregar" value="{{ number_format($prestamo->monto_total,2,'.','') }}" readonly>
            </div>
          </div>
          <!-- Accesos rápidos a PDFs -->
          <div class="row mt-3">
            <div class="col-12">
              <div class="card shadow-sm border-0">
                <div class="card-body">
                  <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-danger px-4" onclick="cronogramaindividualPDF()">
                      <i class="fas fa-file-pdf mr-1"></i> Hoja Resumen
                    </button>
                    <button type="button" class="btn btn-outline-primary px-4" onclick="generarcontratoindividualPDF()">
                      <i class="fas fa-file-signature mr-1"></i> Contrato PDF
                    </button>
                    
                  </div>
                  <p class="text-center text-muted mb-0 mt-2">Se abrirán en una nueva pestaña.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-2">
            <button id="btnDesembolsar" class="btn btn-primary">
              <i class="fas fa-money-bill-wave mr-1"></i> Realizar desembolso
            </button>
            <a href="{{ url('admin/caja/pagarcredito') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div> 
<script>
(function(){
  const RUTA_CUOTAS     = "{{ route('credijoya.cuotasPendientes') }}";
  const RUTA_DESEMBOLSAR= "{{ route('credijoya.desembolsar', $prestamo->id) }}";
  const CSRF            = "{{ csrf_token() }}";

  // helpers numéricos
  function num(v){ const n = parseFloat(String(v).replace(',', '.')); return isNaN(n)?0:n; }
  function fmt(n){ return (isNaN(n)?0:Number(n)).toFixed(2); }

  // Recalcula totales del resumen (suma lo que hay en cada "monto-cancelar")
  function recompute(){
    let totalCancelar = 0;
    document.querySelectorAll('#tablaDeudas tbody tr[data-credito]').forEach(tr=>{
      const inp = tr.querySelector('.monto-cancelar');
      if (inp) totalCancelar += num(inp.value);
    });

    const aprobado = num(document.getElementById('monto_aprobado').value);
    const gastos   = num(document.getElementById('gastos').value);

    const gastosEl = document.getElementById('gastos');
    if (totalCancelar + gastos > aprobado) {
      gastosEl.classList.add('is-invalid');
    } else {
      gastosEl.classList.remove('is-invalid');
    }

    document.getElementById('total_cancelar').value = fmt(totalCancelar);
    document.getElementById('neto_entregar').value  = fmt(Math.max(aprobado - totalCancelar - gastos, 0));
  }

  // Actualiza el monto a cancelar del crédito "tr" a partir de las cuotas marcadas en su "filaDetalle"
  function actualizarMontoDeudaDesdeCuotas(tr, filaDetalle){
    let sum = 0;
    filaDetalle.querySelectorAll('tbody tr').forEach(row=>{
      const on = row.querySelector('.sel-cuota')?.checked;
      if (on) sum += num(row.dataset.total);
    });
    const inp = tr.querySelector('.monto-cancelar');
    if (inp) inp.value = fmt(sum);
    recompute();
  }

  // Escucha cambios en "gastos"
  document.getElementById('gastos').addEventListener('input', recompute);
  document.getElementById('gastos').addEventListener('change', recompute);

  // Cambiar modo: cierra detalles, limpia montos y quita "excluido"
  document.querySelectorAll('input[name="modoCalculo"]').forEach(r=>{
    r.addEventListener('change', ()=>{
      document.querySelectorAll('.fila-cuotas').forEach(f=> f.classList.add('d-none'));
      document.querySelectorAll('#tablaDeudas tbody tr[data-credito]').forEach(tr=>{
        tr.querySelector('.monto-cancelar').value = '0.00';
        delete tr.dataset.excluido; // ← importante
      });
      recompute();
    });
  });

  // Lógica de “Ver cuotas” por crédito
  document.querySelectorAll('#tablaDeudas tbody tr[data-credito]').forEach(tr=>{
    const btn = tr.querySelector('.btn-cuotas');
    if (!btn) return;
    const filaDetalle = tr.nextElementSibling;

    // Etiqueta/ayuda visual para la columna "Cálculo"
    function ruleInfo(c, modo){
      const numC  = Number(c.numero);
      const venc  = Number(c.vencida) === 1;
      const dentro= Number(c.inside_periodo || 0) === 1;
      const futuro= Number(c.future_periodo || 0) === 1;
      const dias  = Number(c.dias_hasta_vencer || 0);
      const iProg = Number(c.interes || 0);
      const iHoy  = Number(c.interes_hoy || 0);

      if (venc) {
        return { label:'VENCIDA: cuota + mora', cls:'badge-vencida',
          tip:`Total = Cuota (${Number(c.monto).toFixed(2)}) + Mora (${Number(c.mora).toFixed(2)})` };
      }

      if (modo === 'total') {
        if (numC === 1 || numC === 2) {
          return { label:'#1 y #2 completas', cls:'badge-total',
            tip:`Total = Cuota completa (${Number(c.monto).toFixed(2)})` };
        }
        const esDentro = dentro || (!dentro && !futuro && iHoy>0);
        if (esDentro) {
          const usaHoy = dias > 7;
          return {
            label: usaHoy ? 'TOTAL: amort + interés a hoy' : 'TOTAL: amort + interés prog.',
            cls:'badge-total',
            tip:`Total = Amort (${Number(c.amortizacion).toFixed(2)}) + ${usaHoy?'Int. hoy':'Int. prog.'} (${(usaHoy?iHoy:iProg).toFixed(2)})`
          };
        }
        return { label:'TOTAL: solo amortización', cls:'badge-total',
          tip:`Total = Amortización (${Number(c.amortizacion).toFixed(2)})` };
      }

      // PARCIAL
      const esDentroParcial = dentro || (!dentro && !futuro && iHoy>0);
      if (esDentroParcial) {
        return { label:'PARCIAL: cuota normal', cls:'badge-parcial',
          tip:`Total = Cuota normal (Amort ${Number(c.amortizacion).toFixed(2)} + Int. prog. ${iProg.toFixed(2)})` };
      }
      return { label:'PARCIAL: cuota normal (futuro)', cls:'badge-parcial',
        tip:`Total = Cuota normal (Amort ${Number(c.amortizacion).toFixed(2)} + Int. prog. ${iProg.toFixed(2)})` };
    }

    btn.addEventListener('click', async () => {
      if (!filaDetalle || !filaDetalle.classList.contains('fila-cuotas')) return;

      // toggle abrir/cerrar
      if (!filaDetalle.classList.contains('d-none')) {
        filaDetalle.classList.add('d-none');
        return;
      }

      const creditoId = tr.getAttribute('data-credito');
      const modo = document.querySelector('input[name="modoCalculo"]:checked')?.value || 'parcial';
      const url  = RUTA_CUOTAS + '?credito_id=' + encodeURIComponent(creditoId) + '&modo=' + encodeURIComponent(modo);

      const tbody = filaDetalle.querySelector('.tabla-cuotas tbody');
      const colSpan = filaDetalle.querySelector('.tabla-cuotas thead tr').children.length;
      tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center text-muted">Cargando...</td></tr>`;

      try {
        const res = await fetch(url);
        const js  = await res.json();
        tbody.innerHTML = '';

        if (js.ok && js.cuotas && js.cuotas.length) {
          // Render filas de cuotas
          js.cuotas.forEach(c => {
            const info = ruleInfo(c, modo);
            const label = c.calculo || info.label;
            const tip   = info.tip || c.calculo || '';

            const row = document.createElement('tr');
            row.dataset.cronogramaId = c.cronograma_id;
            row.dataset.total = Number(c.total).toFixed(2);
            row.dataset.mora  = Number(c.mora).toFixed(2);
            row.dataset.dias  = Number(c.dias_mora);

            const isTotal = (modo === 'total');
            const habil   = Number(c.habilitada ?? (isTotal ? 1 : 0)) === 1;
            const motivo  = c.motivo_bloqueo ? ` title="${c.motivo_bloqueo}"` : '';

            const checkedAttr  = isTotal ? 'checked' : '';
            const disabledAttr = isTotal ? 'disabled' : (habil ? '' : 'disabled');

            row.innerHTML = `
              <td class="text-center">
                <input type="checkbox" class="sel-cuota" ${checkedAttr} ${disabledAttr}${motivo}>
              </td>
              <td class="text-center">${c.numero}</td>
              <td class="text-center">${c.fecha}</td>
              <td class="text-right">${Number(c.monto).toFixed(2)}</td>
              <td class="text-right">${Number(c.amortizacion ?? 0).toFixed(2)}</td>
              <td class="text-right">${Number(c.interes ?? 0).toFixed(2)}</td>
              <td class="text-right">${Number(c.interes_hoy ?? 0).toFixed(2)}</td>
              <td class="text-center">${c.dias_mora}</td>
              <td class="text-right">${Number(c.mora).toFixed(2)}</td>
              <td class="text-center">
                <span class="badge-rule ${info.cls || ''}" title="${tip}">${label}</span>
              </td>
              <td class="text-right total-cuota">${Number(c.total).toFixed(2)}</td>
            `;
            tbody.appendChild(row);
          });

          // ==== Secuencial para PARCIAL / Exclusión para TOTAL ====
          const isTotal = (modo === 'total');

          function refreshSequential(){
            if (isTotal) return;
            const chks = Array.from(tbody.querySelectorAll('.sel-cuota'));
            let allowNext = true;
            chks.forEach(chk => {
              if (allowNext) {
                chk.disabled = false;
              } else {
                chk.disabled = true;
                chk.checked  = false;
              }
              if (!chk.checked) allowNext = false;
            });
            actualizarMontoDeudaDesdeCuotas(tr, filaDetalle);
          }

          // botones de selección
          const btnAll   = filaDetalle.querySelector('.btn-select-all');
          const btnNone  = filaDetalle.querySelector('.btn-unselect-all');

          if (isTotal) {
            // TOTAL: marcar y bloquear todo
            tbody.querySelectorAll('.sel-cuota').forEach(chk=>{
              chk.checked = true;
              chk.disabled = true;
            });
            btnAll?.classList.add('d-none');
            btnNone?.classList.add('d-none');

            // NUEVO: Excluir/Incluir crédito completo
            const headerBtns = btnAll?.parentElement || filaDetalle.querySelector('.d-flex .btn-select-all')?.parentElement;
            if (headerBtns){
              // evita duplicados
              headerBtns.querySelector('.btn-excluir-credito')?.remove();
              headerBtns.querySelector('.btn-incluir-credito')?.remove();

              const btnEx = document.createElement('button');
              btnEx.type = 'button';
              btnEx.className = 'btn btn-xs btn-outline-danger btn-excluir-credito ml-1';
              btnEx.textContent = 'Excluir este crédito';

              const btnIn = document.createElement('button');
              btnIn.type = 'button';
              btnIn.className = 'btn btn-xs btn-outline-success btn-incluir-credito ml-1 d-none';
              btnIn.textContent = 'Incluir este crédito';

              headerBtns.appendChild(btnEx);
              headerBtns.appendChild(btnIn);

              const inp = tr.querySelector('.monto-cancelar');
              const setSuma = ()=>{
                const suma = js.cuotas.reduce((s,c)=> s + Number(c.total||0), 0);
                if (inp){ inp.readOnly = true; inp.value = fmt(suma); }
              };
              setSuma();
              recompute();

              btnEx.addEventListener('click', ()=>{
                tr.dataset.excluido = '1';
                tbody.querySelectorAll('.sel-cuota').forEach(chk=>{ chk.checked=false; }); // siguen deshabilitadas
                if (inp){ inp.readOnly = true; inp.value = '0.00'; }
                recompute();
                btnEx.classList.add('d-none');
                btnIn.classList.remove('d-none');
              });

              btnIn.addEventListener('click', ()=>{
                tr.dataset.excluido = '0';
                tbody.querySelectorAll('.sel-cuota').forEach(chk=>{ chk.checked=true; });
                setSuma();
                recompute();
                btnIn.classList.add('d-none');
                btnEx.classList.remove('d-none');
              });
            } else {
              // fallback: al menos setear suma
              const inp = tr.querySelector('.monto-cancelar');
              if (inp) {
                const suma = js.cuotas.reduce((s,c)=> s + Number(c.total||0), 0);
                inp.readOnly = true;
                inp.value = (isNaN(suma)?0:suma).toFixed(2);
                recompute();
              }
            }

          } else {
            // PARCIAL
            btnAll?.classList.remove('d-none');
            btnNone?.classList.remove('d-none');

            // por si quedaron botones de total
            filaDetalle.querySelector('.btn-excluir-credito')?.remove();
            filaDetalle.querySelector('.btn-incluir-credito')?.remove();

            btnAll?.addEventListener('click', ()=>{
              tbody.querySelectorAll('.sel-cuota').forEach(chk => chk.checked = true);
              refreshSequential();
            });

            btnNone?.addEventListener('click', ()=>{
              const chks = Array.from(tbody.querySelectorAll('.sel-cuota'));
              chks.forEach((chk, idx) => {
                chk.checked = false;
                chk.disabled = (idx !== 0); // sólo la primera habilitada
              });
              actualizarMontoDeudaDesdeCuotas(tr, filaDetalle);
            });

            // aplicar cadena al cambiar cada checkbox
            tbody.querySelectorAll('.sel-cuota').forEach(chk=>{
              chk.addEventListener('change', refreshSequential);
            });

            // Estado inicial: ninguna marcada; sólo la primera habilitada
            const chks = Array.from(tbody.querySelectorAll('.sel-cuota'));
            chks.forEach((chk, idx)=>{
              if (idx === 0) chk.disabled = false;
              chk.checked = false;
            });
            refreshSequential();
          }

          // Primera actualización de totales por si acaso
          actualizarMontoDeudaDesdeCuotas(tr, filaDetalle);
        } else {
          tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center text-muted">No hay cuotas pendientes.</td></tr>`;
        }

        filaDetalle.classList.remove('d-none');
      } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-danger text-center">Error al cargar cuotas.</td></tr>`;
      }
    });
  });

  // Acción principal (desembolsar)
  document.getElementById('btnDesembolsar').addEventListener('click', function(){
    const aprobado = num(document.getElementById('monto_aprobado').value);
    const gastos   = num(document.getElementById('gastos').value);
    const deudas   = [];
    const modoEnvio = document.querySelector('input[name="modoCalculo"]:checked')?.value || 'parcial';

    // Recogemos sólo créditos con cuotas seleccionadas (y no excluidos)
    document.querySelectorAll('#tablaDeudas tbody tr[data-credito]').forEach(tr=>{
      if (tr.dataset.excluido === '1') return; // ← ignora créditos excluidos en TOTAL

      const credito_id = Number(tr.getAttribute('data-credito'));
      const next = tr.nextElementSibling;
      const cuotas = [];

      if (next && next.classList.contains('fila-cuotas')) {
        next.querySelectorAll('tbody tr').forEach(row=>{
          const on = row.querySelector('.sel-cuota')?.checked;
          if (!on) return;
          cuotas.push({
            cronograma_id: Number(row.dataset.cronogramaId),
            total:         Number(row.dataset.total),
            mora:          Number(row.dataset.mora),
            dias_mora:     Number(row.dataset.dias)
          });
        });
      }

      if (cuotas.length) deudas.push({ credito_id, cuotas });
    });

    const totalCancelar = deudas.reduce((acc, d)=> acc + d.cuotas.reduce((s,c)=> s + num(c.total), 0), 0);
    if (totalCancelar + gastos > aprobado){
      Swal.fire({icon:'warning', title:'Importes inválidos', text:'(Cancelar deudas + Gastos) supera el monto aprobado.'});
      return;
    }

  /*   if (!deudas.length && num(document.getElementById('total_cancelar').value) === 0) {
      Swal.fire({icon:'info', title:'Sin cuotas seleccionadas', text:'Selecciona al menos una cuota para continuar.'});
      return;
    } */

    Swal.fire({
      title:'Confirmar desembolso',
      text:'Se aplicarán los pagos seleccionados y se registrarán movimientos de caja.',
      icon:'question',
      showCancelButton:true,
      confirmButtonText: 'Sí, desembolsar'
    }).then(async res=>{
      if(!res.isConfirmed) return;

      const data = new FormData();
      data.append('_token', CSRF);
      data.append('gastos', gastos.toFixed(2));
      data.append('deudas', JSON.stringify(deudas));
      data.append('modo', modoEnvio);

      try{
        const resp = await fetch(RUTA_DESEMBOLSAR, { method:'POST', body:data });
        const js   = await resp.json();
        if(!resp.ok || js.error){ throw new Error(js.error || js.message || 'No se pudo completar el desembolso'); }
        Swal.fire({
          icon: 'success',
          title: 'Desembolso realizado',
          html: `
            <div class="text-left">
              <div><b>Total cancelado:</b> S/ ${js.total_cancelar}</div>
              <div><b>Neto a entregar:</b> <span class="text-success">S/ ${js.neto_entregar}</span></div>
              <hr class="my-2">
              <p class="mb-1">Pulsa <b>Ver enlaces</b> para abrir los tickets manualmente.</p>
            </div>
          `,
          confirmButtonText: 'Ver enlaces',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showCancelButton: false,
          preConfirm: () => {
            const base    = "{{ url('/admin/credijoya') }}";
            const urlCaja = "{{ url('admin/caja/pagarcredito') }}";

            // Construir URLs de tickets
            const urls = [];
            if (Array.isArray(js.ingreso_ids) && js.ingreso_ids.length) {
              const ids = js.ingreso_ids.join('-');
              urls.push(`${base}/ticket-pagos/{{ $prestamo->id }}/${js.caja_id}/${ids}`);
            }
            urls.push(`${base}/ticket-desembolso/{{ $prestamo->id }}`);

            return { urls, urlCaja };
          }
        }).then(({ value }) => {
          if (!value) return;

          const { urls, urlCaja } = value;

          // Segundo Swal con los enlaces para que el usuario los abra manualmente
          Swal.fire({
            icon: 'info',
            title: 'Enlaces de tickets',
            html: `
              <p>Haz clic en los enlaces para abrir los tickets:</p>
              <div class="text-left">
                ${urls.map((u,i) => `<div><a href="${u}" target="_blank" rel="noopener">Abrir ticket ${i+1}</a></div>`).join('')}
              </div>
              <hr class="my-2">
              <p>Cuando termines, pulsa <b>Ir a caja</b>.</p>
            `,
            confirmButtonText: 'Ir a caja',
            allowOutsideClick: false,
            allowEscapeKey: false
          }).then(() => {
            window.location.href = urlCaja;
          });
        });
      }catch(e){
        console.error(e);
        Swal.fire({icon:'error', title:'Error', text:e.message});
      }
    });
  });

  window.cronogramaindividualPDF = function() {
    const id = '{{ $prestamo->id }}';
    window.open("{{ url('/generar-cronogramacredijoya') }}/" + id, '_blank');
  };

  window.generarcontratoindividualPDF = function() {
      const id = '{{ $prestamo->id }}';
      window.open("{{ url('/generar-contratocredijoya') }}/" + id, '_blank');
  };

  window.generarpagarePDF = function() {
      const id = '{{ $prestamo->id }}';
      window.open("{{ url('/generar-pagare') }}/" + id, '_blank');
};


  // arranque
  recompute();
})();
</script>

@endsection
