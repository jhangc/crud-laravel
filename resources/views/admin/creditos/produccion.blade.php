<div class="row ">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos Generales Credito Producción</h3>
            </div>
            <div class="card-body">
                <form enctype="multipart/form-data" id="prestamoForm" name="prestamoForm">
                    @csrf
                    <div class="row">
                        
                        <div class="col-md-4">
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
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="subproducto">SubProductos</label>
                                <select name="subproducto" id="subproducto" class="form-control" required
                                    onchange="toggleFields()">
                                    <option value="">Seleccione una opción...</option>
                                    <option value="creditoempresarial"
                                        {{ old('subproducto') == 'creditoempresarial' ? 'selected' : '' }}>Crédito
                                        empresarial</option>
                                    <option value="creditoempresarialhipotecario"
                                        {{ old('subproducto') == 'creditoempresarialhipotecario' ? 'selected' : '' }}>
                                        Crédito empresarial con garantía hipotecaria empresarial</option>
                                    <option value="palmoagro"
                                        {{ old('subproducto') == 'palmoagro' ? 'selected' : '' }}>Palmo agro con
                                        garantía hipotecaria agrícola</option>
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
                                            <input type="text" id="documento_identidad" name="documento_identidad"
                                                class="form-control">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="buscarCliente"><i class="fas fa-search"></i> Buscar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del cliente</label>
                                        <input type="text" value="{{ old('nombre') }}" name="nombre" id="nombre"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profesion">Profesión</label>
                                        <input type="text" value="{{ old('profesion') }}" name="profesion"
                                            id="profesion" class="form-control">
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
                                            id="direccion" class="form-control">
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
                                    <div class="form-group" id="recurencia_individual">
                                        <label for="recurrencia">Recurrencia</label>
                                        <select name="recurrencia" id="recurrencia" class="form-control">
                                            <option value="">Seleccione una opción...</option>
                                            <option value="mensual"
                                                {{ old('recurrencia') == 'mensual' ? 'selected' : '' }}>Mensual
                                            </option>
                                            <option value="quincenal"
                                                {{ old('recurrencia') == 'quincenal' ? 'selected' : '' }}>Quincenal
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

                    <div class="card card-outline card-warning" id="registro_ventas_diarias">
                        <div class="card-header">
                            <h3 class="card-title">Registro de ventas diarias</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="dia_venta">Dia</label>
                                        <select id="dia_venta" name="dia_venta" class="form-control">
                                            <option value="lunes">Lunes</option>
                                            <option value="martes">Martes</option>
                                            <option value="miercoles">Miércoles</option>
                                            <option value="jueves">Jueves</option>
                                            <option value="viernes">Viernes</option>
                                            <option value="sabado">Sábado</option>
                                            <option value="domingo">Domingo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="venta_minima">Venta mínima (S/.)</label>
                                        <input type="number" id="venta_minima" name="venta_minima"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="venta_maxima">Venta máxima (S/.)</label>
                                        <input type="number" id="venta_maxima" name="venta_maxima"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="btn_anadir_venta" onclick="agregarVentaTabla()"
                                        class="btn btn-warning btnprestamo">Añadir Venta</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered"
                                id="tabla_registro_ventas_diarias">
                                <thead class="thead-blue">
                                    <tr>
                                        <th>Dia</th>
                                        <th>Venta mínima</th>
                                        <th>Venta máxima</th>
                                        <th>Promedio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="ventas_diarias">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Total de ventas semana:</strong></td>
                                        <td><input type="text" id="total_ventas_semana" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Total de ventas quincena:</strong></td>
                                        <td><input type="text" id="total_ventas_quincena" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Total de ventas mes:</strong></td>
                                        <td><input type="text" id="total_ventas_mes" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>% Venta al crédito:</strong></td>
                                        <td><input type="text" id="porcentaje_venta_credito"
                                                name="porcentaje_venta_credito" class="form-control" value="0.00">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Venta al crédito:</strong></td>
                                        <td><input type="text" id="venta_credito" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card card-outline card-info" id="gastos_produccion_agricola">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Gastos a Producir</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nombre_actividad">Nombre de actividad</label>
                                        <input type="text" id="nombre_actividad" name="nombre_actividad"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_terreno">Cantidad de terreno (hectáreas)</label>
                                        <input type="number" id="cantidad_terreno" name="cantidad_terreno"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="produccion_total">Producción Total (Kg)</label>
                                        <input type="number" id="produccion_total" onchange="agricolacalculo()"
                                            name="produccion_total" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="precio_kg">Precio (kg)</label>
                                        <input type="number" id="precio_kg" name="precio_kg"
                                            onchange="agricolacalculo()" class="form-control">
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
                                        <input type="number" id="precio_unitario_gasto_producir"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_gasto_producir">Cantidad</label>
                                        <input type="number" id="cantidad_gasto_producir" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarGastoProducir()"
                                        class="btn btn-info btnprestamo">Añadir Gasto</button>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="unidad_medida">Unidad de medida</label>
                                        <select name="unidad_medida" id="unidad_medida" class="form-control">
                                            <option value="und" selected>Unidades</option>
                                            <option value="qq">Quintales</option>
                                            <option value="kg">Kilos</option>
                                            <option value="m">metros</option>
                                            <option value="l">litros</option>
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precio_venta">Precio de Venta S/.</label>
                                        <input type="number" id="precio_venta" class="form-control">
                                    </div>
                                </div>

                                <!-- <div class="col-md-2">
                                    <button type="button" onclick="agregarProductoProyeccion()"
                                        class="btn btn-primary btnprestamo">Añadir Producto</button>
                                </div> -->

                                <div class="col-md-2">
                                    <button type="button" data-toggle="modal" data-target="#myModal"
                                        class="btn btn-primary btnprestamo">Añadir Producto</button>
                                </div>

                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="datosTabla">
                                    <thead class="thead-blue">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Compra/producción</th>
                                            <th>Precio Venta</th>
                                            <th>Unidad</th>
                                            <th>Utilidad por unidad</th>
                                            <th>Margen por unidad</th>
                                            <th>% proporción de las ventas</th>
                                            <th>Monto de venta</th>
                                            <th>Relación compra/venta</th>
                                            <th>Costo venta</th>
                                            <th>Utilidad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProyecciones">
                                        <!-- Aquí van las filas de la tabla -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td><strong>Total:</strong></td>
                                            <td><input type="text" id="total_proporción_ventas" class="form-control" value="0.00" readonly></td>
                                            <td><input type="text" id="total_monto:venta" class="form-control" value="0.00" readonly></td>
                                            <td colspan="1"></td>
                                            <td><input type="text" id="total_costo_venta" class="form-control" value="0.00" readonly></td>
                                            <td><input type="text" id="total_utilidad" class="form-control" value="0.00" readonly></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- The Modal -->
                    <div class="modal fade" id="myModal">
                        <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">
                                    <label for="producto">Producto:</label>
                                    <input type="text" id="producto" class="form-control d-inline-block w-25 mx-2">
                                    <label for="cantidad">Cantidad:</label>
                                    <input type="number" id="cantidad" class="form-control d-inline-block w-25 mx-2">
                                </h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Example Row 1 -->
                                    <tr>
                                        <td><input type="text" class="form-control" name="descripcion[]" placeholder="Descripción"></td>
                                        <td>
                                            <select class="form-control" name="unidad[]">
                                                <option value="und" selected>Unidades</option>
                                                <option value="qq">Quintales</option>
                                                <option value="kg">Kilos</option>
                                                <option value="m">metros</option>
                                                <option value="l">litros</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="cantidad[]" placeholder="Cantidad" step="1"></td>
                                        <td><input type="number" class="form-control" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01"></td>
                                        <td><input type="number" class="form-control" name="subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
                                    </tr>
                                    <!-- Example Row 2 -->
                                    <tr>
                                        <td><input type="text" class="form-control" name="descripcion[]" placeholder="Descripción"></td>
                                        <td>
                                            <select class="form-control" name="unidad[]">
                                                <option value="und" selected>Unidades</option>
                                                <option value="qq">Quintales</option>
                                                <option value="kg">Kilos</option>
                                                <option value="m">metros</option>
                                                <option value="l">litros</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="cantidad[]" placeholder="Cantidad" step="1"></td>
                                        <td><input type="number" class="form-control" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01"></td>
                                        <td><input type="number" class="form-control" name="subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
                                    </tr>
                                    <!-- Example Row 3 -->
                                    <tr>
                                        <td><input type="text" class="form-control" name="descripcion[]" placeholder="Descripción"></td>
                                        <td>
                                            <select class="form-control" name="unidad[]">
                                                <option value="und" selected>Unidades</option>
                                                <option value="qq">Quintales</option>
                                                <option value="kg">Kilos</option>
                                                <option value="m">metros</option>
                                                <option value="l">litros</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="cantidad[]" placeholder="Cantidad" step="1"></td>
                                        <td><input type="number" class="form-control" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01"></td>
                                        <td><input type="number" class="form-control" name="subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
                                    </tr>
                                    <!-- Example Row 4 -->
                                    <tr>
                                        <td><input type="text" class="form-control" name="descripcion[]" placeholder="Descripción"></td>
                                        <td>
                                            <select class="form-control" name="unidad[]">
                                                <option value="und" selected>Unidades</option>
                                                <option value="qq">Quintales</option>
                                                <option value="kg">Kilos</option>
                                                <option value="m">metros</option>
                                                <option value="l">litros</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="cantidad[]" placeholder="Cantidad" step="1"></td>
                                        <td><input type="number" class="form-control" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01"></td>
                                        <td><input type="number" class="form-control" name="subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
                                    </tr>
                                    <!-- Example Row 5 -->
                                    <tr>
                                        <td><input type="text" class="form-control" name="descripcion[]" placeholder="Descripción"></td>
                                        <td>
                                            <select class="form-control" name="unidad[]">
                                                <option value="und" selected>Unidades</option>
                                                <option value="qq">Quintales</option>
                                                <option value="kg">Kilos</option>
                                                <option value="m">metros</option>
                                                <option value="l">litros</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="cantidad[]" placeholder="Cantidad" step="1"></td>
                                        <td><input type="number" class="form-control" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01"></td>
                                        <td><input type="number" class="form-control" name="subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
                                    </tr>
                                    <!-- Example Row 6 -->
                                    <tr>
                                        <td><input type="text" class="form-control" name="descripcion[]" placeholder="Descripción"></td>
                                        <td>
                                            <select class="form-control" name="unidad[]">
                                                <option value="und" selected>Unidades</option>
                                                <option value="qq">Quintales</option>
                                                <option value="kg">Kilos</option>
                                                <option value="m">metros</option>
                                                <option value="l">litros</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="cantidad[]" placeholder="Cantidad" step="1"></td>
                                        <td><input type="number" class="form-control" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01"></td>
                                        <td><input type="number" class="form-control" name="subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total:</th>
                                        <th><input type="number" class="form-control" id="total" value="0.00" readonly></th>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                            <button type="button" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>

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
                                    <button type="button" onclick="agregarGastoOperativoc()"
                                        class="btn btn-info btnprestamo">Añadir Gasto</button>
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
                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td><strong>Total:</strong></td>
                                        <td><input type="text" id="totalGatosOperativos" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card card-outline card-warning" id="inventario_producto">
                        <div class="card-header">
                            <h3 class="card-title">Registro De inventario terminado</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="descripcion_producto_inventario">Descripción del Producto</label>
                                        <input type="text" id="descripcion_producto_inventario"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidad_medida">Unidad de medida</label>
                                        <select name="unidad_medida_inventario" id="unidad_medida_inventario"
                                            class="form-control">
                                            <option value="und" selected>Unidades</option>
                                            <option value="qq">Quintales</option>
                                            {{-- <option value="qq">Kilos</option> --}}
                                            <option value="kg">Kilos</option>
                                            <option value="m">metros</option>
                                            <option value="l">litros</option>
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
                                    <button type="button" onclick="agregarInventariotabla()"
                                        class="btn btn-warning btnprestamo">Añadir Producto</button>
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
                                        <td><input type="text" id="totalMontoInventario" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>

                    <div class="card card-outline card-warning" id="inventario_producto_proceso">
                        <div class="card-header">
                            <h3 class="card-title">Registro De inventario en proceso</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="descripcion_producto_inventario_proceso">Descripción del Producto</label>
                                        <input type="text" id="descripcion_producto_inventario_proceso" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="unidad_medida_inventario_proceso">Unidad de medida</label>
                                        <select name="unidad_medida_inventario_proceso" id="unidad_medida_inventario_proceso" class="form-control">
                                            <option value="und" selected>Unidades</option>
                                            <option value="qq">Quintales</option>
                                            <option value="kg">Kilos</option>
                                            <option value="m">metros</option>
                                            <option value="l">litros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="precio_unitario_inventario_proceso">Precio unitario</label>
                                        <input type="number" id="precio_unitario_inventario_proceso" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad_producto_inventario_proceso">Cantidad</label>
                                        <input type="number" id="cantidad_producto_inventario_proceso" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarInventarioprocesotabla()" class="btn btn-warning btnprestamo">Añadir Producto</button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-hover table-bordered" id="datosTablaInventario_proceso">
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
                                <tbody id="tablaInventario_proceso">
                                    <!-- Las filas se agregarán aquí dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td><strong>Total:</strong></td>
                                        <td><input type="text" id="totalMontoInventario_proceso" class="form-control" value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
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

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valor_mercado">Valor del mercado (S/.)</label>
                                        <input type="number" name="valor_mercado" id="valor_mercado"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valor_realizacion">Valor de realización (S/.)</label>
                                        <input type="number" name="valor_realizacion" id="valor_realizacion"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valor_gravamen">Valor de Gravamen (S/.)</label>
                                        <input type="number" name="valor_gravamen" id="valor_gravamen"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
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


                    <div class="card card-outline card-warning" id="gastos_familiares">
                        <div class="card-header">
                            <h3 class="card-title">Registro De Gastos Familiares</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="descripcion_producto_inventario">Descripción del gasto</label>
                                        <input type="text" id="descripcion_producto_inventario1"
                                            class="form-control">
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
                                        <input type="number" id="cantidad_producto_inventario1"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="agregarInventariotabla1()"
                                        class="btn btn-warning btnprestamo">Añadir Producto</button>
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
                                        <td><input type="text" id="totalgastosfamiliares" class="form-control"
                                                value="0.00" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

 

                    <div class="card card-outline card-warning" id="activos">
                        <div class="card-header">
                            <h3 class="card-title">Registro De Activos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cuentas_por_cobrar">Cuentas por cobrar</label>
                                        <input type="number" id="cuentas_por_cobrar" name="cuentas_por_cobrar"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="saldo_caja_bancos">Saldo en caja/bancos</label>
                                        <input type="number" id="saldo_caja_bancos" name="saldo_caja_bancos"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="adelanto_a_proveedores">Adelanto a proveedores</label>
                                        <input type="number" id="adelanto_a_proveedores"
                                            name="adelanto_a_proveedores" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="otros">Otros</label>
                                        <input type="number" id="otros" name="otros" class="form-control">
                                    </div>
                                </div>
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
                                    <button type="button" onclick="agregarDeudaFinanciera()"
                                        class="btn btn-primary btnprestamo">Añadir Deuda</button>
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
    let clientesArray = [];
    let totalMonto = 0;



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

    function agregarProductoProyeccion() {
        const descripcion = document.getElementById('producto_descripcion').value;
        const unidadMedida = document.getElementById('unidad_medida').value;
        const precioCompra = parseFloat(document.getElementById('precio_compra').value);
        const precioVenta = parseFloat(document.getElementById('precio_venta').value);
        const proporcion_ventas = parseFloat(document.getElementById('propocion_venta').value);
        const total_venta_mes = parseFloat(document.getElementById('total_ventas_mes').value);

        // Validación de valores
        if (isNaN(precioCompra) || isNaN(precioVenta) || isNaN(proporcion_ventas) || isNaN(total_venta_mes)) {
            alert("Por favor, asegúrese de que todos los campos numéricos estén llenos y sean válidos.");
            return;
        }

        const utilidad_unidad = precioVenta - precioCompra;
        const margen_unidad = utilidad_unidad / precioVenta;
        const monto_venta = total_venta_mes * (proporcion_ventas / 100);
        const relacion_compra_venta = precioCompra / precioVenta;
        const costo_venta = monto_venta * relacion_compra_venta;
        const utilidad = monto_venta - costo_venta;

        const proyeccion = {
            descripcion,
            unidadMedida,
            precioCompra,
            precioVenta,
            proporcion_ventas,
            total_venta_mes,
            utilidad_unidad,
            monto_venta,
            relacion_compra_venta,
            costo_venta,
            utilidad,
            margen_unidad
        };

        proyeccionesArray.push(proyeccion);
        console.log(proyeccionesArray);
        actualizarTablaProyecciones();
        limpiarCamposProyeccion();
    }

    function actualizarTablaProyecciones() {
        const tablaCuerpo = document.getElementById('tablaProyecciones');
        tablaCuerpo.innerHTML = '';

        let totalProporcionVentas = 0;
        let totalMontoVenta = 0;
        let totalCostoVenta = 0;
        let totalUtilidad = 0;

        proyeccionesArray.forEach((proyeccion, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
            <td><input type="text" class="form-control" value="${proyeccion.descripcion}" onchange="editarProyeccion(${index}, 'descripcion', this.value)"></td>
            <td><input type="text" class="form-control" value="${proyeccion.precioCompra}" onchange="editarProyeccion(${index}, 'precioCompra', this.value)"></td>
            <td><input type="number" class="form-control" value="${proyeccion.precioVenta}" onchange="editarProyeccion(${index}, 'precioVenta', this.value)"></td>
            <td><input type="text" class="form-control" value="${proyeccion.unidadMedida}" onchange="editarProyeccion(${index}, 'unidadMedida', this.value)"></td>
            <td>${proyeccion.utilidad_unidad.toFixed(2)}</td>
            <td>${(proyeccion.margen_unidad * 100).toFixed(2)}%</td>
            <td><input type="number" class="form-control" value="${proyeccion.proporcion_ventas}" onchange="editarProyeccion(${index}, 'proporcion_ventas', this.value)"></td>
            <td>${proyeccion.monto_venta.toFixed(2)}</td>
            <td>${(proyeccion.relacion_compra_venta * 100).toFixed(2)}%</td>
            <td>${proyeccion.costo_venta.toFixed(2)}</td>
            <td>${proyeccion.utilidad.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm" onclick="eliminarProyeccion(${index})"><i class="fa fa-trash"></i></button></td>
        `;

            totalProporcionVentas += proyeccion.proporcion_ventas;
            totalMontoVenta += proyeccion.monto_venta;
            totalCostoVenta += proyeccion.costo_venta;
            totalUtilidad += proyeccion.utilidad;
        });

        document.getElementById('total_proporción_ventas').value = totalProporcionVentas.toFixed(2);
        document.getElementById('total_monto:venta').value = totalMontoVenta.toFixed(2);
        document.getElementById('total_costo_venta').value = totalCostoVenta.toFixed(2);
        document.getElementById('total_utilidad').value = totalUtilidad.toFixed(2);
    }

    function editarProyeccion(index, campo, valor) {
        if (campo === 'precioCompra' || campo === 'precioVenta' || campo === 'proporcion_ventas') {
            valor = parseFloat(valor);
        }

        proyeccionesArray[index][campo] = valor;

        if (campo === 'precioCompra' || campo === 'precioVenta' || campo === 'proporcion_ventas' || campo ===
            'total_venta_mes') {
            recalcularProyeccion(index);
        }

        actualizarTablaProyecciones();
    }

    function recalcularProyeccion(index) {
        const proyeccion = proyeccionesArray[index];
        proyeccion.utilidad_unidad = proyeccion.precioVenta - proyeccion.precioCompra;
        proyeccion.margen_unidad = proyeccion.utilidad_unidad / proyeccion.precioVenta;
        proyeccion.monto_venta = proyeccion.total_venta_mes * (proyeccion.proporcion_ventas / 100);
        proyeccion.relacion_compra_venta = proyeccion.precioCompra / proyeccion.precioVenta;
        proyeccion.costo_venta = proyeccion.monto_venta * proyeccion.relacion_compra_venta;
        proyeccion.utilidad = proyeccion.monto_venta - proyeccion.costo_venta;
    }

    function eliminarProyeccion(index) {
        proyeccionesArray.splice(index, 1);
        actualizarTablaProyecciones();
    }

    function limpiarCamposProyeccion() {
        document.getElementById('producto_descripcion').value = '';
        document.getElementById('unidad_medida').value = 'und';
        document.getElementById('precio_compra').value = '';
        document.getElementById('precio_venta').value = '';
        document.getElementById('propocion_venta').value = '';
    }


    let inventarioArray = [];
    let totalInventario = 0;

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

    function editarProducto(index, campo, valor) {
        inventarioArray[index][campo] = parseFloat(valor);
        if (campo === 'precioUnitario' || campo === 'cantidad') {
            inventarioArray[index].montoTotal = (inventarioArray[index].precioUnitario * inventarioArray[index]
                .cantidad).toFixed(2);
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

    let inventarioArray1 = [];
    let totalInventario1 = 0;

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
        console.log(inventarioArray1);
        actualizarTablaInventario1();
        limpiarCamposInventario1();
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

    function editarProducto1(index, campo, valor) {
        inventarioArray[index][campo] = parseFloat(valor);
        if (campo === 'precioUnitario' || campo === 'cantidad') {
            inventarioArray[index].montoTotal = (inventarioArray[index].precioUnitario * inventarioArray[index]
                .cantidad).toFixed(2);
        }
        actualizarTablaInventario1();
    }

    function eliminarProducto1(index) {
        inventarioArray.splice(index, 1);
        actualizarTablaInventario1();
    }

    function limpiarCamposInventario1() {
        document.getElementById('descripcion_producto_inventario1').value = '';
        document.getElementById('precio_unitario_inventario1').value = '';
        document.getElementById('cantidad_producto_inventario1').value = '';
    }


    let deudasFinancierasArray = [];

    function agregarDeudaFinanciera() {
        const entidad = document.getElementById('entidad_financiera').value;
        const saldoCapital = parseFloat(document.getElementById('saldo_capital').value);
        const cuota = parseFloat(document.getElementById('cuota').value);
        const tiempoRestante = 0;

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
        // document.getElementById('tiempo_restante').value = '';
    }

    function actualizarTablaDeudasFinancieras() {
        const tablaCuerpo = document.getElementById('datos_tabla_deudas_financieras');
        tablaCuerpo.innerHTML = '';

        deudasFinancierasArray.forEach((deuda, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML =
                `
            <td>${deuda.entidad}</td>
            <td>${deuda.saldoCapital}</td>
            <td>${deuda.cuota}</td>
            
            <td><button class="btn btn-danger btn-sm" onclick="eliminarDeudaFinanciera(${index})"><i class="fa fa-trash"></i></button></td>`;
        });
    }

    function eliminarDeudaFinanciera(index) {
        deudasFinancierasArray.splice(index, 1);
        actualizarTablaDeudasFinancieras();
    }

    let gastosOperativosArray = [];

    function agregarGastoOperativoc() {
        const descripcion = document.getElementById('descripcion_gasto').value;
        const precioUnitario = parseFloat(document.getElementById('precio_unitario_gasto').value);
        const cantidad = parseFloat(document.getElementById('cantidad_gasto').value);
        const total = (precioUnitario * cantidad).toFixed(2);

        const gasto = {
            descripcion,
            precioUnitario,
            cantidad,
            total: parseFloat(total)
        };
        gastosOperativosArray.push(gasto);
        actualizarTablaGastosOperativos();
        limpiarCamposGastoOperativo();
    }

    function limpiarCamposGastoOperativo() {
        document.getElementById('descripcion_gasto').value = '';
        document.getElementById('precio_unitario_gasto').value = '';
        document.getElementById('cantidad_gasto').value = '';
    }

    function actualizarTablaGastosOperativos() {
        const tablaCuerpo = document.getElementById('datos_tabla_gastos_operativos');
        tablaCuerpo.innerHTML = '';

        gastosOperativosArray.forEach((gasto, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML =
                `
        <td>${gasto.descripcion}</td>
        <td>${gasto.precioUnitario}</td>
        <td>${gasto.cantidad}</td>
        <td>${gasto.total}</td>
        <td><button class="btn btn-danger btn-sm" onclick="eliminarGastoOperativo(${index})"><i class="fa fa-trash"></i></button></td>`;
        });
        actualizarTotalGastosOperativos();
    }

    function eliminarGastoOperativo(index) {
        gastosOperativosArray.splice(index, 1);
        actualizarTablaGastosOperativos();
    }

    function actualizarTotalGastosOperativos() {
        const total = gastosOperativosArray.reduce((sum, gasto) => sum + gasto.total, 0);
        document.getElementById('totalGatosOperativos').value = total.toFixed(2);
    }

    
    let gastosProducirArray = [];

    function agregarGastoProducir() {
        const descripcionGasto = document.getElementById('descripcion_gasto_producir').value;
        const precioUnitario = parseFloat(document.getElementById('precio_unitario_gasto_producir').value);
        const cantidad = parseFloat(document.getElementById('cantidad_gasto_producir').value);
        const totalGasto = (precioUnitario * cantidad).toFixed(2);

        const gastoProducir = {
            descripcionGasto,
            precioUnitario,
            cantidad,
            totalGasto: parseFloat(totalGasto)
        };
        gastosProducirArray.push(gastoProducir);
        actualizarTablaGastosProducir();
        limpiarCamposGastoProducir();
    }

    function limpiarCamposGastoProducir() {
        document.getElementById('descripcion_gasto_producir').value = '';
        document.getElementById('precio_unitario_gasto_producir').value = '';
        document.getElementById('cantidad_gasto_producir').value = '';
    }

    function actualizarTablaGastosProducir() {
        const tablaCuerpo = document.getElementById('datos_tabla_gastos_producir');
        tablaCuerpo.innerHTML = '';

        gastosProducirArray.forEach((gasto, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML =
                `
            <td>${gasto.descripcionGasto}</td>
            <td>${gasto.precioUnitario}</td>
            <td>${gasto.cantidad}</td>
            <td>${gasto.totalGasto}</td>
            <td><button class="btn btn-danger btn-sm" onclick="eliminarGastoProducir(${index})"><i class="fa fa-trash"></i></button></td>`;
        });
    }

    function eliminarGastoProducir(index) {
        gastosProducirArray.splice(index, 1);
        actualizarTablaGastosProducir();
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
            formData.append('proyeccionesArray', JSON.stringify(proyeccionesArray));
            formData.append('inventarioArray', JSON.stringify(inventarioArray));
            formData.append('deudasFinancierasArray', JSON.stringify(deudasFinancierasArray));
            formData.append('gastosOperativosArray', JSON.stringify(gastosOperativosArray));
            formData.append('gastosProducirArray', JSON.stringify(gastosProducirArray));
            formData.append('inventarioArray1', JSON.stringify(inventarioArray1));
            formData.append('ventasdiarias', JSON.stringify(ventasDiarias));
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

    function toggleFields() {
        var selection = document.getElementById('tipo_producto').value;
        var selectionTipoCredito = "produccion";
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


        if (selection === 'agricola') {
                gastos_produccion_agricola.style.display = 'block';
                detalle_negocio.style.display = 'none';
        } else {
            gastos_produccion_agricola.style.display = 'none';
            detalle_negocio.style.display = 'block';
            }
        }

    

    function agricola() {
        var selection = document.getElementById('tipo_producto').value;
        if (selection == 'agricola') {
            var selection1 = document.getElementById('descripcion_negocio').value;
            if (selection1 != '') {
                document.getElementById('nombre_actividad').value = selection1;
            }
        }
    }

    function agricolacalculo() {
        var selection = document.getElementById('tipo_producto').value;
        if (selection == 'agricola') {
            var selection1 = document.getElementById('precio_kg').value;
            var selection2 = document.getElementById('produccion_total').value;
            if (selection1 != '' && selection2 != '') {
                var calculo = parseFloat(selection2 * selection1).toFixed(2);
                document.getElementById('total_soles').value = calculo;
            }
        }
    }
    let ventasDiarias = [];
    let totalVentasSemana = 0;
    let totalVentasQuincena = 0;
    let totalVentasMes = 0;

    function agregarVentaTabla() {
        const dia = document.getElementById('dia_venta').value;
        const min = parseFloat(document.getElementById('venta_minima').value);
        const max = parseFloat(document.getElementById('venta_maxima').value);

        // Validación de venta mínima y máxima
        if (min > max) {
            alert("La venta mínima no puede ser mayor que la venta máxima.");
            return;
        }

        // Validación de día único
        const diaExistente = ventasDiarias.find(venta => venta.dia === dia);
        if (diaExistente) {
            alert("El día ya existe en la tabla de ventas diarias.");
            return;
        }

        const promedio = ((min + max) / 2).toFixed(2);

        const venta = {
            dia,
            min,
            max,
            promedio: parseFloat(promedio)
        };

        ventasDiarias.push(venta);
        actualizarTablaVenta();
        limpiarCamposVenta();
    }

    function actualizarTablaVenta() {
        const tablaCuerpo = document.getElementById('ventas_diarias');
        tablaCuerpo.innerHTML = '';
        totalVentasSemana = 0;
        totalVentasQuincena = 0;
        totalVentasMes = 0;
        factorsemana = 15 / 7;
        factormes = factorsemana * 2;

        ventasDiarias.forEach((venta, index) => {
            const row = tablaCuerpo.insertRow();
            row.innerHTML = `
            <td>${venta.dia}</td>
            <td>${venta.min}</td>
            <td>${venta.max}</td>
            <td>${venta.promedio}</td>
            <td><button class="btn btn-danger btn-sm" onclick="eliminarVenta(${index})"><i class="fa fa-trash"></i></button></td>
        `;
            totalVentasSemana += venta.promedio;
            totalVentasQuincena = totalVentasSemana * factorsemana;
            totalVentasMes = totalVentasSemana * factormes;
        });

        document.getElementById('total_ventas_semana').value = totalVentasSemana.toFixed(2);
        document.getElementById('total_ventas_quincena').value = totalVentasQuincena.toFixed(2);
        document.getElementById('total_ventas_mes').value = totalVentasMes.toFixed(2);

        actualizarVentaCredito();
    }

    function eliminarVenta(index) {
        ventasDiarias.splice(index, 1);
        actualizarTablaVenta();
    }

    function limpiarCamposVenta() {
        document.getElementById('dia_venta').value = '';
        document.getElementById('venta_minima').value = '';
        document.getElementById('venta_maxima').value = '';
    }

    function actualizarVentaCredito() {
        const porcentajeVentaCredito = parseFloat(document.getElementById('porcentaje_venta_credito').value) || 0;
        const ventaCredito = (totalVentasMes * porcentajeVentaCredito / 100).toFixed(2);
        document.getElementById('venta_credito').value = ventaCredito;
    }

    document.getElementById('porcentaje_venta_credito').addEventListener('input', actualizarVentaCredito);


    document.addEventListener('DOMContentLoaded', toggleFields);
</script>
