@extends('layouts.admin')

@section('content')
<div class="row evaluacion">
  <h3 class="titulo">EVALUACIÓN CREDIJOYA</h3>
  <h6><b>TIPO DE CRÉDITO:</b> {{ $prestamo->tipo }}</h6>
  <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
  <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
  <h6><b>CLIENTE:</b> {{ $cliente->nombre ?? '—' }} (DNI {{ $cliente->documento_identidad ?? '—' }})</h6>
  <h6><b>RESPONSABLE:</b> {{ $responsable->name ?? '—' }}</h6>
  <h6><b>MONTO APROBADO:</b> S/ {{ number_format($montoAprobado,2) }}</h6>
  <h6><b>CUOTA A EVALUAR:</b> S/ {{ number_format($cuotaEvaluar,2) }}</h6>

  @if ($modulo === 'aprobar')
    <h6><b>COMENTARIO DEL ANALISTA:</b> {{ $comentarioasesor }}</h6>
  @endif

  @if ($estado === 'rechazado')
    <h6><b>MOTIVO DE RECHAZO:</b> {{ $comentarioadministrador }}</h6>
  @elseif ($estado === 'observado')
    <h6><b>MOTIVO DE OBSERVACIÓN:</b> {{ $comentarioadministrador }}</h6>
  @endif
</div>

<div class="row">
  {{-- Resumen del crédito --}}
  <div class="col-md-6">
    <div class="card card-outline card-warning">
      <div class="card-header"><h3 class="card-title">Resumen</h3></div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead><tr><th>Indicador</th><th>Resultado</th></tr></thead>
          <tbody>
            <tr><td>Tasación total</td><td>S/ {{ number_format($tasacionTotal,2) }}</td></tr>
            <tr><td>Máx. 80% referencia</td><td><b>S/ {{ number_format($max80,2) }}</b></td></tr>
            <tr>
              <td>Monto aprobado</td>
              <td class="{{ $montoAprobado > $max80 ? 'text-danger font-weight-bold':'' }}">
                S/ {{ number_format($montoAprobado,2) }}
              </td>
            </tr>
            <tr><td>TEA</td><td>{{ rtrim(rtrim(number_format($tea,2),'0'),'.') }}%</td></tr>
            <tr><td>Estado</td><td><span class="badge badge-{{ $estado==='aprobado'?'success':($estado==='rechazado'?'danger':'secondary') }}">{{ strtoupper($estado) }}</span></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Joyas --}}
  <div class="col-md-6">
    <div class="card card-outline card-warning">
      <div class="card-header"><h3 class="card-title">Joyas en garantía</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th><th>Código</th><th>K</th><th>Precio/g</th>
              <th>Peso bruto</th><th>Peso neto</th><th>Pzas</th><th>Descripción</th><th>Valor tasación</th>
            </tr>
          </thead>
          <tbody>
            @php $suma = 0; @endphp
            @forelse($joyas as $i=>$j)
              @php $suma += (float)$j->valor_tasacion; @endphp
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $j->codigo ?? '—' }}</td>
                <td>{{ $j->kilate }}K</td>
                <td>{{ number_format($j->precio_gramo,2) }}</td>
                <td>{{ $j->peso_bruto !== null ? number_format($j->peso_bruto,2) : '—' }}</td>
                <td>{{ number_format($j->peso_neto,2) }}</td>
                <td>{{ $j->piezas }}</td>
                <td>{{ $j->descripcion }}</td>
                <td>S/ {{ number_format($j->valor_tasacion,2) }}</td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center text-muted">Sin joyas registradas.</td></tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              <th colspan="8" class="text-right">Total tasación</th>
              <th>S/ {{ number_format($suma,2) }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Cronograma --}}
  <div class="col-md-12">
    <div class="card card-outline card-info">
      <div class="card-header"><h3 class="card-title">Cronograma (pre‑desembolso)</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-hover table-bordered">
          <thead>
            <tr><th>#</th><th>Fecha</th><th>Cuota</th><th>Interés</th><th>Amortización</th><th>Saldo deuda</th></tr>
          </thead>
          <tbody>
            @forelse($cronograma as $c)
              <tr>
                <td>{{ $c->numero }}</td>
                <td>{{ \Carbon\Carbon::parse($c->fecha)->format('Y-m-d') }}</td>
                <td>S/ {{ number_format($c->monto,2) }}</td>
                <td>S/ {{ number_format($c->interes,2) }}</td>
                <td>S/ {{ number_format($c->amortizacion,2) }}</td>
                <td>S/ {{ number_format($c->saldo_deuda,2) }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted">Sin cuotas generadas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Footer de acción (analista vs administrador) --}}
@if ($modulo === 'aprobar')
  <div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">
      <div class="form-group">
        <input type="hidden" value="{{ $prestamo->id }}" id="credito_id">
        <label for="comentarioadministrador">Comentario:</label>
        <textarea name="comentarioadministrador" id="comentarioadministrador" class="form-control" rows="3" style="color:black;" required>{{ $comentarioadministrador }}</textarea>
      </div>
      <button type="button" onclick="confirmarAccion('aprobar')"  class="btn btn-primary btnprestamo">Aprobar</button>
      <button type="button" onclick="confirmarAccion('observar')" class="btn btn-warning btnprestamo">Observar</button>
      <button type="button" onclick="confirmarAccion('rechazar')" class="btn btn-danger btnprestamo">Rechazar</button>
      <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
    </div>
  </div>
@else
  <div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">
      <div class="form-group">
        @if($prestamo->estado === 'aprobado' || $prestamo->estado === 'rechazado' || $prestamo->estado === 'observado')
          <label for="comentarioadministrador">Comentario del administrador:</label>
          <textarea name="comentarioadministrador" id="comentarioadministrador" 
                    class="form-control" rows="3" style="color:black;" readonly>
            {{ $comentarioadministrador }}
          </textarea>
        @else
          <p class="text-muted">Sin comentarios del administrador.</p>
        @endif
      </div>
      <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Volver</a>
    </div>
  </div>
@endif

<script>
function imprimirPDF(){
  var prestamoId = '{{ $prestamo->id }}';
  var url = '{{ url('/generar-pdf') }}/' + prestamoId;
  window.open(url, '_blank');
}

function confirmarAccion(accion){
  var comentario   = document.getElementById('comentario')?.value || null;
  var comentarioAdm= document.getElementById('comentarioadministrador')?.value || null;
  var accionTexto  = (accion==='aprobar'?'aprobar':accion==='rechazar'?'rechazar':accion==='observar'?'observar':'guardar');

  if(!confirm('¿Está seguro que desea ' + accionTexto + ' este crédito?')) return;

  // Para CrediJoya no hacemos verificación de ratios: estado pasa como está o según acción
  var estado = (accion==='aprobar') ? 'aprobado' :
               (accion==='rechazar')? 'rechazado' :
               (accion==='observar')? 'observado' : 'revisado';

  enviarSolicitud(accion, comentario, comentarioAdm, estado);
}

function enviarSolicitud(accion, comentario, comentarioadministrador, estado){
  var creditoid = document.getElementById('credito_id').value;
  var data = {
    _token: '{{ csrf_token() }}',
    id: creditoid,
    comentario: comentario,
    comentarioadministrador: comentarioadministrador,
    accion: accion,
    estado: estado
  };

  $.ajax({
    url: '{{ url('/admin/credito') }}/' + accion,
    type: 'GET',
    data: data,
    success: function(resp){
      alert(resp.mensaje || 'Operación realizada.');
      if(resp.redirect){ window.location.href = resp.redirect; }
      else { location.reload(); }
    },
    error: function(xhr){
      console.error(xhr);
      alert('Ocurrió un error al realizar la acción.');
    }
  });
}
</script>
@endsection
