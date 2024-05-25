@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Nuevo Prestamo</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos Generales</h3>

                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/creditos') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_credito">Tipos de créditos</label>
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

                            <div class="col-md-3">
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
                                        <option value="grupal" {{ old('tipo_producto') == 'grupal' ? 'selected' : '' }}>
                                            Grupal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="subproducto">SubProductos</label>
                                    <select name="subproducto" id="subproducto" class="form-control" required
                                        onchange="toggleFields()">
                                        <option value="">Seleccione una opción...</option>
                                        <option value="credimujerpalmo"
                                            {{ old('subproducto') == 'credimujerpalmo' ? 'selected' : '' }}>
                                            Credimujerpalmo
                                        </option>
                                        <option value="creditoempresarial"
                                            {{ old('subproducto') == 'creditoempresarial' ? 'selected' : '' }}>
                                            Crédito empresarial
                                        </option>
                                        <option value="creditoempresarialhipotecario"
                                            {{ old('subproducto') == 'creditoempresarialhipotecario' ? 'selected' : '' }}>
                                            Crédito empresarial con garantía hipotecaria empresarial
                                        </option>
                                        <option value="palmoagro" {{ old('subproducto') == 'palmoagro' ? 'selected' : '' }}>
                                            Palmo agro con garantía hipotecaria agrícola
                                        </option>
                                        <option value="superconsumo"
                                            {{ old('subproducto') == 'superconsumo' ? 'selected' : '' }}>
                                            Superconsumo
                                        </option>
                                        <option value="superconsumohipotecario"
                                            {{ old('subproducto') == 'superconsumohipotecario' ? 'selected' : '' }}>
                                            Superconsumo con garantía hipotecaria
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="destino_credito">Destino de crédito</label>
                                    <select name="destino_credito" id="destino_credito" class="form-control" required
                                        onchange="toggleFields()">
                                        <option value="">Seleccione una opción...</option>
                                        <option value="activo fijo"
                                            {{ old('destino_credito') == 'activo fijo' ? 'selected' : '' }}>
                                            Activo fijo</option>
                                        <option value="capital de trabajo"
                                            {{ old('destino_credito') == 'capital de trabajo' ? 'selected' : '' }}>
                                            Capital de trabajo</option>
                                        <option value="consumo" {{ old('destino_credito') == 'consumo' ? 'selected' : '' }}>
                                            Consumo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="individualFields" style="display:none;">

                            {{-- //daots de los clientes --}}
                            {{-- <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del cliente</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="documento_identidad">Documento de identidad</label>
                                                <div class="input-group">
                                                    <input type="text" id="documento_identidad"
                                                        value="{{ old('documento_identidad') }}" name="documento_identidad"
                                                        class="form-control" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            id="buscarCliente"><i class="fas fa-search"></i> Buscar
                                                            /button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre">Nombre del cliente</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="profesion">Profesión</label>
                                                <input type="text" value="{{ old('profesion') }}" name="profesion"
                                                    id="profesion" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="text" value="{{ old('telefono') }}" name="telefono"
                                                    id="telefono" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Correo electrónico</label>
                                                <input type="email" value="{{ old('email') }}" name="email"
                                                    id="email" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <input type="text" value="{{ old('direccion') }}" name="direccion"
                                                    id="direccion" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion_laboral">Dirección laboral</label>
                                                <input type="text" value="{{ old('direccion_laboral') }}"
                                                    name="direccion_laboral" id="direccion_laboral" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Ingresos</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select name="tipo_ingreso" id="tipo_ingreso"
                                                            class="form-control" required onchange="toggleFields()">
                                                            <option value="">Seleccione una opción...</option>
                                                            <option value="diario"
                                                                {{ old('tipo_credito') == 'diario' ? 'selected' : '' }}>
                                                                Diario</option>
                                                            <option value="mensual"
                                                                {{ old('tipo_credito') == 'mensual' ? 'selected' : '' }}>
                                                                Mensual</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <input type="text" value="{{ old('direccion') }}"
                                                            name="direccion" id="inputMensual" class="form-control"
                                                            required placeholder="monto en S/.">
                                                        <button type="button" class="btn btn-primary" id="botonDiario"
                                                            data-toggle="modal" data-target="#miModal"><i
                                                                class="bi bi-floppy2"></i> Registrar Diario</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Egresos</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select name="tipo_Egreso" id="tipo_Egreso" class="form-control"
                                                            onchange="toggleFields()">
                                                            <option value="">Seleccione una opción...</option>
                                                            <option value="diario"
                                                                {{ old('tipo_Egreso') == 'diario' ? 'selected' : '' }}>
                                                                Diario</option>
                                                            <option value="mensual"
                                                                {{ old('tipo_Egreso') == 'mensual' ? 'selected' : '' }}>
                                                                Mensual</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <input type="text" value="{{ old('inputMensualEgresos') }}"
                                                            name="inputMensualEgresos" id="inputMensualEgresos"
                                                            class="form-control"  placeholder="monto en S/.">
                                                        <button type="button" class="btn btn-primary"
                                                            id="botonDiarioEgresos" data-toggle="modal"
                                                            data-target="#miModalEgresos"><i class="bi bi-floppy2"></i>
                                                            Registrar Diario</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Descripción del negocio</label>
                                                <input type="email" value="{{ old('email') }}" name="email"
                                                    id="email" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div> --}}

                            {{-- //Datos del credito --}}
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del Credito</h3>
                                </div>
                                <div class="card-body">

                                    <!-- Campos para crédito individual -->


                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="recurrencia">Recurrencia</label>
                                                <select name="recurrencia" id="recurrencia" class="form-control" required onchange="toggleFields()">
                                                    <option value="">Seleccione una opción...</option>
                                                    <option value="mensual" {{ old('recurrencia') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                                    <option value="quincenal" {{ old('recurrencia') == 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tasa_interes">Tasa de interés (%)</label>
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
                                                <input type="text" value="{{ old('fecha_desembolso') }}" name="fecha_desembolso" id="fecha_desembolso" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="monto">Monto total (S/.)</label>
                                                <input type="text" value="{{ old('monto') }}" name="monto" id="monto" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="descripcion_garantia">Descripcion de la Garantia</label>
                                                <input type="text" name="descripcion_garantia"
                                                    id="descripcion_garantia" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="valor_mercado">Valor del mercado (S/.)</label>
                                                <input type="number" name="valor_mercado" id="valor_mercado"
                                                    class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="valor_realizacion">Valor de realización (S/.)</label>
                                                <input type="number" name="valor_realizacion" id="valor_realizacion"
                                                    class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="valor_gravamen">Valor de Gravamen (S/.)</label>
                                                <input type="number" name="valor_gravamen" id="valor_gravamen"
                                                    class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="dni_pdf">Archivo en pdf</label>
                                                <input type="file" name="dni_pdf" accept=".pdf"
                                                    class="form-control-file">
                                                @error('dni_pdf')
                                                    <small style="color: red">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div> --}}


                                    </div>


                                </div>
                            </div>


                            {{-- //Registro de detalle de sus ventas --}}
                            {{-- <div class="card card-outline card-primary" id="detalle_negocio">
                                <div class="card-header">
                                    <h3 class="card-title">Detalle de su negocio</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="producto_descripcion_0">Descripción del producto</label>
                                                <input type="text" id="producto_descripcion" class="form-control"
                                                    >
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="precio_compra_0">Precio de Compra</label>
                                                <input type="number" id="precio_compra" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="precio_venta_0">Precio de Venta</label>
                                                <input type="number" id="precio_venta" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_0">Cantidad</label>
                                                <input type="number" id="cantidad" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2"><button type="button" onclick="agregarProducto()"
                                                class="btn btn-primary">Añadir Producto</button></div>
                                    </div>

                                    <hr>
                                    <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Precio de Compra</th>
                                                <th>Precio de Venta</th>
                                                <th>Cantidad</th>
                                                <th>Margen (en %)</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaCuerpo">
                                            <!-- Las filas se agregarán aquí dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}

                            {{-- //Registro de Proyeccionres de ventas --}}


                            {{-- <div class="card card-outline card-primary" id="detalle_negocio">
                                <div class="card-header">
                                    <h3 class="card-title">Proyecciones de ventas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="producto_descripcion_0">Descripción del producto</label>
                                                <input type="text" id="producto_descripcion" class="form-control"
                                                    >
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="unidad_medida">Unidad de medida</label>
                                                <select name="unidad_medida" id="unidad_medida" class="form-control" >
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
                                                <input type="number" id="frecuencia_compra" name="frecuencia_compra" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="unidades_compradas">Unidades compradas</label>
                                                <input type="number" id="unidades_compradas" name="unidades_compradas" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="unidades_vendidas">Unidades Vendidas</label>
                                                <input type="number" id="unidades_vendidas" name="unidades_vendidas" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="stock_verificado_inspeccion">Stock verificado inspección</label>
                                                <input type="number" id="stock_verificado_inspeccion" name="stock_verificado_inspeccion" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="precio_compra_0">Precio de Compra S/.</label>
                                                <input type="number" id="precio_compra" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="precio_venta_0">Precio de Venta S/.</label>
                                                <input type="number" id="precio_venta" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2"><button type="button" onclick="agregarProducto()"
                                                class="btn btn-primary">Añadir Producto</button></div>
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
                                            <tbody id="tablaCuerpo">
                                                <!-- Las filas se agregarán aquí dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div> --}}


                            {{-- Regsitro de boletas solo para produccion y servicio --}}
                            {{-- <div class="card card-outline card-primary" id="registro_boletas">
                                <div class="card-header">
                                    <h3 class="card-title">Registro de Boletas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="numero_boleta">Numero de Boleta</label>
                                                <input type="text" id="numero_boleta" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="monto_boleta">Monto Boleta</label>
                                                <input type="number" id="monto_boleta" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="descuento_boleta">Descuento</label>
                                                <input type="number" id="descuento_boleta" class="form-control"
                                                    >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" onclick="agregarBoleta()"
                                                class="btn btn-primary">Añadir Boleta</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <table class="table table-striped table-hover table-bordered" id="datosTabla">
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
                            </div> --}}


                            {{-- //Registro de Gastos a Producir --}}
                            {{-- <div class="card card-outline card-info" id="registro_gastos_producir">
                                <div class="card-header">
                                    <h3 class="card-title">Registro de Gastos a Producir</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="nombre_actividad">Nombre de actividad</label>
                                                <input type="text" id="nombre_actividad" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_terreno">Cantidad de terreno (hectáreas)</label>
                                                <input type="number" id="cantidad_terreno" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_terreno">Produccion Total (Kg)</label>
                                                <input type="number" id="cantidad_terreno" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_terreno">Precio (kg)</label>
                                                <input type="number" id="cantidad_terreno" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_terreno">Total en soles</label>
                                                <input type="number" id="cantidad_terreno" class="form-control" >
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="descripcion_gasto">Descripción del insumo</label>
                                                <input type="text" id="descripcion_gasto_producir" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="precio_unitario_gasto">Precio unitario</label>
                                                <input type="number" id="precio_unitario_gasto_producir" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_gasto">Cantidad</label>
                                                <input type="number" id="cantidad_gasto_producir" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" onclick="agregarGastoProducir()" class="btn btn-info">Añadir Gasto</button>
                                        </div>
                                    </div>
                                    

                                    <hr>
                                    <table class="table table-striped table-hover table-bordered" id="datosTablaGastos">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Descripción del insumo</th>
                                                <th>Precio unitario</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaGastosProducir">
                                            <!-- Las filas se agregarán aquí dinámicamente -->
                                        </tbody>
                                    </table>
                                  
                                </div>
                            </div> --}}

                            {{-- //Registro de inventario del producto --}}
                            {{-- <div class="card card-outline card-warning" id="inventario_producto">
                                <div class="card-header">
                                    <h3 class="card-title">Registro de inventario</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="descripcion_producto">Descripción del producto</label>
                                                <input type="text" id="descripcion_producto" class="form-control"
                                                    >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="precio_unitario">Precio unitario</label>
                                                <input type="number" id="precio_unitario" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_producto">Cantidad</label>
                                                <input type="number" id="cantidad_producto" class="form-control"
                                                    >
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="button" onclick="agregarInventario()"
                                                class="btn btn-warning">Añadir Producto</button>
                                        </div>
                                    </div>

                                    <hr>
                                    <table class="table table-striped table-hover table-bordered"
                                        id="datosTablaInventario">
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
                                        <strong>Total de inventario:</strong> <span id="totalMonto"
                                            style="font-size: 18px;">0.00</span>
                                    </div>
                                </div>
                            </div> --}}





                            {{-- //Registro de Gastos Operativos --}}
                            {{-- <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Registro de Gastos Operativos</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="descripcion_gasto">Descripción</label>
                                                <input type="text" id="descripcion_gasto" class="form-control"
                                                    >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="precio_unitario_gasto">Precio unitario</label>
                                                <input type="number" id="precio_unitario_gasto" class="form-control"
                                                    >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cantidad_gasto">Cantidad</label>
                                                <input type="number" id="cantidad_gasto" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="button" onclick="agregarGasto()" class="btn btn-info">Añadir
                                                Gasto</button>
                                        </div>
                                    </div>

                                    <hr>
                                    <table class="table table-striped table-hover table-bordered" id="datosTablaGastos">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Precio unitario</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaGastos">
                                            <!-- Las filas se agregarán aquí dinámicamente -->
                                        </tbody>
                                    </table>
                                    <div class="text" style="background-color: #f0f0f0; padding: 10px;">
                                        <strong>Total de Gastos Operativos:</strong> <span id="totalGatosOperativos"
                                            style="font-size: 18px;">0.00</span>
                                    </div>
                                </div>
                            </div> --}}

                            

                            {{-- Deudas Financieros --}}
                            {{-- <div class="card card-outline card-warning">
                                <div class="card-header" style="display:flex;">
                                    <h3 class="card-title">Deudas Financieras</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="documento_identidad">Nombre de la entidad 1</label>
                                                <input type="text" id="documento_identidad"
                                                    value="{{ old('documento_identidad') }}" name="documento_identidad"
                                                    class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Saldo Capital</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Cuota</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tiempo restante (meses)</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>



                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="documento_identidad">Nombre de la entidad 1</label>
                                                <input type="text" id="documento_identidad"
                                                    value="{{ old('documento_identidad') }}" name="documento_identidad"
                                                    class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Saldo Capital</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Cuota</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tiempo restante (meses)</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>



                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="documento_identidad">Nombre de la entidad 1</label>
                                                <input type="text" id="documento_identidad"
                                                    value="{{ old('documento_identidad') }}" name="documento_identidad"
                                                    class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Saldo Capital</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Cuota</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tiempo restante (meses)</label>
                                                <input type="text" value="{{ old('nombre') }}" name="nombre"
                                                    id="nombre" class="form-control" >
                                            </div>
                                        </div>



                                    </div>


                                </div>
                            </div> --}}



                        </div>




                        {{-- <div id="grupalFields" style="display:none;">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del Credito</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="nombre">Nombre del Grupo</label>
                                                <input type="text" value="{{ old('nombre_grupo') }}"
                                                    name="nombre_grupo" id="nombre_grupo" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="nombre">Cantidad de integrantes</label>
                                                <input type="number" value="{{ old('cantidad_grupo') }}"
                                                    name="cantidad_grupo" id="cantidad_grupo" class="form-control"
                                                    >
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Recurrencia</label>
                                                <select name="tipo_credito" id="tipo_credito" class="form-control"
                                                     onchange="toggleFields()">
                                                    <option value="">Seleccione una opción...</option>
                                                    <option value="individual"
                                                        {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                                        Catorcenal</option>
                                                    <option value="individual"
                                                        {{ old('tipo_credito') == 'individual' ? 'selected' : '' }}>
                                                        Mensual</option>
                                                    <option value="grupal"
                                                        {{ old('tipo_credito') == 'grupal' ? 'selected' : '' }}>
                                                        Anual</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tasa de interes (%)</label>
                                                <input type="text" value="{{ old('tasa_interes') }}"
                                                    name="tasa_interes" id="tasa_interes" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Tiempo del credito</label>
                                                <input type="text" value="{{ old('tiempo_credito') }}" name="nombre"
                                                    id="tiempo_credito'" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Monto total (S/.)</label>
                                                <input type="text" value="{{ old('monto') }}" name="nombre"
                                                    id="monto" class="form-control" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Descripcion de la Garantia</label>
                                                <input type="text" value="{{ old('tasa_interes') }}"
                                                    name="tasa_interes" id="" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nombre">Valorización (S/.)</label>
                                                <input type="text" value="{{ old('tasa_interes') }}"
                                                    name="tasa_interes" id="" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="dni_pdf">Archivo en pdf</label>
                                                <input type="file" name="dni_pdf" accept=".pdf"
                                                    class="form-control-file">
                                                @error('dni_pdf')
                                                    <small style="color: red">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="dni_pdf">Foto Grupal</label>
                                                <input type="file" name="dni_pdf" accept=".pdf"
                                                    class="form-control-file">
                                                @error('Foto')
                                                    <small style="color: red">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>


                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Datos de los clientes</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" id="dni" name="dni"
                                                    placeholder="Agregar por Dni" class="form-control" >
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button"
                                                        id="agregarButton">Agregar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                        <thead class="thead-blue">
                                            <tr>
                                                <th>Nombre del cliente</th>
                                                <th>Profesión</th>
                                                <th>Teléfono</th>
                                                <th>Dirección</th>
                                                <th>monto</th>
                                                <th>Acciones</th>

                                            </tr>
                                        </thead>
                                        <tbody id="tablaCuerpo">
                                            <!-- Las filas se agregarán aquí dinámicamente -->
                                        </tbody>
                                    </table>




                                </div>
                            </div>



                        </div> --}}

                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('admin/creditos') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i>
                                    Guardar registro</button>
                            </div>
                        </div>
                        <hr>

                    </form>
                </div>

            </div>

        </div>
    </div>

    {{-- MENU FLOTANTE --}}
    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registro de Ingresos Semanal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="weeklyIncomeForm">
                        <div class="form-group">
                            <label for="monday">Lunes:</label>
                            <input type="number" class="form-control" id="monday" name="monday"
                                placeholder="Ingresos del lunes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="tuesday">Martes:</label>
                            <input type="number" class="form-control" id="tuesday" name="tuesday"
                                placeholder="Ingresos del martes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="wednesday">Miércoles:</label>
                            <input type="number" class="form-control" id="wednesday" name="wednesday"
                                placeholder="Ingresos del miércoles (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="thursday">Jueves:</label>
                            <input type="number" class="form-control" id="thursday" name="thursday"
                                placeholder="Ingresos del jueves (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="friday">Viernes:</label>
                            <input type="number" class="form-control" id="friday" name="friday"
                                placeholder="Ingresos del viernes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="saturday">Sábado:</label>
                            <input type="number" class="form-control" id="saturday" name="saturday"
                                placeholder="Ingresos del sábado (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="sunday">Domingo:</label>
                            <input type="number" class="form-control" id="sunday" name="sunday"
                                placeholder="Ingresos del domingo (S/.)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="submitWeeklyIncome()">Guardar
                        Cambios</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MENU FLOTANTE --}}
    <div class="modal fade" id="miModalEgresos" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registro de Egresos Semanal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="weeklyIncomeForm">
                        <div class="form-group">
                            <label for="monday">Lunes:</label>
                            <input type="number" class="form-control" id="monday" name="monday"
                                placeholder="Ingresos del lunes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="tuesday">Martes:</label>
                            <input type="number" class="form-control" id="tuesday" name="tuesday"
                                placeholder="Ingresos del martes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="wednesday">Miércoles:</label>
                            <input type="number" class="form-control" id="wednesday" name="wednesday"
                                placeholder="Ingresos del miércoles (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="thursday">Jueves:</label>
                            <input type="number" class="form-control" id="thursday" name="thursday"
                                placeholder="Ingresos del jueves (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="friday">Viernes:</label>
                            <input type="number" class="form-control" id="friday" name="friday"
                                placeholder="Ingresos del viernes (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="saturday">Sábado:</label>
                            <input type="number" class="form-control" id="saturday" name="saturday"
                                placeholder="Ingresos del sábado (S/.)">
                        </div>
                        <div class="form-group">
                            <label for="sunday">Domingo:</label>
                            <input type="number" class="form-control" id="sunday" name="sunday"
                                placeholder="Ingresos del domingo (S/.)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="submitWeeklyIncome()">Guardar
                        Cambios</button>
                </div>
            </div>
        </div>
    </div>








    {{-- para filtrar por tipo de credito --}}
    <script>
        function toggleFields() {
            var selection = document.getElementById('tipo_producto').value;
            var individualFields = document.getElementById('individualFields');
            var grupalFields = document.getElementById('grupalFields');
            if (selection === 'microempresa' || selection === 'agricola' || selection === 'consumo') {
                individualFields.style.display = 'block';
                // grupalFields.style.display = 'none';
            } 
            // else if (selection === 'grupal') {
            //     individualFields.style.display = 'none';
            //     grupalFields.style.display = 'block';
            // } else {
            //     individualFields.style.display = 'none';
            //     grupalFields.style.display = 'none';
            // }

            // var selectionTipoCredito = document.getElementById('tipo_credito').value;
            // var inventario_producto = document.getElementById('inventario_producto');

            // if (selectionTipoCredito === 'servicio') {
            //     inventario_producto.style.display = 'none';
            // } else {
            //     inventario_producto.style.display = 'block';
            // }


            // if (selectionTipoCredito === 'servicio' && selection === 'consumo') {
            //     detalle_negocio.style.display = 'none';
            //     registro_boletas.style.display = 'block';
            // } else {
            //     detalle_negocio.style.display = 'block';
            //     registro_boletas.style.display = 'none';
            // }

            // if (selection === 'agricola') {
            //     registro_gastos_producir.style.display = 'block';
            //     detalle_negocio.style.display = 'none';
            // } else {
            //     registro_gastos_producir.style.display = 'none';
            // }






            // var selection1 = document.getElementById('tipo_ingreso').value;
            // var botonDiario = document.getElementById('botonDiario');
            // var inputMensual = document.getElementById('inputMensual');



            // if (selection1 === 'diario') {
            //     botonDiario.style.display = 'block'; // Muestra el botón para 'Diario'
            //     inputMensual.style.display = 'none'; // Oculta el input para 'Mensual'
            // } else if (selection1 === 'mensual') {
            //     botonDiario.style.display = 'none'; // Oculta el botón para 'Diario'
            //     inputMensual.style.display = 'block'; // Muestra el input para 'Mensual'
            // } else {
            //     // Esconde ambos si se selecciona la opción por defecto o ninguna
            //     botonDiario.style.display = 'none';
            //     inputMensual.style.display = 'none';
            // }

            // var selectionEgreso = document.getElementById('tipo_Egreso').value;
            // var botonDiarioEgresos = document.getElementById('botonDiarioEgresos');
            // var inputMensualEgresos = document.getElementById('inputMensualEgresos');

            // if (selectionEgreso === 'diario') {
            //     botonDiarioEgresos.style.display = 'block'; // Muestra el botón para 'Diario'
            //     inputMensualEgresos.style.display = 'none'; // Oculta el input para 'Mensual'
            // } else if (selectionEgreso === 'mensual') {
            //     botonDiarioEgresos.style.display = 'none'; // Oculta el botón para 'Diario'
            //     inputMensualEgresos.style.display = 'block'; // Muestra el input para 'Mensual'
            // } else {
            //     // Esconde ambos si se selecciona la opción por defecto o ninguna
            //     botonDiarioEgresos.style.display = 'none';
            //     inputMensualEgresos.style.display = 'none';
            // }



        }

        // Call toggleFields on document ready to handle form repopulation on page reload (with old inputs)
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script> 



    {{-- busca por cliente --}}
    <script>
        $(document).ready(function() {
            console.log("presionar", )
            $('#buscarCliente').click(function() {
                var documentoIdentidad = $('#documento_identidad').val(); // Captura el valor del DNI
                console.log("Intentando buscar cliente con DNI:",
                    documentoIdentidad); // Muestra el valor enviado en consola

                $.ajax({
                    url: '{{ route('creditos.buscardni') }}',
                    type: 'GET',
                    data: {
                        documento_identidad: documentoIdentidad
                    },
                    success: function(response) {
                        $('#nombre').val(response.nombre || ''); // Ya existente
                        $('#telefono').val(response.telefono || ''); // Nuevo campo
                        $('#email').val(response.email || ''); // Nuevo campo
                        $('#direccion').val(response.direccion || ''); // Nuevo campo
                        $('#direccion_laboral').val(response.direccion_laboral ||
                            ''); // Nuevo campo
                        $('#profesion').val(response.profesion || ''); // Nuevo campo
                    },
                    error: function(xhr) {
                        console.error("Error al recuperar información: " + xhr.statusText);
                        // Limpiar todos los campos si hay un error
                        $('#nombre, #telefono, #email, #direccion, #direccion_laboral').val('');
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            let cantidadMaxima = 0; // Variable para almacenar el máximo de personas permitidas
            let contadorPersonas = 0; // Contador de personas agregadas

            // Actualizar la cantidad máxima cada vez que cambie el input
            $('#cantidad_grupo').change(function() {
                cantidadMaxima = parseInt($(this).val(), 10); // Actualiza la cantidad máxima permitida
                validarBotonAgregar(); // Validar si el botón debe estar habilitado o deshabilitado
            });

            $('#agregarButton').click(function() {
                if (contadorPersonas < cantidadMaxima) {
                    var documentoIdentidad = $('#dni').val();
                    $.ajax({
                        url: '{{ route('creditos.buscardni') }}',
                        type: 'GET',
                        data: {
                            documento_identidad: documentoIdentidad
                        },
                        success: function(response) {
                            $('#datosTabla tbody').append(
                                '<tr>' +
                                '<td>' + (response.nombre || '') + '</td>' +
                                '<td>' + (response.profesion || '') + '</td>' +
                                '<td>' + (response.telefono || '') + '</td>' +
                                '<td>' + (response.direccion || '') + '</td>' +
                                '<td>' + ('200') + '</td>' +
                                '<td>' +
                                '<button class="btn btn-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>' +
                                '</td>' +
                                '</tr>'
                            ); // Añade la fila al final del cuerpo de la tabla
                            $('#dni').val(''); // Limpiar campo DNI
                            contadorPersonas++; // Incrementar el contador de personas
                            validarBotonAgregar(); // Revalidar el estado del botón
                        },
                        error: function(xhr) {
                            console.error("Error: " + xhr.statusText);
                        }
                    });
                }
            });



            $('#datosTabla').on('click', '.removeRow', function() {
                $(this).closest('tr').remove(); // Elimina la fila más cercana en el DOM a este botón
                contadorPersonas--; // Decrementa el contador al quitar una persona
                validarBotonAgregar(); // Revisa nuevamente el estado del botón Agregar
            });

            function validarBotonAgregar() {
                if (contadorPersonas >= cantidadMaxima) {
                    $('#agregarButton').prop('disabled', true); // Deshabilitar el botón si se alcanza el máximo
                } else {
                    $('#agregarButton').prop('disabled',
                        false); // Habilitar el botón si aún no se alcanza el máximo
                }
            }
        });
    </script>


    {{-- //para agregar fila --}}
    <script>
        function agregarFila(tablaId, campos) {
            var tabla = document.getElementById(tablaId);
            var fila = tabla.insertRow();

            campos.forEach(function(valor) {
                var celda = fila.insertCell();
                celda.innerHTML = valor;
            });

            // Agregar evento al botón de eliminar
            var botonEliminar = document.createElement('button');
            botonEliminar.type = 'button';
            botonEliminar.className = 'btn btn-danger';
            botonEliminar.textContent = 'Eliminar';
            botonEliminar.onclick = function() {
                eliminarFila(this);
            };

            var celdaEliminar = fila.insertCell();
            celdaEliminar.appendChild(botonEliminar);

            // Limpiar campos de entrada
            limpiarCamposEntrada(campos);
        }

        function limpiarCamposEntrada(campos) {
            campos.forEach(function(id) {
                var elemento = document.getElementById(id);
                if (elemento) {
                    elemento.value = '';
                }
            });
        }

        function eliminarFila(boton) {
            var fila = boton.parentNode.parentNode;
            fila.parentNode.removeChild(fila);
        }

        function calcularMargen(precioVenta, precioCompra) {
            return (((precioVenta - precioCompra) / precioCompra) * 100).toFixed(2) + '%';
        }

        function agregarProducto() {
            var descripcion = document.getElementById('producto_descripcion').value;
            var unidadMedida = document.getElementById('unidad_medida').value;
            var frecuenciaCompra = document.getElementById('frecuencia_compra').value;
            var unidadesCompradas = document.getElementById('unidades_compradas').value;
            var unidadesVendidas = document.getElementById('unidades_vendidas').value;
            var stockVerificadoInspeccion = document.getElementById('stock_verificado_inspeccion').value;
            var precioCompra = document.getElementById('precio_compra').value;
            var precioVenta = document.getElementById('precio_venta').value;
            var inventario_valorizado = stockVerificadoInspeccion * precioCompra;
            var uni_vendidad_mes = (30/frecuenciaCompra)*unidadesVendidas;
            var ingresos_mes_venta = uni_vendidad_mes * precioVenta;
            var uni_compra_mes = (30/frecuenciaCompra)*unidadesCompradas;
            var ingresos_mes_compra = uni_compra_mes*precioCompra;
            var margen_bruto_mensual = ingresos_mes_venta - ingresos_mes_compra;
            var margen = (margen_bruto_mensual / ingresos_mes_venta)*100;

            agregarFila('tablaCuerpo', [
                descripcion, 
                unidadMedida, 
                frecuenciaCompra, 
                unidadesCompradas, 
                unidadesVendidas, 
                stockVerificadoInspeccion, 
                precioCompra, 
                precioVenta, 
                inventario_valorizado.toFixed(2), 
                uni_vendida_mes.toFixed(2), 
                ingresos_mes_venta.toFixed(2), 
                uni_compra_mes.toFixed(2), 
                ingresos_mes_compra.toFixed(2), 
                margen_bruto_mensual.toFixed(2), 
                (margen).toFixed(2) + '%']);

            limpiarCamposEntrada([
                'producto_descripcion', 
                'unidad_medida', 
                'frecuencia_compra', 
                'unidades_compradas', 
                'unidades_vendidas', 
                'stock_verificado_inspeccion', 
                'precio_compra', 
                'precio_venta' ]);
        }


        function agregarInventario() {
            var descripcion = document.getElementById('descripcion_producto').value;
            var precioUnitario = document.getElementById('precio_unitario').value;
            var cantidad = document.getElementById('cantidad_producto').value;
            var montoTotal = (precioUnitario * cantidad).toFixed(2);

            agregarFila('datosTablaInventario', [descripcion, precioUnitario, cantidad, montoTotal]);
            limpiarCamposEntrada(['descripcion_producto', 'precio_unitario', 'cantidad_producto']);

            // Actualizar el total sumando el monto total del nuevo producto
            var totalActual = parseFloat(document.getElementById('totalMonto').textContent);
            var nuevoTotal = totalActual + parseFloat(montoTotal);
            document.getElementById('totalMonto').textContent = nuevoTotal.toFixed(2);

        }

        function agregarGasto() {
            var descripcion = document.getElementById('descripcion_gasto').value;
            var precioUnitario = document.getElementById('precio_unitario_gasto').value;
            var cantidad = document.getElementById('cantidad_gasto').value;
            var montoTotal = (precioUnitario * cantidad).toFixed(2);

            agregarFila('datosTablaGastos', [descripcion, precioUnitario, cantidad, montoTotal]);
            limpiarCamposEntrada(['descripcion_gasto', 'precio_unitario_gasto', 'cantidad_gasto']);

            // Actualizar el total sumando el monto total del nuevo producto
            var totalActual = parseFloat(document.getElementById('totalGatosOperativos').textContent);
            var nuevoTotal = totalActual + parseFloat(montoTotal);
            document.getElementById('totalGatosOperativos').textContent = nuevoTotal.toFixed(2);
        }

        function agregarBoleta() {
            var descripcion = document.getElementById('numero_boleta').value;
            var precioUnitario = document.getElementById('monto_boleta').value;
            var cantidad = document.getElementById('descuento_boleta').value;
            var montoTotal = (precioUnitario - cantidad).toFixed(2);

            agregarFila('datos_tabla_boleta', [descripcion, precioUnitario, cantidad, montoTotal]);
            limpiarCamposEntrada(['numero_boleta', 'monto_boleta', 'descuento_boleta']);

            // Actualizar el total sumando el monto total del nuevo producto
            var totalActual = parseFloat(document.getElementById('totalGatosOperativos').textContent);
            var nuevoTotal = totalActual + parseFloat(montoTotal);
            document.getElementById('totalGatosOperativos').textContent = nuevoTotal.toFixed(2);
        }

        function agregarGastoProducir() {
            var descripcion = document.getElementById('descripcion_gasto_producir').value;
            var precioUnitario = document.getElementById('precio_unitario_gasto_producir').value;
            var cantidad = document.getElementById('cantidad_gasto_producir').value;
            var montoTotal = (precioUnitario * cantidad).toFixed(2);

            agregarFila('tablaGastosProducir', [descripcion, precioUnitario, cantidad, montoTotal]);
            limpiarCamposEntrada(['descripcion_gasto_producir', 'precio_unitario_gasto_producir', 'cantidad_gasto_producir']);

            // // Actualizar el total sumando el monto total del nuevo producto
            // var totalActual = parseFloat(document.getElementById('totalGatosOperativos').textContent);
            // var nuevoTotal = totalActual + parseFloat(montoTotal);
            // document.getElementById('totalGatosOperativos').textContent = nuevoTotal.toFixed(2);
        }
    </script>





@endsection
