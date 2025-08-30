@extends('layouts.admin')

@section('content')
<div class="row">
<div class="col-md-12">
<div class="card card-outline card-primary mb-3">
  <div class="card-header d-flex justify-content-between">
    <h3 class="card-title mb-0">Precios por Kilate (Historial)</h3>
    <button class="btn btn-primary" id="btnAbrirNuevo"><i class="fas fa-plus"></i> Nuevo precio</button>
  </div>
  <div class="card-body">
    <div class="form-row mb-3">
      <div class="col-md-2">
        <select id="f_kilate" class="form-control">
          <option value="">Todos los kilates</option>
          <option value="14">14K</option>
          <option value="16">16K</option>
          <option value="18">18K</option>
          <option value="21">21K</option>
        </select>
      </div>
      <div class="col-md-3">
        <input type="date" id="f_desde" class="form-control" placeholder="Desde">
      </div>
      <div class="col-md-3">
        <input type="date" id="f_hasta" class="form-control" placeholder="Hasta">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary btn-block" id="btnFiltrar"><i class="fas fa-search"></i> Filtrar</button>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-secondary btn-block" id="btnReset">Reset</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-bordered" id="tabla" style="width:100%">
        <thead class="thead-blue">
          <tr>
            <th>ID</th>
            <th>Kilate</th>
            <th>Precio (S/./g)</th>
            <th>Fecha</th>
            <th>Creado</th>
            <th style="width:110px;">Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
 </div>
</div>
{{-- Modal ÚNICO (Crear / Editar) --}}
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formModal" class="modal-content">
      @csrf
      <input type="hidden" id="formMode" value="create">  {{-- create | edit --}}
      <input type="hidden" id="rowId"> {{-- id para editar --}}

      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Nuevo precio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <label for="kilate">Kilate</label>
          <select id="kilate" class="form-control" required>
            <option value="">Seleccione...</option>
            <option value="14">14K</option>
            <option value="16">16K</option>
            <option value="18">18K</option>
            <option value="21">21K</option>
          </select>
        </div>
        <div class="form-group">
          <label for="precio">Precio (S/./g)</label>
          <input type="number" step="0.01" min="0.01" id="precio" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="fecha">Fecha</label>
          <input type="date" id="fecha" class="form-control" value="{{ now()->toDateString() }}" required>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit" id="btnGuardarModal">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
jQuery(function ($) {
  // —— Verificación Bootstrap 4 Modal ——
  function ensureModalPlugin() {
    if (!$.fn || typeof $.fn.modal !== 'function') {
      console.error('Bootstrap 4 modal() no está disponible. Revisa el orden de scripts (jQuery -> bootstrap.bundle.min.js).');
      Swal.fire({
        icon: 'error',
        title: 'Error de dependencias',
        text: 'Bootstrap 4 (bootstrap.bundle.min.js) no está cargado correctamente. No se puede abrir el modal.',
      });
      return false;
    }
    return true;
  }

  // —— Helpers ——
  function toast(type, title, text) {
    return Swal.fire({toast:true, position:'top-end', icon:type, title, text, showConfirmButton:false, timer:2200, timerProgressBar:true});
  }
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

  // —— DataTable ——
  let tabla = null;
  function initDataTable() {
    tabla = $('#tabla').DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: '{{ route('preciosoro.list') }}',
        data: function(d) {
          d.kilate = $('#f_kilate').val() || '';
          d.desde  = $('#f_desde').val() || '';
          d.hasta  = $('#f_hasta').val() || '';
        }
      },
      order: [[3, 'desc'], [1, 'asc']],
      columns: [
        { data: 'id', name: 'id' },
        { data: 'kilate', render: function(d){ return d + 'K'; } },
        { data: 'precio', render: function(d){ return Number(d).toFixed(2); } },
        { data: 'fecha', name: 'fecha' },
        { data: 'created_at', render: function(d){ return d ? d : ''; } },
        { data: null, orderable: false, searchable: false, render: function(row) {
            return '' +
              '<button class="btn btn-sm btn-warning mr-1" onclick="openEdit('+row.id+','+row.kilate+',\''+row.precio+'\',\''+row.fecha+'\')">' +
                '<i class="fas fa-edit"></i>' +
              '</button>' +
              '<button class="btn btn-sm btn-danger" onclick="delItem('+row.id+')">' +
                '<i class="fas fa-trash"></i>' +
              '</button>';
          }
        }
      ],
      language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      responsive: true,
      lengthChange: true,
      pageLength: 10
    });
  }

  // —— Filtros ——
  $('#btnFiltrar').on('click', function(){ tabla.ajax.reload(); });
  $('#btnReset').on('click', function(){
    $('#f_kilate').val(''); $('#f_desde').val(''); $('#f_hasta').val('');zºz
    tabla.ajax.reload();
  });

  // —— Modal único (Crear/Editar) ——
  function resetModal() {
    $('#formMode').val('create');
    $('#rowId').val('');
    $('#modalTitle').text('Nuevo precio');
    $('#kilate').val('');
    $('#precio').val('');
    $('#fecha').val('{{ now()->toDateString() }}');
    $('#btnGuardarModal').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
  }

  $('#btnAbrirNuevo').on('click', function(){
    resetModal();
    if (!ensureModalPlugin()) return;
    $('#modalForm').modal('show');
  });

  // Exponer funciones globales seguras
  window.openEdit = function(id, kilate, precio, fecha) {
    $('#formMode').val('edit');
    $('#rowId').val(id);
    $('#modalTitle').text('Editar precio');
    $('#kilate').val(String(kilate));
    $('#precio').val(precio);
    $('#fecha').val(fecha);
    if (!ensureModalPlugin()) return;
    $('#modalForm').modal('show');
  };

  // —— Guardar (create/update con el mismo form) ——
  $('#formModal').on('submit', function(e){
    e.preventDefault();
    const mode  = $('#formMode').val();
    const id    = $('#rowId').val();
    const data  = {
      kilate: $('#kilate').val(),
      precio: $('#precio').val(),
      fecha:  $('#fecha').val()
    };

    if (!data.kilate || !data.precio || !data.fecha) {
      return toast('warning','Campos requeridos','Completa todos los campos.');
    }

    $('#btnGuardarModal').prop('disabled', true).text('Guardando...');

    if (mode === 'create') {
      $.post('{{ route('preciosoro.store') }}', data)
        .done(function(){
          toast('success','Guardado','Precio registrado');
          if (ensureModalPlugin()) $('#modalForm').modal('hide');
          tabla.ajax.reload(null, false);
        })
        .fail(function(xhr){
          const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error))
                      ? (xhr.responseJSON.message || xhr.responseJSON.error)
                      : 'No se pudo guardar (verifica duplicados por fecha/kilate).';
          toast('warning','Aviso', msg);
        })
        .always(function(){
          $('#btnGuardarModal').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
        });
    } else {
      $.ajax({
        url: '{{ url('/admin/precios-oro') }}/' + id,
        method: 'POST',
        data: { ...data, _method: 'PUT' }
      })
      .done(function(){
        toast('success','Actualizado','Registro modificado');
        if (ensureModalPlugin()) $('#modalForm').modal('hide');
        tabla.ajax.reload(null, false);
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error))
                      ? (xhr.responseJSON.message || xhr.responseJSON.error)
                      : 'No se pudo actualizar (verifica duplicados por fecha/kilate).';
        toast('warning','Aviso', msg);
      })
      .always(function(){
        $('#btnGuardarModal').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
      });
    }
  });

  // —— Eliminar ——
  window.delItem = function(id) {
    Swal.fire({
      icon: 'warning',
      title: 'Eliminar',
      text: '¿Eliminar este registro?',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(function(res){
      if (!res.isConfirmed) return;
      $.ajax({
        url: '{{ url('/admin/precios-oro') }}/' + id,
        method: 'POST',
        data: { _method: 'DELETE' }
      })
      .done(function(){
        toast('success','Eliminado','Registro eliminado');
        tabla.ajax.reload(null, false);
      })
      .fail(function(xhr){
        toast('error','Error', (xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo eliminar');
      });
    });
  };

  // —— Init ——
  initDataTable();
});
</script>
@endsection
