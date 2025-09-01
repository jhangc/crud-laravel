@extends('layouts.admin')

@section('content')
<style>
  :root{
    --primary:#155eef; --primary-50:#eff4ff; --danger:#d62c2c; --muted:#667085; --border:#d0d5dd;
  }
  .segmented { display:grid; grid-template-columns: repeat(4,1fr); gap:.5rem; }
  .segmented .opt { border:1px solid var(--border); border-radius:.8rem; padding:.65rem .8rem; text-align:center; cursor:pointer; user-select:none; transition:.15s; }
  .segmented .opt:hover{ background:#fafafa }
  .segmented .opt.active { border-color:var(--primary); box-shadow:0 0 0 3px rgba(21,94,239,.15); color:var(--primary); font-weight:600; background:var(--primary-50); }
  .opt small { display:block; margin-top:.25rem; font-size:.75rem; color:var(--muted); }
  .pill-badge { font-size:.72rem; padding:.15rem .4rem; border-radius:999px; background:var(--primary-50); color:var(--primary); margin-left:.25rem; }
  .pill-badge.warn { background:#fff1f1; color:var(--danger); }
  .kpi .label{font-size:.72rem;color:#98a2b3}
  .kpi .value{font-weight:600}
  .muted{color:var(--muted)}
  .spin{display:inline-block;width:1rem;height:1rem;border:2px solid #fff;border-right-color:transparent;border-radius:50%;animation:sp .7s linear infinite;margin-right:.5rem;transform:translateY(2px)}
  @keyframes sp{to{transform:rotate(360deg)}}
  @media (max-width:768px){ .segmented{grid-template-columns:1fr 1fr} }
</style>

<h3 class="mb-3">Pago de Crédito — CrediJoya</h3>

{{-- HEADER --}}
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-3 align-items-center">
      <div class="col-md-4">
        <div class="label text-muted small">Cliente</div>
        @foreach($credito->clientes as $cliente)
          <div class="value fw-semibold">{{ $cliente->nombre }}</div>
        @endforeach
      </div>
      <div class="col-md-3">
        <div class="label text-muted small">Crédito #</div>
        <div class="value fw-semibold">{{ $credito->id }}</div>
      </div>
      <div class="col-md-3">
        <div class="label text-muted small">Monto aprobado</div>
        <div class="value fw-semibold">S/ {{ number_format((float)$credito->monto_total,2) }}</div>
      </div>
      <div class="col-md-2">
        <div class="label text-muted small">Estado</div>
        <span class="badge bg-{{ $credito->estado === 'terminado' ? 'success' : 'secondary' }}">{{ strtoupper($credito->estado ?? '---') }}</span>
      </div>
      <div class="col-md-2">
        <div class="label text-muted small">TEA vigente</div>
        <div class="value fw-semibold">{{ number_format((float)($credito->tasa ?? 0),2,'.','') }}%</div>
      </div>
    </div>
  </div>
</div>

{{-- JOYAS --}}
<div class="card shadow-sm mb-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr><th>#</th><th>Descripción</th><th class="text-end">Peso neto (g)</th><th>Kilate</th><th>Estado</th></tr>
        </thead>
        <tbody>
          @forelse($joyas as $i => $j)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $j->descripcion }}</td>
              <td class="text-end">{{ number_format((float)$j->peso_neto,2) }}</td>
              <td>{{ $j->kilate }}</td>
              <td>
                @if((int)$j->devuelta === 0)
                  <span class="badge bg-warning text-dark">En custodia</span>
                @else
                  
                  <span class="badge bg-success">Liberada</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">Sin joyas registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="small text-muted p-2">* Al cancelar la totalidad del crédito se liberan las joyas.</div>
  </div>
</div>

{{-- CUOTA --}}
<div class="card shadow-sm mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <b>Cuota vigente</b>
    @if($cuotaVigente)
      @php
        $hoy = \Carbon\Carbon::now();
        $vto = \Carbon\Carbon::parse($cuotaVigente->fecha);
        $esVencida = $hoy->gt($vto);
        $diasHastaVencer = $esVencida ? 0 : $hoy->diffInDays($vto);
        $esMismoDia = \Carbon\Carbon::parse($credito->fecha_desembolso)->isSameDay($hoy);
      @endphp
      <span class="badge bg-{{ $esVencida ? 'danger' : 'info' }}">#{{ $cuotaVigente->numero }} — {{ $esVencida ? 'VENCIDA' : 'EN CURSO' }}</span>
    @endif
  </div>
  <div class="card-body">
    @if(!$cuotaVigente)
      <div class="alert alert-success mb-0">No hay cuotas pendientes para este crédito.</div>
    @else
      @php
        $cuotaProg = round((float)$cuotaVigente->monto, 2);
        $amort     = round((float)$cuotaVigente->amortizacion,2);
        $intProg   = round((float)$cuotaVigente->interes,2);
        $moraCalc  = round((float)$moraHoy, 2);
        $intHoy    = round((float)$interesHoy, 2);
      @endphp

      <div class="row g-3 kpi text-center text-md-start">
        <div class="col-6 col-md-2"><div class="label">Vence</div><div class="value">{{ $vto->format('d/m/Y') }}</div></div>
        <div class="col-6 col-md-2"><div class="label">Capital</div><div class="value text-primary">S/ {{ number_format($amort,2) }}</div></div>
        <div class="col-6 col-md-2"><div class="label">Int. prog.</div><div class="value">S/ {{ number_format($intProg,2) }}</div></div>
        <div class="col-6 col-md-2"><div class="label">Int. a hoy</div><div class="value">S/ {{ number_format($intHoy,2) }}</div></div>
        <div class="col-6 col-md-2"><div class="label">Mora</div><div class="value text-danger">S/ {{ number_format($moraCalc,2) }}</div></div>
        <div class="col-6 col-md-2"><div class="label">Cuota (prog.)</div><div class="value">S/ {{ number_format($cuotaProg,2) }}</div></div>
      </div>

      @if($esMismoDia)
        <div class="alert alert-info mt-3 mb-0">
          <b>Mismo día de desembolso:</b> se cobra <b>solo capital</b> (interés ≈ 0, sin mora). <span class="pill-badge">Libera joyas</span>
        </div>
      @endif
    @endif
  </div>
</div>

{{-- FORM (SIN action/method, todo via AJAX) --}}
<form id="formPago"
      class="card shadow-sm mb-4"
      data-url="{{ route('pagocredijoya.store', $credito->id) }}"
      novalidate>
     @csrf
  <div class="card-body">
    @if($cuotaVigente)

      {{-- MODOS --}}
      @if(!$esMismoDia)
        <div class="mb-3">
          <div class="fw-semibold mb-2">¿Qué quieres pagar hoy?</div>
          <div class="segmented" id="segmented" role="tablist" aria-label="Modo de pago">
            <div class="opt active" data-modo="interes" role="tab" aria-selected="true" tabindex="0">
              Interés
              <small>Interés + mora <span class="pill-badge warn">Renueva 1 mes</span></small>
            </div>
            <div class="opt" data-modo="cuota" role="tab" aria-selected="false" tabindex="0">
              Cuota
              <small>Capital + int. prog. <span class="pill-badge">Libera joyas</span></small>
            </div>
            <div class="opt" data-modo="totalhoy" role="tab" aria-selected="false" tabindex="0">
              Total hoy
              <small>Capital + interés hoy <span class="pill-badge">Libera joyas</span></small>
            </div>
            <div class="opt" data-modo="adelanto" role="tab" aria-selected="false" tabindex="0">
              Interés + adelanto
              <small>Interés + tu adelanto <span class="pill-badge warn">Renueva 1 mes</span></small>
            </div>
          </div>
          <div class="form-text mt-2" id="ayudaModo">Pone al día (no amortiza capital).</div>
        </div>
      @endif

      {{-- Campos --}}
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label"><b>Monto a pagar</b></label>
          <input
            type="text"
            inputmode="decimal"
            autocomplete="off"
            name="monto_pago_visible"
            id="monto_pago"
            class="form-control"
            value="{{ $esMismoDia ? number_format($amort,2,',','.') : '' }}"
            {{ $esMismoDia ? 'readonly' : '' }}
            aria-describedby="ayudaMonto"
            required>
          <div class="form-text" id="ayudaMonto">Se calcula automáticamente.</div>
        </div>

        <div class="col-md-4" id="boxAdelanto" style="display:none;">
          <label class="form-label"><b>Adelanto de capital (opcional)</b></label>
          <input type="text" inputmode="decimal" id="adelanto_capital" class="form-control" value="0,00" autocomplete="off">
          <div class="form-text">Suma al mínimo (interés + mora).</div>
        </div>
        <div class="col-md-4" id="boxTea" style="display:none;">
          <label class="form-label"><b>Nueva tasa TEA (opcional)</b></label>
        
            <input
              type="number"
              inputmode="decimal"
              autocomplete="off"
              id="nueva_tasa_tea"
              class="form-control"
              placeholder="p.ej. 95.00">
        
       
          <div class="form-text">
           Para la  renovacion del crédito.
          </div>
        </div>

        {{-- Hidden: modo, tipo y monto real --}}
        <input type="hidden" name="modo_pago"  id="modo_pago"        value="{{ $esMismoDia ? 'totalhoy' : 'interes' }}">
        <input type="hidden" name="tipo_pago"  id="tipo_pago_hidden" value="{{ $esMismoDia ? 'total'    : 'interes' }}">
        <input type="hidden" name="monto_pago" id="monto_pago_hidden" value="">
      </div>
    @else
      <div class="alert alert-info mb-0">Este crédito no tiene cuotas pendientes para cobrar.</div>
    @endif
  </div>

  <div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <div id="msg" class="text-danger small" role="alert" aria-live="polite"></div>
    <button class="btn btn-primary" id="btnPagar" {{ !$cuotaVigente ? 'disabled' : '' }}>
      Registrar pago
    </button>
  </div>
</form>
<script>
/* ============================================================
 * CrediJoya • Pago (frontend) — Monto visible EN PUNTO (1234.56)
 * ============================================================ */
(function ($) {
  'use strict';

  /* ----------------------- CONFIG / STATE ----------------------- */
  const $form       = $('#formPago');
  const URL_PAGO    = $form.data('url');
  const CSRF        = $form.find('input[name=_token]').val() || '{{ csrf_token() }}';
  const $teaIn      = $('#nueva_tasa_tea');   // ya lo tienes
  const $teaBox     = $('#boxTea'); 

  const hayCuota    = {{ $cuotaVigente ? 'true' : 'false' }};
  const esMismoDia  = {{ isset($esMismoDia) && $esMismoDia ? 'true' : 'false' }};
  if (!hayCuota) return;

  const CUOTA = {
    cuotaProg : {{ isset($cuotaVigente) ? number_format($cuotaProg,2,'.','') : '0' }},
    amort     : {{ isset($cuotaVigente) ? number_format($amort,2,'.','')     : '0' }},
    intProg   : {{ isset($cuotaVigente) ? number_format($intProg,2,'.','')   : '0' }},
    intHoy    : {{ isset($cuotaVigente) ? number_format($intHoy,2,'.','')    : '0' }},
    mora      : {{ isset($cuotaVigente) ? number_format($moraCalc,2,'.','')  : '0' }},
    vencida   : {{ isset($cuotaVigente) && $esVencida ? 'true' : 'false' }},
    diasRest  : {{ isset($cuotaVigente) ? (int)$diasHastaVencer : 0 }},
  };

  // DOM
  const $seg        = $('#segmented');
  const $ayudaModo  = $('#ayudaModo');
  const $ayudaMonto = $('#ayudaMonto');
  const $montoVis   = $('#monto_pago');          // visible (solo 1234.56)
  const $montoReal  = $('#monto_pago_hidden');   // hidden normalizado
  const $tipoHidden = $('#tipo_pago_hidden');
  const $modoHidden = $('#modo_pago');
  const $adelBox    = $('#boxAdelanto');
  const $adelIn     = $('#adelanto_capital');
  const $btn        = $('#btnPagar');
  const $msg        = $('#msg');

  function setMsg(text){ $msg.text(text || ''); }


  /* ---------------------------- HELPERS ---------------------------- */
  // Normaliza SIEMPRE a "####.##" (sin miles). Acepta entradas con coma, las convierte a punto.
  function parseDotMoney(input){
    let s = String(input ?? '').trim();
    s = s.replace(/,/g, '.');              // si ponen coma, la tratamos como punto
    s = s.replace(/[^0-9.]/g, '');         // sólo dígitos y punto
    // deja sólo el primer punto decimal
    let firstDot = s.indexOf('.');
    if (firstDot !== -1) {
      const head = s.slice(0, firstDot + 1);
      const tail = s.slice(firstDot + 1).replace(/\./g, '');
      s = head + tail;
    }
    const n = parseFloat(s);
    return isNaN(n) ? 0 : n;
  }
  const toMoney = (n) => (Math.round(Number(n||0)*100)/100).toFixed(2); // "1234.56"

  function interesReglaHoy(){
    if (CUOTA.vencida === true) return Number(CUOTA.intProg);
    return (CUOTA.diasRest > 7) ? Number(CUOTA.intHoy) : Number(CUOTA.intProg);
  }
  function minimoInter(){
    const i = (CUOTA.vencida === true) ? Number(CUOTA.intProg) : Number(CUOTA.intHoy);
    return i + Number(CUOTA.mora);
  }
  function basePorModo(m){
    if (m === 'interes' || m === 'adelanto') return minimoInter();
    if (m === 'cuota')     return Number(CUOTA.cuotaProg) + (CUOTA.vencida===true ? Number(CUOTA.mora) : 0);
    return (CUOTA.vencida===true)
      ? (Number(CUOTA.cuotaProg) + Number(CUOTA.mora))
      : (Number(CUOTA.amort) + Number(interesReglaHoy()));
  }

  /* ---------------------------- UI ACTIONS ---------------------------- */
  function lockMonto(val, lock){
    $montoVis.val(toMoney(val));                 // <<< visible SIEMPRE 1234.56
    if (lock) $montoVis.attr('readonly','readonly'); else $montoVis.removeAttr('readonly');
  }
  function setAyuda(a,b){ $ayudaModo.text(a||''); $ayudaMonto.text(b||''); }
  function activarOpcion(modo){
    $seg.find('.opt').each(function(){
      const $o = $(this); const act = $o.data('modo')===modo;
      $o.toggleClass('active', act).attr('aria-selected', act ? 'true':'false');
    });
  }
  function setModo(modo){
     function hideTea(){
      $teaBox.hide();
      $teaIn.val('');
    }
    activarOpcion(modo);
    $modoHidden.val(modo);

    if (modo==='interes'){
      $tipoHidden.val('interes'); $adelBox.hide();
      lockMonto(basePorModo('interes'), true);
      setAyuda('Paga interés + mora. Renueva 1 mes.', 'Monto bloqueado al mínimo.');
      setMsg('El pago mínimo es S/ ' + toMoney(minimoInter()));
       $teaBox.show(); 
    } else if (modo==='cuota'){
      $tipoHidden.val('total'); $adelBox.hide();
       hideTea(); 
      lockMonto(basePorModo('cuota'), true);
      setAyuda(
        (CUOTA.vencida===true)
          ? 'Cuota (capital + int. prog.) + mora por estar vencida. Libera joyas.'
          : 'Cuota del periodo (capital + interés programado). Libera joyas.',
        'Monto bloqueado.'
      );
      setMsg('');
    } else if (modo==='totalhoy'){
      $tipoHidden.val('total'); $adelBox.hide();
       hideTea(); 
      lockMonto(basePorModo('totalhoy'), true);
      setAyuda(
        (CUOTA.vencida===true)
          ? 'Cuota vencida: cuota (cap+int prog) + mora. Libera joyas.'
          : 'En curso: capital + interés (regla de 7 días). Libera joyas.',
        'Monto bloqueado.'
      );
      setMsg('');
    } else if (modo==='adelanto'){
      $tipoHidden.val('parcial'); $adelBox.show(); 
      $teaBox.show(); 
      lockMonto(basePorModo('adelanto'), false);
      setAyuda('Interés + mora + tu adelanto de capital (renueva 1 mes).', 'Puedes editar por encima del mínimo.');
      setMsg('El pago mínimo es S/ ' + toMoney(minimoInter()));
    }
  }

  /* ----------------------------- EVENTS ----------------------------- */
  function bindEvents(){
    $seg.on('click', '.opt', function(){ setModo($(this).data('modo')); })
        .on('keydown', '.opt', function(e){
          if (e.key==='Enter'||e.key===' '){ e.preventDefault(); setModo($(this).data('modo')); }
        });

    // Sanitiza mientras escriben (deja solo 1234.56)
    $montoVis.on('input', function(){
      const n = parseDotMoney($(this).val());
      $(this).val(toMoney(n));
    });
    $adelIn.on('input', function(){
      if ($adelBox.is(':visible')){
        const base = minimoInter();
        const adel = Math.max(parseDotMoney($(this).val()), 0);
        lockMonto(base + adel, false);
      }
    });
    $form.on('submit', async function (ev) {
      ev.preventDefault();
      setMsg('');
      $btn.prop('disabled', true).html('<span class="spin"></span>Registrando...');

      // ✅ 1) calcular una sola vez y en formato punto
      const montoNum = parseDotMoney($montoVis.val());
      if (!(montoNum > 0)) {
        setMsg('Ingresa un monto válido.');
        $btn.prop('disabled', false).text('Registrar pago');
        return;
      }

      // ✅ 2) setear el hidden ya normalizado ("####.##")
      $montoReal.val(toMoney(montoNum)); // ej: "1257.54"

      // (debug útil)
      console.log('[CJ] visible=', $montoVis.val(), 'hidden=', $montoReal.val(),
                  'modo=', $modoHidden.val(), 'tipo=', $tipoHidden.val());

      const ticketWin = window.open('about:blank', '_blank');
      const REDIR_URL = "{{ url('admin/caja/cobrar') }}"; // destino después de abrir el ticket
      try {
        const fd = new FormData($form[0]);
        const teaNum = parseDotMoney($teaIn.val());
        if ($teaBox.is(':visible')) {
            if (!isNaN(teaNum) && teaNum > 0) {
              fd.set('nueva_tasa_tea', $teaIn.val()); // "95.00"
            } else {
              fd.delete('nueva_tasa_tea');
            }
          } else {
            fd.delete('nueva_tasa_tea');
          }
        // ✅ 3) forzar claves y quitar el visible
        fd.set('monto_pago', $montoReal.val());
        fd.set('modo_pago',  $modoHidden.val());
        fd.set('tipo_pago',  $tipoHidden.val());
        if (fd.has('monto_pago_visible')) fd.delete('monto_pago_visible');
        if (!fd.has('_token')) fd.append('_token', CSRF);

        // (debug: ver realmente qué se manda)
        for (const [k, v] of fd.entries()) { console.log('[FD]', k, v); }

        const js = await $.ajax({
          url: URL_PAGO,
          method: 'POST',
          data: fd,
          processData: false,
          contentType: false,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
          },
          dataType: 'json'
        });

        if (!js || js.ok !== true || !js.ticket_url) {
          ticketWin?.close();
          setMsg((js && (js.error || js.message)) || 'No se pudo registrar el pago.');
          $btn.prop('disabled', false).text('Registrar pago');
          return;
        }

        // ✅ Abrir ticket con fallback si el popup fue bloqueado
        if (!ticketWin || ticketWin.closed) {
          window.open(js.ticket_url, '_blank');
        } else {
          ticketWin.location = js.ticket_url;
        }

        // ✅ Redirigir a caja/cobrar
        window.location.assign(REDIR_URL);
      } catch (err) {
        ticketWin?.close();
        const errs = err?.responseJSON?.errors
          ? Object.values(err.responseJSON.errors).flat().join('\n')
          : null;
        const apiMsg = errs
          || err?.responseJSON?.error
          || err?.responseJSON?.message
          || (err?.responseText && String(err.responseText).slice(0,180))
          || String(err);
        setMsg('Error de red: ' + apiMsg);
        $btn.prop('disabled', false).text('Registrar pago');
      }
    });

  }

  /* ------------------------------ INIT ------------------------------ */
  function init(){
  if (esMismoDia){
    setModo('totalhoy'); 
    $tipoHidden.val('total');
    lockMonto(Number(CUOTA.amort), true); // sólo capital
    $teaBox.hide(); $teaIn.val('');       // <<< asegurar oculto
  } else {
    setModo('interes');
  }
  bindEvents();
}

  $(init); // GO
})(window.jQuery);
</script>



@endsection
