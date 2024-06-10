@extends('layouts.admin')

@section('content')
<div class="row">
    <h1>Editar Prestámo</h1>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos Generales</h3>
            </div>
            <div class="card-body">
                <form enctype="multipart/form-data" id="prestamoForm" name="prestamoForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_credito">Tipos de créditos</label>
                                <select name="tipo_credito" id="tipo_credito" class="form-control" required onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="comercio" {{ old('tipo_credito') == 'comercio' ? 'selected' : '' }}>Comercio</option>
                                    <option value="servicio" {{ old('tipo_credito') == 'servicio' ? 'selected' : '' }}>Servicio</option>
                                    <option value="produccion" {{ old('tipo_credito') == 'produccion' ? 'selected' : '' }}>Producción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_producto">Productos</label>
                                <select name="tipo_producto" id="tipo_producto" class="form-control" required onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="microempresa" {{ old('tipo_producto') == 'microempresa' ? 'selected' : '' }}>Microempresa</option>
                                    <option value="agricola" {{ old('tipo_producto') == 'agricola' ? 'selected' : '' }}>Agrícola</option>
                                    <option value="consumo" {{ old('tipo_producto') == 'consumo' ? 'selected' : '' }}>Consumo</option>
                                    <option value="grupal" {{ old('tipo_producto') == 'grupal' ? 'selected' : '' }}>Grupal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="subproducto">SubProductos</label>
                                <select name="subproducto" id="subproducto" class="form-control" required onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="credimujerpalmo" {{ old('subproducto') == 'credimujerpalmo' ? 'selected' : '' }}>Credimujerpalmo</option>
                                    <option value="creditoempresarial" {{ old('subproducto') == 'creditoempresarial' ? 'selected' : '' }}>Crédito empresarial</option>
                                    <option value="creditoempresarialhipotecario" {{ old('subproducto') == 'creditoempresarialhipotecario' ? 'selected' : '' }}>Crédito empresarial con garantía hipotecaria empresarial</option>
                                    <option value="palmoagro" {{ old('subproducto') == 'palmoagro' ? 'selected' : '' }}>Palmo agro con garantía hipotecaria agrícola</option>
                                    <option value="superconsumo" {{ old('subproducto') == 'superconsumo' ? 'selected' : '' }}>Superconsumo</option>
                                    <option value="superconsumohipotecario" {{ old('subproducto') == 'superconsumohipotecario' ? 'selected' : '' }}>Superconsumo con garantía hipotecaria</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="documento_identidad">DNI</label>
                                        <div class="input-group">
                                            <input type="text" id="documento_identidad" name="documento_identidad" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del cliente</label>
                                        <input type="text" value="{{ old('nombre') }}" name="nombre" id="nombre" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_prestamo">Nombre del Grupo</label>
                                <input type="text" value="{{ old('nombre_prestamo') }}" name="nombre_prestamo" id="nombre_prestamo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad_grupo">Cantidad de integrantes</label>
                                <input type="number" value="{{ old('cantidad_grupo') }}" name="cantidad_grupo" id="cantidad_grupo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="foto_grupal">Foto Grupal</label>
                                <input type="file" name="foto_grupal" accept="image/*" class="form-control-file">
                                @error('foto_grupal')
                                <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-striped table-hover table-bordered" id="datosTabla">
                        <thead class="thead-blue">
                            <tr>
                                <th>Nombre del cliente</th>
                                <th>DNI</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCuerpo">
                            <!-- Las filas se agregarán aquí dinámicamente -->
                        </tbody>
                    </table>
                    <div class="text" style="background-color: #f0f0f0; padding: 10px;">
                        <strong>Total Monto:</strong> <span id="totalMonto" style="font-size: 18px;">0.00</span>
                    </div>
                    <hr>

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
                                        <select name="descripcion_negocio" id="descripcion_negocio" class="form-control">
                                            <option value="" disabled>Seleccione una descripción...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="recurrencia">Recurrencia</label>
                                        <select name="recurrencia" id="recurrencia" class="form-control" required>
                                            <option value="">Seleccione una opción...</option>
                                            <option value="mensual" {{ old('recurrencia') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                            <option value="quincenal" {{ old('recurrencia') == 'quincenal' ? 'selected' : '' }}>Quincenal</option>
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
                                        <input type="text" value="{{ old('monto') }}" name="monto" id="monto" class="form-control" required>
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
                                        <input type="text" name="descripcion_garantia" id="descripcion_garantia" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valor_mercado">Valor del mercado (S/.)</label>
                                        <input type="number" name="valor_mercado" id="valor_mercado" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                    <div class="card card-outline card-primary" id="detalle_negocio">
                        <div class="card-header">
                            <h3 class="card-title">Proyecciones de ventas</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="producto_descripcion">Descripción del producto</label>
                                        <input type="text" id="producto_descripcion" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidad_medida">Unidad de medida</label>
                                        <select name="unidad_medida" id="unidad_medida" class="form-control">
                                            <option value="und" selected>Und</option>
                                            <option value="kg">Kg</option>
                                            <option value="m">m</option>
                                            <option value="l">l</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="frecuencia_compra">Frecuencia de compra</label>
                                        <input type="number" id="frecuencia_compra" name="frecuencia_compra" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidades_compradas">Unidades compradas</label>
                                        <input type="number" id="unidades_compradas" name="unidades_compradas" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidades_vendidas">Unidades Vendidas</label>
                                        <input type="number" id="unidades_vendidas" name="unidades_vendidas" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="stock_verificado_inspeccion">Stock verificado inspección</label>
                                        <input type="number" id="stock_verificado_inspeccion" name="stock_verificado_inspeccion" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precio_compra">Precio de Compra S/.</label>
                                        <input type="number" id="precio_compra" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precio_venta">Precio de Venta S/.</label>
                                        <input type="number" id="precio_venta" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarProductoProyeccion()" class="btn btn-primary btnprestamo">Añadir Producto</button>
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                    <thead class="thead-blue">
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Unidad de Medida</th>
                                            <th>Frecuencia de Compra</th>
                                            <th>Unidades Compradas</th>
                                            <th>Unidades Vendidas</th>
                                            <th>Stock Verificado</th>
                                            <th>Precio de Compra</th>
                                            <th>Precio de Venta</th>
                                            <th>Inventario Valorizado</th>
                                            <th>Unidades Vendidas por Mes</th>
                                            <th>Ingresos Mensuales por Venta</th>
                                            <th>Unidades Compradas por Mes</th>
                                            <th>Ingresos Mensuales por Compra</th>
                                            <th>Margen Bruto Mensual</th>
                                            <th>Margen (%)</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProyecciones">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card card-outline card-primary" id="registro_boletas">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Boletas</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_boleta">Numero de Boleta</label>
                                        <input type="text" id="numero_boleta" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="monto_boleta">Monto Boleta</label>
                                        <input type="number" id="monto_boleta" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="descuento_boleta">Descuento</label>
                                        <input type="number" id="descuento_boleta" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarBoleta()" class="btn btn-primary btnprestamo">Añadir Boleta</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Nombre de la boleta</th>
                                        <th>Monto</th>
                                        <th>Descuento</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="datos_tabla_boleta">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card card-outline card-info" id="registro_gastos_producir">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Gastos a Producir</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nombre_actividad">Nombre de actividad</label>
                                        <input type="text" id="nombre_actividad" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_terreno">Cantidad de terreno (hectáreas)</label>
                                        <input type="number" id="cantidad_terreno" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="produccion_total">Producción Total (Kg)</label>
                                        <input type="number" id="produccion_total" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="precio_kg">Precio (kg)</label>
                                        <input type="number" id="precio_kg" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="total_soles">Total en soles</label>
                                        <input type="number" id="total_soles" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="descripcion_gasto_producir">Descripción del insumo</label>
                                        <input type="text" id="descripcion_gasto_producir" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precio_unitario_gasto_producir">Precio unitario</label>
                                        <input type="number" id="precio_unitario_gasto_producir" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_gasto_producir">Cantidad</label>
                                        <input type="number" id="cantidad_gasto_producir" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarGastoProducir()" class="btn btn-info btnprestamo">Añadir Gasto</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Descripción del insumo</th>
                                        <th>Precio unitario</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="datos_tabla_gastos_producir">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card card-outline card-warning" id="inventario_producto">
                        <div class="card-header">
                            <h3 class="card-title">Registro de inventario</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="descripcion_producto_inventario">Descripción del producto</label>
                                        <input type="text" id="descripcion_producto_inventario" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                        <th>Precio unitario</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaInventario">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                            <div class="text" style="background-color: #f0f0f0; padding: 10px;">
                                <strong>Total de inventario:</strong> <span id="totalMontoInventario" style="font-size: 18px;">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="card card-outline card-info" id="gastos_ope">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Gastos Operativos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="descripcion_gasto">Descripción</label>
                                        <input type="text" id="descripcion_gasto" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precio_unitario_gasto">Precio unitario</label>
                                        <input type="number" id="precio_unitario_gasto" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_gasto">Cantidad</label>
                                        <input type="number" id="cantidad_gasto" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarGastoOperativoc()" class="btn btn-info btnprestamo">Añadir Gasto</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Precio unitario</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="datos_tabla_gastos_operativos">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                            <div class="text" style="background-color: #f0f0f0; padding: 10px;">
                                <strong>Total de Gastos Operativos:</strong> <span id="totalGatosOperativos" style="font-size: 18px;">0.00</span>
                            </div>
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
                                        <!-- <th>Tiempo restante</th> -->
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
                            <a href="{{url('admin/creditos')}}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Guardar registro</button>
                        </div>
                    </div>
                    <hr>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection