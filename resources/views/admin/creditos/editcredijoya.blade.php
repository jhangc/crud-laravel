@extends('layouts.admin')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Editar CrediJoya (Pre‑registro)</h3>
        <div class="card-tools">
          <span class="badge badge-info">Crédito #{{ $credito->id }}</span>
        </div>
      </div>
      <div class="card-body">
        <form enctype="multipart/form-data" id="credijoyaEditForm" name="credijoyaEditForm">
          @csrf

          <!-- Cabecera / Cliente -->
          <div class="row">
            <div class="col-md-12">
              <div class="form-group mb-2">
                <label for="documento_identidad">Documento de identidad</label>
                <div class="input-group">
                  <input type="text" id="documento_identidad" name="documento_identidad" class="form-control"
                         value="{{ optional($cliente)->documento_identidad }}" placeholder="DNI (8 dígitos)">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="buscarCliente">
                      <i class="fas fa-search"></i> Buscar
                    </button>
                  </div>
                </div>
                <small id="deudaPrevInfo" class="text-muted d-block mt-1"></small>
              </div>
            </div>
          </div>

          <!-- Datos del cliente -->
          <div class="card card-outline card-warning">
            <div class="card-header">
              <h3 class="card-title">Datos del Cliente</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ optional($cliente)->nombre }}">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ optional($cliente)->telefono }}">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="email">Correo</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ optional($cliente)->email }}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" value="{{ optional($cliente)->direccion }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="direccion_laboral">Dirección laboral</label>
                    <input type="text" name="direccion_laboral" id="direccion_laboral" class="form-control" value="{{ optional($cliente)->direccion_laboral }}">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Joyas -->
          <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title mb-0">Joyas en Garantía</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="kilataje">Kilataje</label>
                    <select id="kilataje" class="form-control" onchange="autollenarPrecioOro()">
                      <option value="">Seleccione...</option>
                      <option value="14">14K</option>
                      <option value="16">16K</option>
                      <option value="18">18K</option>
                      <option value="21">21K</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="precio_oro">Precio oro (S/./g)</label>
                    <input type="number" step="0.01" id="precio_oro" class="form-control" placeholder="Auto" readonly>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="peso_bruto">Peso bruto (g)</label>
                    <input type="number" step="0.01" id="peso_bruto" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="peso_neto">Peso neto (g)</label>
                    <input type="number" step="0.01" id="peso_neto" class="form-control" placeholder="Bruto - merma">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="piezas">Piezas</label>
                    <input type="number" id="piezas" class="form-control" value="1" min="1">
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="form-group">
                    <label for="descripcion_joya">Descripción</label>
                    <input type="text" id="descripcion_joya" class="form-control" placeholder="Anillo, cadena...">
                  </div>
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-warning btnprestamo" onclick="agregarJoya()">
                    <i class="fas fa-plus"></i> Añadir joya
                  </button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered" id="tablaJoyas">
                  <thead class="thead-blue">
                    <tr>
                      <th>K</th>
                      <th>Precio/g</th>
                      <th>Peso bruto</th>
                      <th>Peso neto</th>
                      <th>Piezas</th>
                      <th>Descripción</th>
                      <th>Valor tasación (S/.)</th>
                      <th>Código</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="joyas_body"></tbody>
                  <tfoot>
                    <tr>
                      <td colspan="6" class="text-right"><strong>Total tasación:</strong></td>
                      <td><input type="text" id="total_tasacion_footer" class="form-control" value="{{ number_format($tasacion_total,2,'.','') }}" readonly></td>
                      <td colspan="2"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <small class="text-muted">
                Valor tasación ítem = peso neto × precio por gramo.
              </small>
            </div>
          </div>

          <!-- Parámetros del crédito (solo básicos) -->
          <div class="card card-outline card-secondary">
            <div class="card-header">
              <h3 class="card-title">Parámetros del Crédito</h3>
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-md-3">
                  <label class="mb-0">Tasación total</label>
                  <input type="text" id="tasacion_total" class="form-control" value="{{ number_format($tasacion_total,2,'.','') }}" readonly>
                </div>
                <div class="col-md-3">
                  <label class="mb-0">Máx. 80% (referencial)</label>
                  <input type="text" id="monto_max_80" class="form-control" value="{{ number_format($monto_max_80,2,'.','') }}" readonly>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-0">
                    <label for="tasa_tea" class="mb-0">Tasa anual (TEA %)</label>
                    <input type="number" step="0.01" name="tasa_tea" id="tasa_tea" class="form-control param-field" required
                           value="{{ $credito->tasa }}">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-0">
                    <label for="fecha_desembolso" class="mb-0">Fecha desembolso</label>
                    <input type="date" name="fecha_desembolso" id="fecha_desembolso" class="form-control param-field" required
                           value="{{ \Carbon\Carbon::parse($credito->fecha_desembolso)->toDateString() }}">
                  </div>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="monto_aprobado">Monto aprobado (S/.)</label>
                    <input type="number" step="0.01" id="monto_aprobado" name="monto_aprobado"
                           class="form-control param-field" placeholder="≤ 80% tasación" required
                           value="{{ number_format($credito->monto_total,2,'.','') }}">
                    <small class="text-muted">Límite sugerido: 80% de la tasación.</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-0">
                    <label for="proximo_vencimiento" class="mb-0">Próximo vencimiento</label>
                    <input type="text" id="proximo_vencimiento" class="form-control"
                           value="{{ \Carbon\Carbon::parse($credito->fecha_desembolso)->addDays(30)->toDateString() }}" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="row">
            <div class="col-md-12">
              <a href="{{ url('admin/creditos') }}" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy2"></i> Guardar cambios
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<script>
  // ===== Data inicial desde backend =====
  let joyas = @json($joyas);
  function normalizarJoyasBackend(arr){
  if (!Array.isArray(arr)) return [];
  return arr.map(j => {
    // Toma la primera clave disponible entre posibles nombres
    const precio_gramo = num(j.precio_gramo ?? j.precio ?? j.precio_oro);
    const peso_neto    = num(j.peso_neto ?? j.peso ?? j.peso_total);
    const peso_bruto   = num(j.peso_bruto ?? j.peso_bruto_total ?? j.bruto);
    const piezas       = Math.max(parseInt(j.piezas ?? j.cantidad ?? 1), 1);
    const kilataje     = j.kilataje ?? j.k ?? j.karats ?? '';
    const descripcion  = j.descripcion ?? j.detalle ?? j.nombre ?? '';
    const codigo       = j.codigo ?? j.cod ?? null;

    // Valor tasación: peso neto total × precio por gramo (SIN multiplicar por piezas)
    const valor_tasacion = num(j.valor_tasacion) || (peso_neto * precio_gramo);

    return {
      kilataje,
      precio_gramo,
      peso_bruto,
      peso_neto,
      piezas,
      descripcion,
      valor_tasacion: isNaN(valor_tasacion) ? 0 : valor_tasacion,
      id: j.id ?? null,
      codigo
    };
  });
}
// Init
$(function(){
  // Normaliza lo que vino del backend (no altera los inputs ya llenados por Blade)
  joyas = normalizarJoyasBackend(joyas);

  renderJoyas();
  toggleParamState();

  // Solo recalcula si HAY joyas válidas (ver guard en recomputarTotales)
  recomputarTotales();
});
  function toast(type, title, text) {
    return Swal.fire({ toast:true, position:'top-end', icon:type, title:title||'', text:text||'', showConfirmButton:false, timer:2200, timerProgressBar:true });
  }

  function fmt(n){ return (isNaN(n)?0:Number(n)).toFixed(2); }
  function num(v){ const n=parseFloat(v); return isNaN(n)?0:n; }
  function escapeHtml(str){ return String(str).replace(/[&<>"']/g,s=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
  function addDaysToISO(iso,days){ if(!iso) return ''; const d=new Date(iso+'T00:00:00'); d.setDate(d.getDate()+days); return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function toggleParamState(){ const enabled = joyas.length>0; $('.param-field').prop('disabled', !enabled); }

  // ===== Recalculo maestro =====
  function recomputarTotales(){
    // Si no hay joyas, no sobreescribas lo que vino del backend
    if (!Array.isArray(joyas) || joyas.length === 0) {
      toggleParamState();
      return;
    }

    const totalTasacion = joyas.reduce((s,j)=> {
      const vt = j.valor_tasacion ?? (num(j.peso_neto) * num(j.precio_gramo));
      return s + (isNaN(vt) ? 0 : vt);
    }, 0);

    $('#tasacion_total').val(fmt(totalTasacion));
    $('#total_tasacion_footer').val(fmt(totalTasacion));

    const max80 = totalTasacion * 0.80;
    $('#monto_max_80').val(fmt(max80));

    let aprobado = num($('#monto_aprobado').val());
    if(!aprobado){
      aprobado = max80;
      $('#monto_aprobado').val(fmt(max80));
    }
    if(aprobado > max80){
      $('#monto_aprobado').addClass('is-invalid').val(fmt(max80));
    } else {
      $('#monto_aprobado').removeClass('is-invalid');
    }
  }


  $('#monto_aprobado').on('input change', recomputarTotales);

  // ===== Joyas =====
  async function autollenarPrecioOro() {
    const k = $('#kilataje').val();
    if (!k) return;
    try {
      const resp = await $.get(`{{ url('/admin/credijoya/precio-oro') }}`, { kilataje: k });
      if (resp && resp.precio_por_gramo) {
        $('#precio_oro').val(resp.precio_por_gramo);
      } else {
        $('#precio_oro').val('');
        Swal.fire({ icon:'warning', title:'Precio no disponible', text:`No hay precio activo registrado para ${k}K.` });
      }
    } catch (e) {
      $('#precio_oro').val('');
      Swal.fire({ icon:'error', title:'Error', text:'No se pudo obtener el precio del oro.' });
    }
  }

    function agregarJoya(){
      const k = $('#kilataje').val();
      const precio = num($('#precio_oro').val());
      const bruto  = num($('#peso_bruto').val());
      const neto   = num($('#peso_neto').val()); // este es el peso total ya medido
      const piezas = Math.max(parseInt($('#piezas').val()||'1'),1);
      const desc   = ($('#descripcion_joya').val()||'').trim();

      if (!k || !precio || !neto) {
        return Swal.fire({ icon:'warning', title:'Datos incompletos', text:'Kilataje, precio por gramo y peso neto son obligatorios.' });
      }

      const valor = neto * precio; // <<< SIN multiplicar por piezas

      joyas.push({
        kilataje:k, precio_gramo:precio, peso_bruto:bruto, peso_neto:neto,
        piezas, descripcion:desc, valor_tasacion:valor, id:null, codigo:null
      });

      renderJoyas();
      limpiarCamposJoya();
      recomputarTotales();
      toggleParamState();
  }
  function renderJoyas(){
    const tb = $('#joyas_body'); tb.empty();
    let total = 0;
    joyas.forEach((j,i)=>{
      const vt = j.valor_tasacion || (j.peso_neto * j.precio_gramo * (j.piezas||1));
      total += vt;
      tb.append(`
        <tr>
          <td>${j.kilataje}K</td>
          <td>${fmt(j.precio_gramo)}</td>
          <td><input type="number" step="0.01" class="form-control" value="${fmt(j.peso_bruto||0)}" onchange="editarJoya(${i}, 'peso_bruto', this.value)"></td>
          <td><input type="number" step="0.01" class="form-control" value="${fmt(j.peso_neto||0)}" onchange="editarJoya(${i}, 'peso_neto', this.value)"></td>
          <td><input type="number" class="form-control" min="1" value="${j.piezas||1}" onchange="editarJoya(${i}, 'piezas', this.value)"></td>
          <td><input type="text" class="form-control" value="${escapeHtml(j.descripcion||'')}" onchange="editarJoya(${i}, 'descripcion', this.value)"></td>
          <td>${fmt(vt)}</td>
          <td>${j.codigo || '-'}</td>
          <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarJoya(${i})">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
      `);
    });
    $('#total_tasacion_footer').val(fmt(total));
  }

  function editarJoya(i,campo,valor){
  if (!joyas[i]) return;
  if (['peso_bruto','peso_neto','precio_gramo'].includes(campo)) valor = num(valor);
  if (campo === 'piezas') valor = Math.max(parseInt(valor||'1'),1);
  joyas[i][campo] = valor;

  // Recalcula valor_tasacion solo con peso neto total × precio por gramo
  joyas[i].valor_tasacion = num(joyas[i].peso_neto) * num(joyas[i].precio_gramo);

  renderJoyas();
  recomputarTotales();
}
  function eliminarJoya(i){ joyas.splice(i,1); renderJoyas(); recomputarTotales(); toggleParamState(); }
  function limpiarCamposJoya(){ $('#kilataje,#precio_oro,#peso_bruto,#peso_neto,#descripcion_joya').val(''); $('#piezas').val('1'); }

  // ===== Fechas =====
  $('#fecha_desembolso').on('change', function(){ $('#proximo_vencimiento').val(addDaysToISO($(this).val(), 30)); });

  // ===== Buscar cliente =====
  $(document).on('click', '#buscarCliente', function(){
    const $btn = $(this);
    const $dni = $('#documento_identidad');
    const dni  = ($dni.val()||'').trim();

    if (!/^\d{8}$/.test(dni)) {
      toast('warning','DNI inválido','Ingresa un DNI de 8 dígitos.');
      $dni.focus();
      return;
    }

    const originalHtml = $btn.html();
    $btn.prop('disabled',true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Buscando…');

    $.get('/admin/creditos/buscardni', { documento_identidad: dni })
      .done(function(r){
        $('#nombre').val(r.nombre||''); $('#telefono').val(r.telefono||''); $('#email').val(r.email||'');
        $('#direccion').val(r.direccion||''); $('#direccion_laboral').val(r.direccion_laboral||'');
        $('#deudaPrevInfo').text('');
        toast('success','Cliente encontrado','Base de datos.');
      })
      .fail(function(){
        $('#nombre,#telefono,#email,#direccion,#direccion_laboral').val('');
        $('#deudaPrevInfo').text('');
        toast('info','Cliente no registrado','Puedes continuar.');
      })
      .always(function(){ $btn.prop('disabled',false).html(originalHtml); });
  });
  $('#documento_identidad').on('keypress', function(e){ if (e.which===13) $('#buscarCliente').click(); });

  // ===== Submit (UPDATE) =====
  $('#credijoyaEditForm').on('submit', function(e){
    e.preventDefault();
    if (joyas.length===0) return Swal.fire({ icon:'warning', title:'Faltan joyas', text:'Registra al menos una joya.' });

    const formData = new FormData(this);
    formData.append('joyas', JSON.stringify(joyas));
    formData.append('tasacion_total', $('#tasacion_total').val());
    formData.append('monto_max_80',   $('#monto_max_80').val());
    formData.append('proximo_vencimiento', $('#proximo_vencimiento').val());
    //formData.append('_method','PUT');

    $.ajax({
      url: '/admin/credijoya/update/{{ $credito->id }}',
      type: 'POST',
      data: formData, contentType: false, processData: false
    })
    .done(function(){ Swal.fire({icon:'success', title:'Guardado', text:'Cambios actualizados.'}).then(()=> location.href='/admin/creditos'); })
    .fail(function(resp){ console.log(resp); Swal.fire({icon:'error', title:'Error', text:'No se pudo actualizar el crédito.'}); });
  });

  // Init
  $(function(){ renderJoyas(); toggleParamState(); recomputarTotales(); });
</script>
@endsection
