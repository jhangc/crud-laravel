@extends('layouts.admin')

@section('content')
<div class="row ">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar Credito Grupal</h3>
            </div>
            <div class="card-body">
                <form enctype="multipart/form-data" id="prestamoForm" name="prestamoForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_credito">Tipos de créditos</label>
                                <input id="credito-id" name="credito-id" value="{{$id}}" type="hidden">
                                <input  type="hidden" value="comercio" name="tipo_credito" id="tipo_credito">
                                <input type="hidden" name="tipo_producto" id="tipo_producto" value="grupal">
                                <select name="tipo_credito" id="tipo_credito" class="form-control" required
                                    onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="comercio" {{ old('tipo_credito') == 'comercio' ? 'selected' : '' }}>
                                        Comercio</option>
                                    <option value="servicio" {{ old('tipo_credito') == 'servicio' ? 'selected' : '' }}>
                                        Servicio</option>
                                    <option value="produccion"
                                        {{ old('tipo_credito') == 'produccion' ? 'selected' : '' }}>Producción</option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_producto">Productos</label>
                                <select name="tipo_producto" id="tipo_producto" class="form-control" required
                                    onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="microempresa"
                                        {{ old('tipo_producto') == 'microempresa' ? 'selected' : '' }}>Microempresa
                                    </option>
                                    <option value="agricola" {{ old('tipo_producto') == 'agricola' ? 'selected' : '' }}>
                                        Agrícola</option>
                                    <option value="consumo" {{ old('tipo_producto') == 'consumo' ? 'selected' : '' }}>
                                        Consumo</option>
                                   
                                </select>
                            </div>
                        </div> -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="subproducto">SubProductos</label>
                                <select name="subproducto" id="subproducto" class="form-control" required
                                    onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="credimujerpalmo"
                                        {{ old('subproducto') == 'credimujerpalmo' ? 'selected' : '' }}>Credimujerpalmo
                                    </option>
                                
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="destino_credito">Destino de crédito</label>
                                <select name="destino_credito" id="destino_credito" class="form-control" required
                                    onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="activo fijo"
                                        {{ old('destino_credito') == 'activo fijo' ? 'selected' : '' }}>Activo fijo
                                    </option>
                                    <option value="capital de trabajo"
                                        {{ old('destino_credito') == 'capital de trabajo' ? 'selected' : '' }}>Capital
                                        de trabajo</option>
                                    <option value="consumo"
                                        {{ old('destino_credito') == 'consumo' ? 'selected' : '' }}>Consumo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Grupo</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre_prestamo">Nombre del Grupo</label>
                                        <input type="text" value="{{ old('nombre_prestamo') }}"
                                            name="nombre_prestamo" id="nombre_prestamo" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cantidad_grupo">Cantidad de integrantes</label>
                                        <input type="number" value="{{ old('cantidad_grupo') }}" name="cantidad_grupo"
                                            id="cantidad_grupo" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="foto_grupal">Foto Grupal</label>
                                        <input type="file" name="foto_grupal" accept="image/*"
                                            class="form-control-file">
                                        @error('foto_grupal')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" id="dnic" name="dnic"
                                            placeholder="Agregar por Dni" class="form-control">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button"
                                                id="buscarClientec">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Nombre del cliente</th>
                                        <th>DNI</th>
                                        <th>Profesión</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCuerpo">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                            <div class="text" style="background-color: #f0f0f0; padding: 10px;">
                                <strong>Total Monto:</strong> <span id="totalMonto"
                                    style="font-size: 18px;">0.00</span>
                            </div>
                        </div>
                    </div>

                   

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Crédito</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="descripcion_negocio">Descripción del negocio</label>
                                        <!-- <input type="text" value="{{ old('descripcion_negocio') }}" name="descripcion_negocio" id="descripcion_negocio" class="form-control"> -->
                                        <select name="descripcion_negocio" id="descripcion_negocio"
                                            onchange="agricola()" class="form-control">
                                            <option value="" selected>Seleccione una descripción...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="recurencia_grupal">
                                        <label for="recurrencia">Recurrencia</label>
                                        <select name="recurrencia1" id="recurrencia1" class="form-control">
                                            <option value="">Seleccione una opción...</option>
                                            <option value="catorcenal"
                                                {{ old('recurrencia') == 'mensual' ? 'selected' : '' }}> Catorcenal
                                            </option>
                                            <option value="veinteochenal"
                                                {{ old('recurrencia') == 'quincenal' ? 'selected' : '' }}>Veinteochenal
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tasa_interes">Tasa de interés anual (%)</label>
                                        <input type="text" value="{{ old('tasa_interes') }}" name="tasa_interes"
                                            id="tasa_interes" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tiempo_credito">Tiempo del crédito</label>
                                        <input type="text" value="{{ old('tiempo_credito') }}"
                                            name="tiempo_credito" id="tiempo_credito" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_desembolso">Fecha de desembolso</label>
                                        <input type="date" value="{{ old('fecha_desembolso') }}"
                                            name="fecha_desembolso" id="fecha_desembolso" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periodo_gracia_dias">Periodo de Gracia (días)</label>
                                        <input type="number" value="{{ old('periodo_gracia_dias') }}"
                                            name="periodo_gracia_dias" id="periodo_gracia_dias" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="monto">Monto total (S/.)</label>
                                        <input type="text" value="{{ old('monto') }}" name="monto"
                                            id="monto" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del la Garantia</h3>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="descripcion_garantia">Descripcion de la Garantia</label>
                                        <input type="text" name="descripcion_garantia" id="descripcion_garantia"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="valor_mercado">Valor (S/.)</label>
                                        <input type="number" name="valor_mercado" id="valor_mercado"
                                            class="form-control">
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="archivo_garantia">Archivo en pdf</label>
                                        <input type="file" name="archivo_garantia" accept=".pdf"
                                            class="form-control-file">
                                        @error('archivo_garantia')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>


                            </div>


                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ url('admin/creditos') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Guardar
                                registro</button>
                        </div>
                    </div>
                    <hr>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
    const idc = document.getElementById('credito-id').value;
   function cargardata() {
        $.ajax({
            url: `/admin/creditoinfo/${idc}`,
            type: 'GET',
            success: function(response) {
                console.log(response);
                let credito = response.credito;

                $('#tipo_credito').val(credito.tipo);
                $('#tipo_producto').val(credito.producto);
                $('#subproducto').val(credito.subproducto);
                $('#destino_credito').val(credito.destino);
                $('#recurrencia').val(credito.recurrencia);
                $('#tasa_interes').val(credito.tasa);
                $('#tiempo_credito').val(credito.tiempo);
                $('#monto').val(credito.monto_total);
                $('#fecha_desembolso').val(credito.fecha_desembolso);
                $('#descripcion_negocio').val(credito.descripcion_negocio);
                $('#periodo_gracia_dias').val(credito.periodo_gracia_dias);
                $('#nombre_prestamo').val(credito.nombre_prestamo);
                $('#cantidad_grupo').val(credito.cantidad_integrantes);
                $('#porcentaje_venta_credito').val(credito.porcentaje_credito);
                llenarClientes(response.clientes);
               
            },
            error: function(xhr) {
                console.error("Error al recuperar la información: " + xhr.statusText);
            }
        });
    }

    function llenarClientes(clientes) {
        // Llenar datos del cliente individual
        $('#nombre').val(clientes[0].clientes.nombre || '');
        $('#telefono').val(clientes[0].clientes.telefono || '');
        $('#direccion').val(clientes[0].clientes.direccion || '');
        $('#profesion').val(clientes[0].clientes.profesion || '');
        $('#email').val(clientes[0].clientes.email || '');
        $('#direccion_laboral').val(clientes[0].clientes.direccion_laboral || '');
        $('#documento_identidad').val(clientes[0].clientes.documento_identidad || '');
    }

    cargardata();
    });
    let clientesArray = [];
    let totalMonto = 0;

    document.getElementById('buscarClientec').addEventListener('click', function() {
        const documentoIdentidad = document.getElementById('dnic').value;
        $.ajax({
            url: '{{ route('creditos.buscardni') }}',
            type: 'GET',
            data: {
                documento_identidad: documentoIdentidad
            },
            success: function(response) {
                const cliente = {
                    nombre: response.nombre || '',
                    profesion: response.profesion || '',
                    telefono: response.telefono || '',
                    direccion: response.direccion || '',
                    monto: 0,
                    documento: documentoIdentidad,
                };
                clientesArray.push(cliente);
                actualizarTabla();
                document.getElementById('dnic').value = ''; // Limpiar campo
            },
            error: function(xhr) {
                console.error("Error al recuperar información: " + xhr.statusText);
                // Limpiar campo si hay error
                document.getElementById('dnic').value = '';
            }
        });
    });

    function actualizarTabla() {
        const tablaCuerpo = document.getElementById('tablaCuerpo');
        tablaCuerpo.innerHTML = '';
        totalMonto = 0;
        clientesArray.forEach((cliente, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
            <td><input type="text" class="form-control" value="${cliente.nombre}" onchange="editarCliente(${index}, 'nombre', this.value)"></td>
            <td><input type="text" class="form-control" value="${cliente.documento}" onchange="editarCliente(${index}, 'documento', this.value)"></td>
            <td><input type="text" class="form-control" value="${cliente.profesion}" onchange="editarCliente(${index}, 'profesion', this.value)"></td>
            <td><input type="text" class="form-control" value="${cliente.telefono}" onchange="editarCliente(${index}, 'telefono', this.value)"></td>
            <td><input type="text" class="form-control" value="${cliente.direccion}" onchange="editarCliente(${index}, 'direccion', this.value)"></td>
            <td><input type="number" class="form-control" value="${cliente.monto}" onchange="editarCliente(${index}, 'monto', this.value)"></td>
            <td><button class="btn btn-danger btn-sm" onclick="eliminarCliente(${index})"><i class="fa fa-trash"></i></button></td>
        `;
            totalMonto += parseFloat(cliente.monto) || 0;
        });
        document.getElementById('totalMonto').textContent = totalMonto.toFixed(2);
        document.getElementById('monto').value = totalMonto.toFixed(2);
    }

    function editarCliente(index, campo, valor) {
        clientesArray[index][campo] = valor;
        if (campo === 'monto') {
            totalMonto = clientesArray.reduce((total, cliente) => total + (parseFloat(cliente.monto) || 0), 0);
            document.getElementById('totalMonto').textContent = totalMonto.toFixed(2);
            document.getElementById('monto').value = totalMonto.toFixed(2);
        }
    }

    function eliminarCliente(index) {
        clientesArray.splice(index, 1);
        actualizarTabla();
    }
    let proyeccionesArray = [];


    function toggleFields() {
        var selection = document.getElementById('tipo_producto').value;
        var credito_individual = document.getElementById('credito_individual');
        var grupal1Fields = document.getElementById('grupal1Fields');
        var individual1Fields = document.getElementById('individual1Fields');
        var selectionTipoCredito = document.getElementById('tipo_credito').value;
        console.log(selectionTipoCredito);
        if (selectionTipoCredito != '') {
            $.ajax({
                url: '{{ url('/admin/credito/descripcion') }}',
                type: 'GET',
                data: {
                    opcion: selectionTipoCredito
                },
                success: function(response) {
                    var descripciones = response.data;
                    console.log(descripciones);
                    var descripcionSelect = document.getElementById('descripcion_negocio');
                    descripcionSelect.innerHTML =
                        '<option value="" selected >Seleccione una descripción...</option>';
                    descripciones.forEach(function(descripcion) {
                        var option = document.createElement('option');
                        option.value = descripcion.giro_economico;
                        option.text = descripcion.giro_economico;
                        descripcionSelect.appendChild(option);
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        } else {
            var descripcionSelect = document.getElementById('descripcion_negocio');
            descripcionSelect.innerHTML = '<option value="">Seleccione una descripción...</option>';
        }
    }


    $(document).ready(function() {
        $('#buscarCliente').click(function() {
            var documentoIdentidad = $('#documento_identidad').val();
            $.ajax({
                url: '{{ route('creditos.buscardni') }}',
                type: 'GET',
                data: {
                    documento_identidad: documentoIdentidad
                },
                success: function(response) {
                    $('#nombre').val(response.nombre || '');
                    $('#telefono').val(response.telefono || '');
                    $('#email').val(response.email || '');
                    $('#direccion').val(response.direccion || '');
                    $('#direccion_laboral').val(response.direccion_laboral || '');
                    $('#profesion').val(response.profesion || '');
                },
                error: function(xhr) {
                    console.error("Error al recuperar información: " + xhr.statusText);
                    $('#nombre, #telefono, #email, #direccion, #direccion_laboral, #profesion')
                        .val('');
                }
            });
        });



        $('#prestamoForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('clientesArray', JSON.stringify(clientesArray));
            formData.append('proyeccionesArray', JSON.stringify([]));
            formData.append('inventarioArray', JSON.stringify([]));
            formData.append('deudasFinancierasArray', JSON.stringify([]));
            formData.append('gastosOperativosArray', JSON.stringify([]));
            formData.append('boletasArray', JSON.stringify([]));
            formData.append('gastosProducirArray', JSON.stringify([]));
            formData.append('inventarioArray1', JSON.stringify([]));
            formData.append('ventasdiarias', JSON.stringify([]));
            $.ajax({
                url: '{{ url('/admin/creditos/store') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Datos guardados exitosamente'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ url('admin/creditos') }}';
                        }
                    });
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar los datos'
                    });
                    console.log(response);
                }
            });
        });
    });
    let descripciones = [];



    document.addEventListener('DOMContentLoaded', toggleFields);
</script>
@endsection