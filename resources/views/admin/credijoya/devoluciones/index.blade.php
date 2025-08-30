@extends('layouts.admin')

@section('content')
<style>
  :root{ --primary:#155eef; --muted:#667085; --border:#d0d5dd; }
  .card { border:1px solid #e5e7eb; border-radius:14px; padding:1rem; margin-bottom:1rem; }
  .btn { border-radius:10px; padding:.5rem .9rem; border:1px solid var(--border); cursor:pointer }
  .btn-primary{ background:var(--primary); color:#fff; border-color:var(--primary) }
  .btn-light{ background:#fff }
  .pill{ display:inline-block; padding:.15rem .5rem; border:1px solid #eee; border-radius:999px; font-size:.8rem; color:#444; }
  table { width:100%; border-collapse:collapse; margin-top:.5rem }
  th,td{ border-bottom:1px solid #f3f3f3; padding:.5rem; vertical-align:top; font-size:.92rem }
  .rowflex{ display:flex; justify-content:space-between; flex-wrap:wrap; gap:1rem }
  .muted{ color:var(--muted) }
  .danger{ color:#d62c2c }
</style>

<h2>Devolución de Joyas</h2>
<p class="muted">Créditos cancelados con joyas pendientes de entregar. La custodia se cobra desde el día 16, pror-rateada por día sobre la tasación total.</p>

<div id="contenedor"></div>

<script>
const RLIST   = '{{ route('devoluciones.list') }}';
const RCALC   = (id)=> `{{ url('admin/credijoya/devoluciones/calcular-custodia') }}/${id}`;
const RPAGAR  = (id)=> `{{ url('admin/credijoya/devoluciones/pagar-custodia') }}/${id}`;
const RDEV    = (id)=> `{{ url('admin/credijoya/devoluciones/devolver') }}/${id}`;
const CSRF    = '{{ csrf_token() }}';

(async function cargar(){
  const cont = document.getElementById('contenedor');
  cont.innerHTML = 'Cargando...';
  const r = await fetch(RLIST);
  const js = await r.json();
  if(!js.ok){ cont.innerHTML='Error al cargar.'; return; }
  if(!js.data.length){ cont.innerHTML = '<div class="card">No hay joyas pendientes de entregar.</div>'; return; }

  cont.innerHTML = js.data.map(c => `
    <div class="card" data-credito="${c.id}">
      <div class="rowflex">
        <div>
          <div><b>Crédito:</b> #${c.id} <span class="pill">Tasación: S/ ${Number(c.tasacion_total).toFixed(2)}</span></div>
          <div><b>Cliente(s):</b> ${c.clientes||'---'}</div>
          <div class="muted">Cancelado: ${c.fecha_fin||'-'}</div>
        </div>
        <div>
          <button class="btn btn-light" onclick="calc(${c.id}, this)">Ver custodia</button>
          <button class="btn btn-primary" data-btn-devolver disabled onclick="devolver(${c.id}, this)">Devolver seleccionadas</button>
        </div>
      </div>
      <table>
        <thead><tr><th></th><th>Código</th><th>Descripción</th><th>K</th><th>Peso Neto</th><th>Val. Tasación</th></tr></thead>
        <tbody>
          ${c.joyas.map(j=>`
            <tr>
              <td><input type="checkbox" class="chk" value="${j.id}" checked /></td>
              <td>${j.codigo||''}</td>
              <td>${j.descripcion||''}</td>
              <td>${j.kilate||''}</td>
              <td>${j.peso_neto||''}</td>
              <td>S/ ${Number(j.valor_tasacion||0).toFixed(2)}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
      <div class="custodia" style="margin-top:.6rem"></div>
    </div>
  `).join('');
})();

async function calc(creditoId, btn){
  const card = btn.closest('.card');
  const wrap = card.querySelector('.custodia');
  const btnDev = card.querySelector('[data-btn-devolver]');
  wrap.innerHTML = '<span class="muted">Calculando...</span>';

  const r = await fetch(RCALC(creditoId));
  const js = await r.json();
  if(!js.ok){ wrap.textContent='Error.'; return; }

  const detalle = `
    <div class="rowflex" style="align-items:flex-end">
      <div>
        <div><b>Días transcurridos:</b> ${js.dias_transcurridos} (cobra desde el día ${js.desde_dia})</div>
        <div><b>% mensual:</b> ${js.porcentaje_mensual}% &nbsp; <span class="muted">(≈ tasa diaria ${(js.tasa_diaria*100).toFixed(4)}%)</span></div>
        <div><b>Base:</b> S/ ${Number(js.base).toFixed(2)}</div>
        <div><b>Acumulado:</b> <b>S/ ${Number(js.acumulado).toFixed(2)}</b></div>
        <div><b>Pagado:</b> S/ ${Number(js.pagado).toFixed(2)}</div>
        <div><b>Pendiente:</b> <span class="${js.pendiente>0?'danger':''}"><b>S/ ${Number(js.pendiente).toFixed(2)}</b></span></div>
      </div>
      <div>
        <div style="display:flex; gap:.5rem; align-items:center; justify-content:flex-end">
          <input type="number" min="0.01" step="0.01" placeholder="Monto a pagar" value="${Number(js.pendiente).toFixed(2)}" style="padding:.4rem .6rem; border:1px solid #e5e7eb; border-radius:10px; width:160px" data-monto />
          <button class="btn btn-primary" ${js.pendiente<=0.001?'disabled':''} onclick="pagar(${creditoId}, this)">Pagar custodia</button>
        </div>
        <div class="muted" style="margin-top:.3rem">Puedes realizar pagos parciales.</div>
      </div>
    </div>
  `;
  wrap.innerHTML = detalle;

  // Habilita devolución solo si no hay pendiente
  btnDev.disabled = !(js.pendiente <= 0.001);
}

async function pagar(creditoId, btn){
  const card = btn.closest('.card');
  const input = card.querySelector('[data-monto]');
  const monto = parseFloat(input.value||'0');
  if(!(monto>0)){ alert('Ingresa un monto válido.'); return; }

  const fd = new FormData(); fd.append('monto', monto);
  btn.disabled = true;
  const r = await fetch(RPAGAR(creditoId), { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':CSRF} });
  const js = await r.json();
  btn.disabled = false;
  if(!js.ok){ alert(js.error||'Error'); return; }

  window.open(js.ticket_url, '_blank');
  // Recalcular para refrescar pendiente y habilitar devolución si ya quedó en cero
  const calcBtn = card.querySelector('.btn.btn-light');
  calc(creditoId, calcBtn);
}

async function devolver(creditoId, btn){
  const card = btn.closest('.card');
  const ids = [...card.querySelectorAll('.chk:checked')].map(i=>i.value);
  if(!ids.length){ alert('Selecciona al menos una joya.'); return; }
  if(!confirm('Confirmar devolución de las joyas seleccionadas?')) return;

  const fd = new FormData(); ids.forEach(id => fd.append('joya_ids[]', id));
  btn.disabled = true;
  const r = await fetch(RDEV(creditoId), { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':CSRF} });
  const js = await r.json();
  btn.disabled = false;

  if(!js.ok){ alert(js.error||'Error'); return; }
  window.open(js.hoja_url, '_blank');
  card.remove();
}
</script>
@endsection
