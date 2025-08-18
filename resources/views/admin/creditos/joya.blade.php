<div class="row">
  <div class="col-md-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Datos Generales - CrediJoya</h3>
      </div>
      <div class="card-body">
        <form enctype="multipart/form-data" id="credijoyaForm" name="credijoyaForm">
          @csrf
          <input type="hidden" name="tipo_credito" id="tipo_credito" value="credijoya">

          {{-- Cabecera / Cliente --}}
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="documento_identidad">Documento de identidad</label>
                <div class="input-group">
                  <input type="text" id="documento_identidad" name="documento_identidad" class="form-control">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="buscarCliente">
                      <i class="fas fa-search"></i> Buscar
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                Crédito con garantía de joyas. Vencimiento cada 30 días; el cliente puede pagar solo intereses o intereses + amortización.
              </div>
            </div>
          </div>

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

       
          <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title mb-0">Joyas en Garantía</h3>
              <div>
                <button type="button" class="btn btn-sm btn-primary" onclick="agregarJoya()">
                  <i class="fas fa-plus"></i> Añadir joya
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-2">
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
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="precio_oro">Precio oro (S/./g)</label>
                    <input type="number" step="0.01" id="precio_oro" class="form-control" placeholder="Auto">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="peso_bruto">Peso bruto (g)</label>
                    <input type="number" step="0.01" id="peso_bruto" class="form-control">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="peso_neto">Peso neto (g)</label>
                    <input type="number" step="0.01" id="peso_neto" class="form-control" placeholder="Bruto - merma">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="piezas">Piezas</label>
                    <input type="number" id="piezas" class="form-control" value="1" min="1">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="descripcion_joya">Descripción</label>
                    <input type="text" id="descripcion_joya" class="form-control" placeholder="Anillo, cadena...">
                  </div>
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

         
          <div class="card card-outline card-secondary">
            <div class="card-header">
              <h3 class="card-title">Parámetros del Crédito</h3>
            </div>
            <div class="card-body">
              {{-- Resumen de montos --}}
              <div class="row">
                <div class="col-md-3">
                  <label class="mb-0">Tasación total</label>
                  <input type="text" id="tasacion_total" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-3">
                  <label class="mb-0">Máx. 80%</label>
                  <input type="text" id="monto_max_80" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-3">
                  <label class="mb-0">ITF desembolso</label>
                  <input type="text" id="itf_desembolso" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-3">
                  <label class="mb-0">Neto a recibir</label>
                  <input type="text" id="neto_recibir" class="form-control" value="0.00" readonly>
                </div>
              </div>
              <hr>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="tasa_tea">Tasa anual (TEA %)</label>
                    <input type="number" step="0.01" name="tasa_tea" id="tasa_tea" class="form-control param-field" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="fecha_desembolso">Fecha desembolso</label>
                    <input type="date" name="fecha_desembolso" id="fecha_desembolso" class="form-control param-field" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="proximo_vencimiento">Próximo vencimiento</label>
                    <input type="text" id="proximo_vencimiento" class="form-control" value="" readonly>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="deuda_prev_monto">Deuda previa (S/.)</label>
                    <input type="number" step="0.01" name="deuda_prev_monto" id="deuda_prev_monto" class="form-control param-field" value="0">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="deuda_prev_modo">Modo de deuda previa</label>
                    <select id="deuda_prev_modo" name="deuda_prev_modo" class="form-control param-field">
                      <option value="">Sin deuda / No aplica</option>
                      <option value="pagar_con_desembolso">Pagar con desembolso</option>
                      <option value="paralelo">Mantener paralelo</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="monto_aprobado">Monto aprobado (S/.)</label>
                    <input type="number" step="0.01" name="monto_aprobado" id="monto_aprobado" class="form-control param-field" placeholder="≤ 80% tasación" required>
                  </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="button" class="btn btn-outline-primary btn-block param-field" onclick="ponerOchentaPorciento()">
                    Calcular 80%
                  </button>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <small class="text-muted">
                    ITF 0.005% aplica si desembolso &gt; S/ 1000. Neto = Monto aprobado - ITF - (Deuda previa si “pagar con desembolso”).
                  </small>
                </div>
              </div>
            </div>
          </div>

          {{-- Footer --}}
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
  // --- Estado en memoria ---
  let joyas = [];
  const ITF_TASA = 0.00005; // 0.005%

  // --- Utilidades ---
  function fmt(n){ return (isNaN(n)?0:Number(n)).toFixed(2); }
  function num(v){ const n=parseFloat(v); return isNaN(n)?0:n; }
  function escapeHtml(str){
    return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
  }

  function addDaysToISO(isoDate, days){
    if(!isoDate) return '';
    const d = new Date(isoDate+'T00:00:00');
    d.setDate(d.getDate()+days);
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${dd}`;
  }

  // Habilitar/Deshabilitar parámetros hasta que haya joyas
  function toggleParamState(){
    const enabled = joyas.length > 0;
    $('.param-field').prop('disabled', !enabled);
  }

  function recomputarTotales(){
    const totalTasacion = joyas.reduce((s,j)=>s + j.valor_tasacion, 0);
    $('#tasacion_total').val(fmt(totalTasacion));
    const max80 = totalTasacion * 0.80;
    $('#monto_max_80').val(fmt(max80));

    const aprobado = num($('#monto_aprobado').val());
    if (aprobado > max80){ $('#monto_aprobado').addClass('is-invalid'); }
    else { $('#monto_aprobado').removeClass('is-invalid'); }

    const itf = (aprobado > 1000) ? (aprobado * ITF_TASA) : 0;
    $('#itf_desembolso').val(fmt(itf));

    const deuda = num($('#deuda_prev_monto').val());
    const modo  = $('#deuda_prev_modo').val();
    const descDeuda = (modo === 'pagar_con_desembolso') ? deuda : 0;
    const neto = Math.max(aprobado - itf - descDeuda, 0);
    $('#neto_recibir').val(fmt(neto));
  }

  function ponerOchentaPorciento(){
    const max80 = num($('#monto_max_80').val());
    $('#monto_aprobado').val(fmt(max80));
    recomputarTotales();
  }

  // --- Joyas ---
  async function autollenarPrecioOro(){
    const k = $('#kilataje').val();
    if (!k) return;
    try{
      const resp = await $.get(`{{ url('/admin/credijoya/precio-oro') }}`, { kilataje: k });
      if (resp && typeof resp.precio_por_gramo !== 'undefined'){
        $('#precio_oro').val(resp.precio_por_gramo);
      }
    }catch(e){
      console.warn('No se pudo obtener precio del oro', e);
    }
  }

  function agregarJoya(){
    const k = $('#kilataje').val();
    const precio = num($('#precio_oro').val());
    const bruto  = num($('#peso_bruto').val());
    const neto   = num($('#peso_neto').val());
    const piezas = Math.max(parseInt($('#piezas').val() || '1'), 1);
    const desc   = ($('#descripcion_joya').val() || '').trim();

    if (!k || !precio || !neto){
      return Swal.fire({ icon:'warning', title:'Datos incompletos', text:'Kilataje, precio por gramo y peso neto son obligatorios.' });
    }
    const valor = neto * precio * piezas;

    joyas.push({
      kilataje:k,
      precio_gramo:precio,
      peso_bruto:bruto,
      peso_neto:neto,
      piezas,
      descripcion:desc,
      valor_tasacion:valor
    });
    renderJoyas();
    limpiarCamposJoya();
    recomputarTotales();
    toggleParamState();
  }

  function renderJoyas(){
    const tb = $('#joyas_body');
    tb.empty();
    let total = 0;
    joyas.forEach((j,i)=>{
      total += j.valor_tasacion;
      const tr = `
        <tr>
          <td>${j.kilataje}K</td>
          <td>${fmt(j.precio_gramo)}</td>
          <td><input type="number" step="0.01" class="form-control" value="${fmt(j.peso_bruto)}" onchange="editarJoya(${i}, 'peso_bruto', this.value)"></td>
          <td><input type="number" step="0.01" class="form-control" value="${fmt(j.peso_neto)}" onchange="editarJoya(${i}, 'peso_neto', this.value)"></td>
          <td><input type="number" class="form-control" min="1" value="${j.piezas}" onchange="editarJoya(${i}, 'piezas', this.value)"></td>
          <td><input type="text" class="form-control" value="${escapeHtml(j.descripcion)}" onchange="editarJoya(${i}, 'descripcion', this.value)"></td>
          <td>${fmt(j.valor_tasacion)}</td>
          <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarJoya(${i})">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>`;
      tb.append(tr);
    });
    $('#total_tasacion_footer').val(fmt(total));
  }

  function editarJoya(i, campo, valor){
    if (!joyas[i]) return;
    if (['peso_bruto','peso_neto','precio_gramo'].includes(campo)) valor = num(valor);
    if (campo === 'piezas') valor = Math.max(parseInt(valor || '1'), 1);
    joyas[i][campo] = valor;
    joyas[i].valor_tasacion = joyas[i].peso_neto * joyas[i].precio_gramo * joyas[i].piezas;
    renderJoyas();
    recomputarTotales();
  }

  function eliminarJoya(i){
    joyas.splice(i,1);
    renderJoyas();
    recomputarTotales();
    toggleParamState();
  }

  function limpiarCamposJoya(){
    $('#kilataje').val('');
    $('#precio_oro').val('');
    $('#peso_bruto').val('');
    $('#peso_neto').val('');
    $('#piezas').val('1');
    $('#descripcion_joya').val('');
  }

  // Próximo vencimiento (fecha desembolso + 30 días)
  $('#fecha_desembolso').on('change', function(){
    const iso = $(this).val();
    $('#proximo_vencimiento').val(addDaysToISO(iso, 30));
  });

  // Eventos que afectan neto/ITF
  $('#monto_aprobado, #deuda_prev_monto, #deuda_prev_modo').on('input change', recomputarTotales);

  // Buscar cliente por DNI
  $(document).on('click', '#buscarCliente', function(){
    const documentoIdentidad = $('#documento_identidad').val();
    if (!documentoIdentidad) return;
    $.ajax({
      url: '{{ route('creditos.buscardni') }}',
      type: 'GET',
      data: { documento_identidad: documentoIdentidad },
      success: function(r){
        $('#nombre').val(r.nombre || '');
        $('#telefono').val(r.telefono || '');
        $('#email').val(r.email || '');
        $('#direccion').val(r.direccion || '');
        $('#direccion_laboral').val(r.direccion_laboral || '');
      },
      error: function(xhr){
        console.error('Error al recuperar información:', xhr.statusText);
        $('#nombre, #telefono, #email, #direccion, #direccion_laboral').val('');
      }
    });
  });

  // Submit
  $('#credijoyaForm').on('submit', function(e){
    e.preventDefault();

    const max80 = num($('#monto_max_80').val());
    const aprobado = num($('#monto_aprobado').val());
    if (joyas.length === 0){
      return Swal.fire({ icon:'warning', title:'Faltan joyas', text:'Registra al menos una joya en garantía.' });
    }
    if (aprobado > max80){
      return Swal.fire({ icon:'error', title:'Monto inválido', text:'El monto aprobado no puede superar el 80% de la tasación.' });
    }

    const formData = new FormData(this);
    formData.append('joyas', JSON.stringify(joyas));
    formData.append('tasacion_total', $('#tasacion_total').val());
    formData.append('monto_max_80', $('#monto_max_80').val());
    formData.append('itf_desembolso', $('#itf_desembolso').val());
    formData.append('neto_recibir', $('#neto_recibir').val());
    formData.append('proximo_vencimiento', $('#proximo_vencimiento').val());

    $.ajax({
      url: '{{ url('/admin/credijoya/store') }}',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(resp){
        Swal.fire({ icon:'success', title:'¡Éxito!', text:'Crédito CrediJoya registrado.' })
          .then(()=> window.location.href='{{ url('admin/creditos') }}');
      },
      error: function(resp){
        console.log(resp);
        Swal.fire({ icon:'error', title:'Error', text:'No se pudo guardar el crédito.' });
      }
    });
  });

  // Inicial
  $(function(){
    toggleParamState();   // parámetros deshabilitados hasta que haya joyas
    recomputarTotales();  // inicializa totales en 0
  });
</script>

