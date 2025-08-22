<div class="row">
  <div class="col-md-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Datos Generales - CrediJoya (Pre‑registro)</h3>
      </div>
      <div class="card-body">
        <form enctype="multipart/form-data" id="credijoyaForm" name="credijoyaForm">
          @csrf
          <input type="hidden" name="tipo_credito" id="tipo_credito" value="credijoya">

          <!-- Cabecera / Cliente -->
          <div class="row">
            <div class="col-md-12">
              <div class="form-group mb-2">
                <label for="documento_identidad">Documento de identidad</label>
                <div class="input-group">
                  <input type="text" id="documento_identidad" name="documento_identidad" class="form-control" placeholder="DNI (8 dígitos)">
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
                    <input type="text" name="nombre" id="nombre" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="email">Correo</label>
                    <input type="email" name="email" id="email" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="direccion_laboral">Dirección laboral</label>
                    <input type="text" name="direccion_laboral" id="direccion_laboral" class="form-control">
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
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="joyas_body"></tbody>
                  <tfoot>
                    <tr>
                      <td colspan="6" class="text-right"><strong>Total tasación:</strong></td>
                      <td><input type="text" id="total_tasacion_footer" class="form-control" value="0.00" readonly></td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <small class="text-muted">
                Valor tasación ítem = peso neto × precio por gramo × piezas.
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
                  <input type="text" id="tasacion_total" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-3">
                  <label class="mb-0">Máx. 80% (referencial)</label>
                  <input type="text" id="monto_max_80" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-0">
                    <label for="tasa_tea" class="mb-0">Tasa anual (TEA %)</label>
                    <input type="number" step="0.01" name="tasa_tea" id="tasa_tea" class="form-control param-field" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-0">
                    <label for="fecha_desembolso" class="mb-0">Fecha desembolso</label>
                    <input type="date" name="fecha_desembolso" id="fecha_desembolso" class="form-control param-field" required>
                  </div>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="monto_aprobado">Monto aprobado (S/.)</label>
                    <input type="number" step="0.01" id="monto_aprobado" name="monto_aprobado"
                           class="form-control param-field" placeholder="≤ 80% tasación" required>
                    <small class="text-muted">Se propone 80% automáticamente; puedes reducirlo.</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-0">
                    <label for="proximo_vencimiento" class="mb-0">Próximo vencimiento</label>
                    <input type="text" id="proximo_vencimiento" class="form-control" value="" readonly>
                  </div>
                </div>
              </div>

              <!-- (ITF / deuda previa / neto —> se verán luego en el flujo de desembolso) -->
            </div>
          </div>

          <!-- Footer -->
          <div class="row">
            <div class="col-md-12">
              <a href="{{ url('admin/creditos') }}" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy2"></i> Guardar registro
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // ===== Helpers =====
  function toast(type, title, text) {
    return Swal.fire({ toast:true, position:'top-end', icon:type, title:title||'', text:text||'', showConfirmButton:false, timer:2200, timerProgressBar:true });
  }
  let joyas = [];

  function fmt(n){ return (isNaN(n)?0:Number(n)).toFixed(2); }
  function num(v){ const n=parseFloat(v); return isNaN(n)?0:n; }
  function escapeHtml(str){ return String(str).replace(/[&<>"']/g,s=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
  function addDaysToISO(iso,days){ if(!iso) return ''; const d=new Date(iso+'T00:00:00'); d.setDate(d.getDate()+days); return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function toggleParamState(){ const enabled = joyas.length>0; $('.param-field').prop('disabled', !enabled); }

  // ===== Recalculo maestro (sin deuda/ITF/neto) =====
  function recomputarTotales(){
    // Tasación y 80%
    const totalTasacion = joyas.reduce((s,j)=> s + j.valor_tasacion, 0);
    $('#tasacion_total').val(fmt(totalTasacion));
    const max80 = totalTasacion * 0.80;
    $('#monto_max_80').val(fmt(max80));

    // Proponer 80% si está vacío
    let aprobado = num($('#monto_aprobado').val());
    if(!aprobado){
      aprobado = max80;
      $('#monto_aprobado').val(fmt(max80));
    }

    // Validar límite 80%
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
      Swal.fire({ icon:'error', title:'Error', text:'No se pudo obtener el precio del oro. Intente nuevamente.' });
    }
  }

  function agregarJoya(){
    const k = $('#kilataje').val();
    const precio = num($('#precio_oro').val());
    const bruto  = num($('#peso_bruto').val());
    const neto   = num($('#peso_neto').val());
    const piezas = Math.max(parseInt($('#piezas').val()||'1'),1);
    const desc   = ($('#descripcion_joya').val()||'').trim();

    if (!k || !precio || !neto) {
      return Swal.fire({ icon:'warning', title:'Datos incompletos', text:'Kilataje, precio por gramo y peso neto son obligatorios.' });
    }
    const valor = neto * precio * piezas;

    joyas.push({ kilataje:k, precio_gramo:precio, peso_bruto:bruto, peso_neto:neto, piezas, descripcion:desc, valor_tasacion:valor });
    renderJoyas();
    limpiarCamposJoya();
    recomputarTotales();
    toggleParamState();
  }

  function renderJoyas(){
    const tb = $('#joyas_body'); tb.empty();
    let total = 0;
    joyas.forEach((j,i)=>{
      total += j.valor_tasacion;
      tb.append(`
        <tr>
          <td>${j.kilataje}K</td>
          <td>${fmt(j.precio_gramo)}</td>
          <td><input type="number" step="0.01" class="form-control" value="${fmt(j.peso_bruto)}" onchange="editarJoya(${i}, 'peso_bruto', this.value)"></td>
          <td><input type="number" step="0.01" class="form-control" value="${fmt(j.peso_neto)}" onchange="editarJoya(${i}, 'peso_neto', this.value)"></td>
          <td><input type="number" class="form-control" min="1" value="${j.piezas}" onchange="editarJoya(${i}, 'piezas', this.value)"></td>
          <td><input type="text" class="form-control" value="${escapeHtml(j.descripcion)}" onchange="editarJoya(${i}, 'descripcion', this.value)"></td>
          <td>${fmt(j.valor_tasacion)}</td>
          <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarJoya(${i})"><i class="fa fa-trash"></i></button></td>
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
    joyas[i].valor_tasacion = joyas[i].peso_neto * joyas[i].precio_gramo * joyas[i].piezas;
    renderJoyas();
    recomputarTotales();
  }
  function eliminarJoya(i){ joyas.splice(i,1); renderJoyas(); recomputarTotales(); toggleParamState(); }
  function limpiarCamposJoya(){ $('#kilataje,#precio_oro,#peso_bruto,#peso_neto,#descripcion_joya').val(''); $('#piezas').val('1'); }

  // ===== Fechas =====
  $('#fecha_desembolso').on('change', function(){ $('#proximo_vencimiento').val(addDaysToISO($(this).val(), 30)); });

  // ===== Buscar cliente (solo datos; deuda previa se verá en evaluación/desembolso) =====
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
        $('#deudaPrevInfo').text(''); // nada aquí en pre-registro
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

  // ===== Submit =====
  $('#credijoyaForm').on('submit', function(e){
    e.preventDefault();
    if (joyas.length===0) return Swal.fire({ icon:'warning', title:'Faltan joyas', text:'Registra al menos una joya en garantía.' });

    const formData = new FormData(this);
    formData.append('joyas', JSON.stringify(joyas));
    formData.append('tasacion_total', $('#tasacion_total').val());
    formData.append('monto_max_80',   $('#monto_max_80').val()); // referencial
    formData.append('proximo_vencimiento', $('#proximo_vencimiento').val());

    $.ajax({
      url: '/admin/credijoya/store',
      type: 'POST',
      data: formData, contentType: false, processData: false
    })
    .done(function(){ Swal.fire({icon:'success', title:'¡Éxito!', text:'Crédito CrediJoya pre‑registrado.'}).then(()=> location.href='/admin/creditos'); })
    .fail(function(resp){ console.log(resp); Swal.fire({icon:'error', title:'Error', text:'No se pudo guardar el crédito.'}); });
  });

  // Init
  $(function(){ toggleParamState(); recomputarTotales(); });
</script>

