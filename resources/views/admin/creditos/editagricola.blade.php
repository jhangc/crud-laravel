@extends('layouts.admin')

@section('content')
<div class="row ">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar Credito Producción Agricola</h3>
            </div>
            <div class="card-body">
                <form enctype="multipart/form-data" id="prestamoForm" name="prestamoForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="hidden" value="produccion" name="tipo_credito" id="tipo_credito">
                                <input id="credito-id" name="credito-id" value="{{$id}}" type="hidden">
                                <input type="hidden" value="agricola" name="tipo_producto" id="tipo_producto">
                                <label for="subproducto">SubProductos</label>
                                <select name="subproducto" id="subproducto" class="form-control" required onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="palmoagro" {{ old('subproducto') == 'palmoagro' ? 'selected' : '' }}>Palmo agro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="destino_credito">Destino de crédito</label>
                                <select name="destino_credito" id="destino_credito" class="form-control" required onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="activo fijo" {{ old('destino_credito') == 'activo fijo' ? 'selected' : '' }}>Activo fijo</option>
                                    <option value="capital de trabajo" {{ old('destino_credito') == 'capital de trabajo' ? 'selected' : '' }}>Capital de trabajo</option>
                                    <option value="consumo" {{ old('destino_credito') == 'consumo' ? 'selected' : '' }}>Consumo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card card-outline card-warning" id="individual1Fields">
                        <div class="card-header">
                            <h3 class="card-title">Datos del cliente</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="documento_identidad">Documento de identidad</label>
                                        <div class="input-group">
                                            <input type="text" id="documento_identidad" name="documento_identidad" class="form-control">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="buscarCliente"><i class="fas fa-search"></i> Buscar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del cliente</label>
                                        <input type="text" value="{{ old('nombre') }}" name="nombre" id="nombre" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profesion">Profesión</label>
                                        <input type="text" value="{{ old('profesion') }}" name="profesion" id="profesion" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" value="{{ old('telefono') }}" name="telefono" id="telefono" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Correo electrónico</label>
                                        <input type="email" value="{{ old('email') }}" name="email" id="email" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" value="{{ old('direccion') }}" name="direccion" id="direccion" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion_laboral">Dirección laboral</label>
                                        <input type="text" value="{{ old('direccion_laboral') }}" name="direccion_laboral" id="direccion_laboral" class="form-control">
                                    </div>
                                </div>
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
                                        <select name="descripcion_negocio" id="descripcion_negocio"  class="form-control">
                                            <option value="" selected>Seleccione una descripción...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="recurencia_individual">
                                        <label for="recurrencia">Recurrencia</label>
                                        <select name="recurrencia" id="recurrencia" class="form-control">
                                            <option value="">Seleccione una opción...</option>
                                            <option value="mensual" {{ old('recurrencia') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                            <option value="quincenal" {{ old('recurrencia') == 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                                            <option value="anual" {{ old('recurrencia') == 'anual' ? 'selected' : '' }}>Anual</option>
                                            <option value="semestral" {{ old('recurrencia') == 'semestral' ? 'selected' : '' }}>Semestral</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tasa_interes">Tasa de interés anual (%)</label>
                                        <input type="text" value="{{ old('tasa_interes') }}" name="tasa_interes" id="tasa_interes" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tiempo_credito">Tiempo del crédito</label>
                                        <input type="text" value="{{ old('tiempo_credito') }}" name="tiempo_credito" id="tiempo_credito" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_desembolso">Fecha de desembolso</label>
                                        <input type="date" value="{{ old('fecha_desembolso') }}" name="fecha_desembolso" id="fecha_desembolso" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periodo_gracia_dias">Periodo de Gracia (días)</label>
                                        <input type="number" value="{{ old('periodo_gracia_dias') }}" name="periodo_gracia_dias" id="periodo_gracia_dias" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="monto">Monto total (S/.)</label>
                                        <input type="text" value="{{ old('monto') }}" name="monto" id="monto" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Ventas por Producto</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nombre_actividad">Nombre de actividad</label>
                                        <input type="text" id="nombre_actividad" name="nombre_actividad" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cantidad_terreno">Unidad medida de la siembra</label>
                                        <select name="cantidad_terreno" id="cantidad_terreno" class="form-control">
                                            <option value="hectarea" selected>Héctareas</option>
                                            <option value="metros">Metros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cantidad_cultivar">Cantidad a cultivar</label>
                                        <input type="number" id="cantidad_cultivar" name="cantidad_cultivar" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="unidad_medida_venta">Unidad medida de venta</label>
                                        <select name="unidad_medida_venta" id="unidad_medida_venta" class="form-control">
                                            <option value="kilos" selected>Kg</option>
                                            <option value="qq">Quintales</option>
                                            <option value="unidades">unidades</option>
                                            <option value="sacos">sacos</option>
                                            <option value="toneladas">toneladas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="rendimiento_unidad_siembra">Rendimiento por unidad de siembra</label>
                                        <input type="number" id="rendimiento_unidad_siembra" name="rendimiento_unidad_siembra" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ciclo_productivo">Ciclo productivo (meses)</label>
                                        <input type="number" id="ciclo_productivo" name="ciclo_productivo" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mes_inicio">Mes de inicio</label>
                                        <input type="date" id="mes_inicio" name="mes_inicio" class="form-control" onchange="proyectarMeses()">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>% de ventas mensual</h5>
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Mes</th>
                                                <th>% Ventas</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla_ventas_mensual">
                                            <!-- Los meses y porcentajes se agregarán aquí dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Tipo de producto</h5>
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Calidad</th>
                                                <th>Precio unitario (S/.)</th>
                                                <th>Porcentaje (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla_tipo_producto">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Gastos Operativos</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Etapa</th>
                                        <th style="width: 120px;">Unidad</th>
                                        <th>Precio Unitario</th>
                                        <th>mes 1</th>
                                        <th>mes 2</th>
                                        <th>mes 3</th>
                                        <th>mes 4</th>
                                        <th>mes 5</th>
                                        <th>mes 6</th>
                                        <th>mes 7</th>
                                        <th>mes 8</th>
                                        <th>mes 9</th>
                                        <th>mes 10</th>
                                        <th>mes 11</th>
                                        <th>mes 12</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_gastos_operativos">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card card-outline card-warning" id="inventario_producto">
                        <div class="card-header">
                            <h3 class="card-title">Inventario Cosecha Terminada</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="descripcion_producto_inventario">Descripción del Producto</label>
                                        <input type="text" id="descripcion_producto_inventario" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidad_medida">Unidad de medida</label>
                                        <select name="unidad_medida_inventario" id="unidad_medida_inventario" class="form-control">
                                            <option value="und" selected>Unidades</option>
                                            <option value="qq">Quintales</option>
                                            <option value="kg">Kilos</option>
                                            <option value="m">metros</option>
                                            <option value="l">litros</option>
                                            <option value="sacos">sacos</option>
                                            <option value="toneladas">toneladas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="precio_unitario_inventario">Precio unitario</label>
                                        <input type="number" id="precio_unitario_inventario" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_producto_inventario">Cantidad</label>
                                        <input type="number" id="cantidad_producto_inventario" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarInventariotabla()" class="btn btn-warning btnprestamo">Añadir Producto</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered" id="datosTablaInventario">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Precio unitario</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaInventario">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Total:</strong></td>
                                        <td><input type="text" id="totalMontoInventario" class="form-control" value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="card card-outline card-warning" id="inventario_materiales">
                        <div class="card-header">
                            <h3 class="card-title">Inventario de materiales</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="descripcion_material_inventario_materiales">Descripción del Material</label>
                                        <input type="text" id="descripcion_material_inventario_materiales" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidad_medida_inventario_materiales">Unidad de medida</label>
                                        <select name="unidad_medida_inventario_materiales" id="unidad_medida_inventario_materiales" class="form-control">
                                            <option value="und" selected>Unidades</option>
                                            <option value="qq">Quintales</option>
                                            <option value="kg">Kilos</option>
                                            <option value="m">metros</option>
                                            <option value="l">litros</option>
                                            <option value="sacos">sacos</option>
                                            <option value="toneladas">toneladas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="precio_unitario_inventario_materiales">Precio unitario</label>
                                        <input type="number" id="precio_unitario_inventario_materiales" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_material_inventario_materiales">Cantidad</label>
                                        <input type="number" id="cantidad_material_inventario_materiales" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarInventarioMaterialTabla()" class="btn btn-warning btnprestamo">Añadir Material</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered" id="datosTablaInventario_materiales">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Precio unitario</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaInventario_materiales">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Total:</strong></td>
                                        <td><input type="text" id="totalMontoInventario_materiales" class="form-control" value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div> --}}
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Datos del la Garantia</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="descripcion_garantia">Descripcion de la Garantia</label>
                                        <input type="text" name="descripcion_garantia" id="descripcion_garantia" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valor_mercado">Valor del mercado (S/.)</label>
                                        <input type="number" name="valor_mercado" id="valor_mercado" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md3">
                                    <div class="form-group">
                                        <label for="valor_realizacion">Valor de realización (S/.)</label>
                                        <input type="number" name="valor_realizacion" id="valor_realizacion" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valor_gravamen">Valor de Gravamen (S/.)</label>
                                        <input type="number" name="valor_gravamen" id="valor_gravamen" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="archivo_garantia">Archivo en pdf</label>
                                        <input type="file" name="archivo_garantia" accept=".pdf" class="form-control-file">
                                        @error('archivo_garantia')
                                        <small style="color: red">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-outline card-warning" id="gastos_familiares">
                        <div class="card-header">
                            <h3 class="card-title">Registro De Gastos Familiares</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="descripcion_producto_inventario">Descripción del gasto</label>
                                        <input type="text" id="descripcion_producto_inventario1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precio_unitario_inventario">Precio unitario</label>
                                        <input type="number" id="precio_unitario_inventario1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_producto_inventario">Cantidad</label>
                                        <input type="number" id="cantidad_producto_inventario1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarInventariotabla1()" class="btn btn-warning btnprestamo">Añadir Producto</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered" id="datosTablaInventario1">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Precio unitario</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaInventario1">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td><strong>Total:</strong></td>
                                        <td><input type="text" id="totalgastosfamiliares" class="form-control" value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card card-outline card-warning" id="deudas_finan">
                        <div class="card-header" style="display:flex;">
                            <h3 class="card-title">Deudas Financieras</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="entidad_financiera">Nombre de la entidad</label>
                                        <input type="text" id="entidad_financiera" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="saldo_capital">Saldo Restante</label>
                                        <input type="text" id="saldo_capital" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cuota">Cuota</label>
                                        <input type="text" id="cuota" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="agregarDeudaFinanciera()" class="btn btn-primary btnprestamo">Añadir Deuda</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Nombre de la entidad</th>
                                        <th>Saldo Restante</th>
                                        <th>Cuota</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="datos_tabla_deudas_financieras">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ url('admin/creditos') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Guardar registro</button>
                        </div>
                    </div>
                    <hr>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function cargarData() {
        const idc = document.getElementById('credito-id').value;
        $.ajax({
            url: `/admin/creditoinfo/${idc}`,
            type: 'GET',
            success: function(response) {
                let credito = response.credito;

                // Llenar datos del crédito
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
                // Llenar  registor de  producto
                let gastoagricola=response.gastosAgricolas;
                $('#nombre_actividad').val(gastoagricola.nombre_actividad);
                $('#cantidad_terreno').val(gastoagricola.unidad_medida_siembra);
                $('#cantidad_cultivar').val(gastoagricola.cantidad_cultivar);
                $('#unidad_medida_venta').val(gastoagricola.unidad_medida_venta);
                $('#rendimiento_unidad_siembra').val(gastoagricola.rendimiento_unidad_siembra);
                $('#ciclo_productivo').val(gastoagricola.ciclo_productivo_meses);
                $('#mes_inicio').val(gastoagricola.mes_inicio);
                //garantia
                let garantia=response.garantia;
                $('#descripcion_garantia').val(garantia.descripcion);
                $('#valor_mercado').val(garantia.valor_mercado);
                $('#valor_realizacion').val(garantia.valor_realizacion);
                $('#valor_gravamen').val(garantia.valor_gravamen);


                // Llenar datos del cliente
                if (response.clientes.length > 0) {
                    let cliente = response.clientes[0].clientes;
                    $('#documento_identidad').val(cliente.documento_identidad);
                    $('#nombre').val(cliente.nombre);
                    $('#telefono').val(cliente.telefono);
                    $('#email').val(cliente.email);
                    $('#direccion').val(cliente.direccion);
                    $('#direccion_laboral').val(cliente.direccion_laboral);
                    $('#profesion').val(cliente.profesion);
                }

                // Llenar ventas mensuales
                ventasMensualesArray = response.ventasMensuales.map(venta => ({
                    mes: venta.mes,
                    porcentaje: parseFloat(venta.porcentaje)
                }));
                actualizarTablaVentasMensual();

                // Llenar tipo de producto
                tipoProductoArray = response.tipoProducto.map(producto => ({
                    PRODUCTO: producto.producto,
                    precio_unitario: parseFloat(producto.precio),
                    procentaje_producto: parseFloat(producto.porcentaje)
                }));
                actualizarTablaTipoProducto();

                // Llenar gastos operativos
                gastosOperativosArray = response.gastosOperativos.map(gasto => ({
                    gasto: gasto.descripcion,
                    unidad: gasto.unidad,
                    precioUnitario: gasto.precio_unitario,
                    mes1: gasto.mes1,
                    mes2: gasto.mes2,
                    mes3: gasto.mes3,
                    mes4: gasto.mes4,
                    mes5: gasto.mes5,
                    mes6: gasto.mes6,
                    mes7: gasto.mes7,
                    mes8: gasto.mes8,
                    mes9: gasto.mes9,
                    mes10: gasto.mes10,
                    mes11: gasto.mes11,
                    mes12: gasto.mes12
                }));
                actualizarTablaGastosOperativos();

                // Llenar inventario
                inventarioArray = response.inventario.map(item => ({
                    descripcion: item.descripcion,
                    precioUnitario: item.precio_unitario,
                    cantidad: item.cantidad,
                    unidad: item.unidad,
                    montoTotal: (item.precio_unitario * item.cantidad).toFixed(2)
                }));
                actualizarTablaInventario();

                // Llenar gastos familiares
                inventarioArray1 = response.gastosFamiliares.map(item => ({
                    descripcion: item.descripcion,
                    precioUnitario: item.precio_unitario,
                    cantidad: item.cantidad,
                    montoTotal: (item.precio_unitario * item.cantidad).toFixed(2)
                }));
                actualizarTablaInventario1();

                // Llenar deudas financieras
                deudasFinancierasArray = response.deudasFinancieras.map(deuda => ({
                    entidad: deuda.nombre_entidad,
                    saldoCapital: parseFloat(deuda.saldo_capital),
                    cuota: parseFloat(deuda.cuota),
                    tiempoRestante: deuda.tiempo_restante
                }));
                actualizarTablaDeudasFinancieras();
            },
            error: function(response) {
                console.error(response);
            }
        });
    }

    function actualizarTablaVentasMensual() {
        const tablaCuerpo = document.getElementById('tabla_ventas_mensual');
        tablaCuerpo.innerHTML = '';
        ventasMensualesArray.forEach((venta, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${venta.mes}</td>
                <td><input type="number" class="form-control" value="${venta.porcentaje}" onchange="actualizarVentaMensual(${index}, this.value)"></td>
            `;
        });
    }

    function actualizarTablaTipoProducto() {
        const tablaCuerpo = document.getElementById('tabla_tipo_producto');
        tablaCuerpo.innerHTML = '';
        tipoProductoArray.forEach((producto, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${producto.PRODUCTO}</td>
                <td><input type="number" class="form-control" value="${producto.precio_unitario}" onchange="actualizarTipoProducto(${index}, 'precio_unitario', this.value)"></td>
                <td><input type="number" class="form-control" value="${producto.procentaje_producto}" onchange="actualizarTipoProducto(${index}, 'procentaje_producto', this.value)"></td>
            `;
        });
    }

    function actualizarTablaGastosOperativos() {
        const tablaCuerpo = document.getElementById('tabla_gastos_operativos');
        tablaCuerpo.innerHTML = '';
        gastosOperativosArray.forEach((gasto, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${gasto.gasto}</td>
                <td>
                    <select class="form-control" onchange="actualizarGastoOperativo(${index}, 'unidad', this.value)">
                        <option value="" ${gasto.unidad === '' ? 'selected' : ''}>seleccione...</option>
                        <option value="jor" ${gasto.unidad === 'jor' ? 'selected' : ''}>Jor.</option>
                        <option value="uni" ${gasto.unidad === 'uni' ? 'selected' : ''}>Uni.</option>
                    </select>
                </td>
                <td><input type="number" class="form-control" value="${gasto.precioUnitario}" onchange="actualizarGastoOperativo(${index}, 'precioUnitario', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes1}" onchange="actualizarGastoOperativo(${index}, 'mes1', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes2}" onchange="actualizarGastoOperativo(${index}, 'mes2', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes3}" onchange="actualizarGastoOperativo(${index}, 'mes3', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes4}" onchange="actualizarGastoOperativo(${index}, 'mes4', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes5}" onchange="actualizarGastoOperativo(${index}, 'mes5', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes6}" onchange="actualizarGastoOperativo(${index}, 'mes6', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes7}" onchange="actualizarGastoOperativo(${index}, 'mes7', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes8}" onchange="actualizarGastoOperativo(${index}, 'mes8', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes9}" onchange="actualizarGastoOperativo(${index}, 'mes9', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes10}" onchange="actualizarGastoOperativo(${index}, 'mes10', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes11}" onchange="actualizarGastoOperativo(${index}, 'mes11', this.value)"></td>
                <td><input type="number" class="form-control" value="${gasto.mes12}" onchange="actualizarGastoOperativo(${index}, 'mes12', this.value)"></td>
            `;
        });
    }

    function actualizarTablaInventario() {
        const tablaCuerpo = document.getElementById('tablaInventario');
        tablaCuerpo.innerHTML = '';
        totalInventario = 0;

        inventarioArray.forEach((producto, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${producto.descripcion}</td>
                <td>${producto.unidad}</td>
                <td><input type="number" class="form-control" value="${producto.precioUnitario}" onchange="editarProducto(${index}, 'precioUnitario', this.value)"></td>
                <td><input type="number" class="form-control" value="${producto.cantidad}" onchange="editarProducto(${index}, 'cantidad', this.value)"></td>
                <td>${producto.montoTotal}</td>
                <td><button class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})"><i class="fa fa-trash"></i></button></td>
            `;
            totalInventario = parseFloat(totalInventario) + parseFloat(producto.montoTotal);
        });
        document.getElementById('totalMontoInventario').value = totalInventario.toFixed(2);
    }

    function actualizarTablaInventario1() {
        const tablaCuerpo = document.getElementById('tablaInventario1');
        tablaCuerpo.innerHTML = '';
        totalInventario1 = 0;

        inventarioArray1.forEach((producto, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${producto.descripcion}</td>
                <td><input type="number" class="form-control" value="${producto.precioUnitario}" onchange="editarProducto1(${index}, 'precioUnitario', this.value)"></td>
                <td><input type="number" class="form-control" value="${producto.cantidad}" onchange="editarProducto1(${index}, 'cantidad', this.value)"></td>
                <td>${producto.montoTotal}</td>
                <td><button class="btn btn-danger btn-sm" onclick="eliminarProducto1(${index})"><i class="fa fa-trash"></i></button></td>
            `;
            totalInventario1 = parseFloat(totalInventario1) + parseFloat(producto.montoTotal);
        });
        document.getElementById('totalgastosfamiliares').value = totalInventario1.toFixed(2);
    }

    function actualizarTablaDeudasFinancieras() {
        const tablaCuerpo = document.getElementById('datos_tabla_deudas_financieras');
        tablaCuerpo.innerHTML = '';

        deudasFinancierasArray.forEach((deuda, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${deuda.entidad}</td>
                <td>${deuda.saldoCapital}</td>
                <td>${deuda.cuota}</td>
                <td><button class="btn btn-danger btn-sm" onclick="eliminarDeudaFinanciera(${index})"><i class="fa fa-trash"></i></button></td>
            `;
        });
    }

    function actualizarTipoProducto(index, campo, valor) {
        tipoProductoArray[index][campo] = parseFloat(valor);
    }

    function actualizarVentaMensual(index, valor) {
        ventasMensualesArray[index].porcentaje = parseFloat(valor);
    }

    function actualizarGastoOperativo(index, campo, valor) {
        if (campo === 'precioUnitario' || campo.startsWith('mes')) {
            valor = parseFloat(valor);
        }
        gastosOperativosArray[index][campo] = valor;
    }

    $(document).ready(function() {
        toggleFields();
        cargarData();
        
    });

    $('#buscarCliente').click(function() {
        var documentoIdentidad = $('#documento_identidad').val();
        $.ajax({
            url: '{{route('creditos.buscardni')}}',
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
                $('#nombre, #telefono, #email, #direccion, #direccion_laboral, #profesion').val('');
            }
        });
    });

    let ventasMensualesArray = [];
    let inventarioArray = [];
    let inventarioMaterialArray =[];
    let deudasFinancierasArray =[];
    let gastosOperativosArray=[];
    let inventarioArray1=[];

    function agregarInventariotabla() {
        const descripcion = document.getElementById('descripcion_producto_inventario').value;
        const precioUnitario = document.getElementById('precio_unitario_inventario').value;
        const cantidad = document.getElementById('cantidad_producto_inventario').value;
        const unidad = document.getElementById('unidad_medida_inventario').value;
        const montoTotal = (precioUnitario * cantidad).toFixed(2);

        const producto = {
            descripcion,
            precioUnitario,
            cantidad,
            unidad,
            montoTotal: parseFloat(montoTotal)
        };

        inventarioArray.push(producto);
        actualizarTablaInventario();
        limpiarCamposInventario();
    }

    function editarProducto(index, campo, valor) {
        inventarioArray[index][campo] = parseFloat(valor);
        if (campo === 'precioUnitario' || campo === 'cantidad') {
            inventarioArray[index].montoTotal = (inventarioArray[index].precioUnitario * inventarioArray[index].cantidad).toFixed(2);
        }
        actualizarTablaInventario();
    }

    function eliminarProducto(index) {
        inventarioArray.splice(index, 1);
        actualizarTablaInventario();
    }

    function limpiarCamposInventario() {
        document.getElementById('descripcion_producto_inventario').value = '';
        document.getElementById('precio_unitario_inventario').value = '';
        document.getElementById('cantidad_producto_inventario').value = '';
    }

    function agregarInventarioMaterialTabla() {
        const descripcion = document.getElementById('descripcion_material_inventario_materiales').value;
        const precioUnitario = document.getElementById('precio_unitario_inventario_materiales').value;
        const cantidad = document.getElementById('cantidad_material_inventario_materiales').value;
        const unidad = document.getElementById('unidad_medida_inventario_materiales').value;
        const montoTotal = (precioUnitario * cantidad).toFixed(2);

        const material = {
            descripcion,
            precioUnitario,
            cantidad,
            unidad,
            montoTotal: parseFloat(montoTotal)
        };

        inventarioMaterialArray.push(material);
        actualizarTablaInventarioMaterial();
        limpiarCamposInventarioMaterial();
    }

    function actualizarTablaInventarioMaterial() {
        const tablaCuerpo = document.getElementById('tablaInventario_materiales');
        tablaCuerpo.innerHTML = '';
        totalInventarioMaterial = 0;

        inventarioMaterialArray.forEach((material, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
                <td>${material.descripcion}</td>
                <td>${material.unidad}</td>
                <td><input type="number" class="form-control" value="${material.precioUnitario}" onchange="editarMaterial(${index}, 'precioUnitario', this.value)"></td>
                <td><input type="number" class="form-control" value="${material.cantidad}" onchange="editarMaterial(${index}, 'cantidad', this.value)"></td>
                <td>${material.montoTotal}</td>
                <td><button class="btn btn-danger btn-sm" onclick="eliminarMaterial(${index})"><i class="fa fa-trash"></i></button></td>
            `;
            totalInventarioMaterial = parseFloat(totalInventarioMaterial) + parseFloat(material.montoTotal);
        });
        document.getElementById('totalMontoInventario_materiales').value = totalInventarioMaterial.toFixed(2);
    }

    function editarMaterial(index, campo, valor) {
        inventarioMaterialArray[index][campo] = parseFloat(valor);
        if (campo === 'precioUnitario' || campo === 'cantidad') {
            inventarioMaterialArray[index].montoTotal = (inventarioMaterialArray[index].precioUnitario * inventarioMaterialArray[index].cantidad).toFixed(2);
        }
        actualizarTablaInventarioMaterial();
    }

    function eliminarMaterial(index) {
        inventarioMaterialArray.splice(index, 1);
        actualizarTablaInventarioMaterial();
    }

    function limpiarCamposInventarioMaterial() {
        document.getElementById('descripcion_material_inventario_materiales').value = '';
        document.getElementById('precio_unitario_inventario_materiales').value = '';
        document.getElementById('cantidad_material_inventario_materiales').value = '';
    }

    function agregarInventariotabla1() {
        const descripcion = document.getElementById('descripcion_producto_inventario1').value;
        const precioUnitario = document.getElementById('precio_unitario_inventario1').value;
        const cantidad = document.getElementById('cantidad_producto_inventario1').value;
        const montoTotal = (precioUnitario * cantidad).toFixed(2);

        const producto = {
            descripcion,
            precioUnitario,
            cantidad,
            montoTotal: parseFloat(montoTotal)
        };

        inventarioArray1.push(producto);
        actualizarTablaInventario1();
        limpiarCamposInventario1();
    }

    function editarProducto1(index, campo, valor) {
        inventarioArray1[index][campo] = parseFloat(valor);
        if (campo === 'precioUnitario' || campo === 'cantidad') {
            inventarioArray1[index].montoTotal = (inventarioArray1[index].precioUnitario * inventarioArray1[index].cantidad).toFixed(2);
        }
        actualizarTablaInventario1();
    }

    function eliminarProducto1(index) {
        inventarioArray1.splice(index, 1);
        actualizarTablaInventario1();
    }

    function limpiarCamposInventario1() {
        document.getElementById('descripcion_producto_inventario1').value = '';
        document.getElementById('precio_unitario_inventario1').value = '';
        document.getElementById('cantidad_producto_inventario1').value = '';
    }

    function agregarDeudaFinanciera() {
        const entidad = document.getElementById('entidad_financiera').value;
        const saldoCapital = parseFloat(document.getElementById('saldo_capital').value);
        const cuota = parseFloat(document.getElementById('cuota').value);
        const tiempoRestante = "0";

        const deuda = {
            entidad,
            saldoCapital,
            cuota,
            tiempoRestante
        };
        deudasFinancierasArray.push(deuda);
        actualizarTablaDeudasFinancieras();
        limpiarCamposDeudaFinanciera();
    }

    function limpiarCamposDeudaFinanciera() {
        document.getElementById('entidad_financiera').value = '';
        document.getElementById('saldo_capital').value = '';
        document.getElementById('cuota').value = '';
    }

    $('#prestamoForm').on('submit', function(e) {
        e.preventDefault();
        const idc = document.getElementById('credito-id').value;
        var formData = new FormData(this);
        formData.append('proyeccionesArray', JSON.stringify([]));
        formData.append('ventasMensualesArray', JSON.stringify(ventasMensualesArray));
        formData.append('tipoProductoArray', JSON.stringify(tipoProductoArray));
        formData.append('gastosAgricolaArray', JSON.stringify(gastosOperativosArray));
        formData.append('inventarioArray', JSON.stringify(inventarioArray));
        formData.append('inventarioMaterialArray', JSON.stringify(inventarioMaterialArray));
        formData.append('deudasFinancierasArray', JSON.stringify(deudasFinancierasArray));
        formData.append('inventarioArray1', JSON.stringify(inventarioArray1)); 
                $.ajax({
                    url:`/admin/creditos/updateagricola/${idc}`,
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
                                window.location.href = '{{ url('admin/creditos')}}';
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

    function toggleFields() {
        var selectionTipoCredito = "produccion";
        if (selectionTipoCredito != '') {
            $.ajax({
                url: '{{ url('/admin/credito/descripcion')}}',
                type: 'GET',
                data: {
                    opcion: selectionTipoCredito
                },
                success: function(response) {
                    var descripciones = response.data;
                    var descripcionSelect = document.getElementById('descripcion_negocio');
                    descripcionSelect.innerHTML = '<option value="" selected >Seleccione una descripción...</option>';
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

    function proyectarMeses() {
        var fechaInicio = document.getElementById('mes_inicio').value;
        if (fechaInicio) {
            var fecha = new Date(fechaInicio);
            var meses = [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
                "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];
            var tablaVentasMensual = document.getElementById('tabla_ventas_mensual');
            var tablaTipoProducto = document.getElementById('tabla_tipo_producto');
            tablaVentasMensual.innerHTML = '';
            tablaTipoProducto.innerHTML = '';
            tipoProductoArray = [{
                    "PRODUCTO": "A",
                    "precio_unitario": "",
                    "procentaje_producto": ""
                },
                {
                    "PRODUCTO": "B",
                    "precio_unitario": "",
                    "procentaje_producto": ""
                },
                {
                    "PRODUCTO": "C",
                    "precio_unitario": "",
                    "procentaje_producto": ""
                },
                {
                    "PRODUCTO": "D",
                    "precio_unitario": "",
                    "procentaje_producto": ""
                }
            ]
            for (var i = 0; i < tipoProductoArray.length; i++) {
                var row = tablaTipoProducto.insertRow();
                row.innerHTML = `
                    <tr>
                        <td>${tipoProductoArray[i].PRODUCTO}</td>
                        <td><input type="number" class="form-control" value="${tipoProductoArray[i].precio_unitario}" onchange="actualizarTipoProducto(${i}, 'precio_unitario', this.value)"></td>
                        <td><input type="number" class="form-control" value="${tipoProductoArray[i].procentaje_producto}" onchange="actualizarTipoProducto(${i}, 'procentaje_producto', this.value)"></td>
                    </tr>
                `;
            }

            gastosOperativosArray = [{
                    "gasto": "DESHIERBO",
                    "unidad": "",
                    'precioUnitario':"",
                    "mes1": "",
                    "mes2": "",
                    "mes3": "",
                    "mes4": "",
                    "mes5": "",
                    "mes6": "",
                    "mes7": "",
                    "mes8": "",
                    "mes9": "",
                    "mes10": "",
                    "mes11": "",
                    "mes12": ""
                },
                {
                    "gasto": "PODA",
                    "unidad": "",
                    'precioUnitario':"",
                    "mes1": "",
                    "mes2": "",
                    "mes3": "",
                    "mes4": "",
                    "mes5": "",
                    "mes6": "",
                    "mes7": "",
                    "mes8": "",
                    "mes9": "",
                    "mes10": "",
                    "mes11": "",
                    "mes12": ""
                },
                {
                    "gasto": "COSECHA",
                    'precioUnitario':"",
                    "unidad": "",
                    "mes1": "",
                    "mes2": "",
                    "mes3": "",
                    "mes4": "",
                    "mes5": "",
                    "mes6": "",
                    "mes7": "",
                    "mes8": "",
                    "mes9": "",
                    "mes10": "",
                    "mes11": "",
                    "mes12": ""
                },
                {
                    "gasto": "TRANSPORTE",
                    'precioUnitario':"",
                    "unidad": "",
                    "mes1": "",
                    "mes2": "",
                    "mes3": "",
                    "mes4": "",
                    "mes5": "",
                    "mes6": "",
                    "mes7": "",
                    "mes8": "",
                    "mes9": "",
                    "mes10": "",
                    "mes11": "",
                    "mes12": ""
                },
                {
                    "gasto": "OTROS",
                    'precioUnitario':"",
                    "unidad": "",
                    "mes1": "",
                    "mes2": "",
                    "mes3": "",
                    "mes4": "",
                    "mes5": "",
                    "mes6": "",
                    "mes7": "",
                    "mes8": "",
                    "mes9": "",
                    "mes10": "",
                    "mes11": "",
                    "mes12": ""
                }
            ];

            var tablaGastosOperativos = document.getElementById('tabla_gastos_operativos');
            tablaGastosOperativos.innerHTML = '';

            for (var i = 0; i < gastosOperativosArray.length; i++) {
                var row = tablaGastosOperativos.insertRow();
                row.innerHTML = `
                    <tr>
                        <td>${gastosOperativosArray[i].gasto}</td>
                        <td>
                            <select class="form-control" onchange="actualizarGastoOperativo(${i}, 'unidad', this.value)">
                                <option value="">seleccione...</option>
                                <option value="jor" ${gastosOperativosArray[i].unidad === 'jor' ? 'selected' : ''}>Jor.</option>
                                <option value="uni" ${gastosOperativosArray[i].unidad === 'uni' ? 'selected' : ''}>Uni.</option>
                            </select>
                        </td>
                        <td><input type="number" class="form-control" onchange="actualizarGastoOperativo(${i}, 'precioUnitario', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes1}" onchange="actualizarGastoOperativo(${i}, 'mes1', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes2}" onchange="actualizarGastoOperativo(${i}, 'mes2', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes3}" onchange="actualizarGastoOperativo(${i}, 'mes3', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes4}" onchange="actualizarGastoOperativo(${i}, 'mes4', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes5}" onchange="actualizarGastoOperativo(${i}, 'mes5', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes6}" onchange="actualizarGastoOperativo(${i}, 'mes6', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes7}" onchange="actualizarGastoOperativo(${i}, 'mes7', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes8}" onchange="actualizarGastoOperativo(${i}, 'mes8', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes9}" onchange="actualizarGastoOperativo(${i}, 'mes9', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes10}" onchange="actualizarGastoOperativo(${i}, 'mes10', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes11}" onchange="actualizarGastoOperativo(${i}, 'mes11', this.value)"></td>
                        <td><input type="number" class="form-control" value="${gastosOperativosArray[i].mes12}" onchange="actualizarGastoOperativo(${i}, 'mes12', this.value)"></td>
                    </tr>
                `;
            }

            for (var i = 0; i < 12; i++) {
                var mesIndex = (fecha.getMonth() + i) % 12;
                var mes = meses[mesIndex] + ' ' + (fecha.getFullYear() + Math.floor((fecha.getMonth() + i) / 12));

                var ventaMensual = {
                    mes: mes,
                    porcentaje: 0
                };
                ventasMensualesArray.push(ventaMensual);

                var rowVenta = tablaVentasMensual.insertRow();
                rowVenta.innerHTML = `
                    <td>${mes}</td>
                    <td><input type="number" class="form-control" value="${ventaMensual.porcentaje}" onchange="actualizarVentaMensual(${i}, this.value)"></td>
                `;
            }
        }
    }
</script>

@endsection