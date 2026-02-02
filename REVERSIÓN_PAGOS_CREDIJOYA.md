# 🔄 Sistema de Reversión de Pagos Credijoya

## ¿Cómo funciona?

Se ha agregado un sistema completo de reversión para eliminar pagos de Credijoya que se registraron por error.

### **Acceso**

Puedes acceder a la herramienta en:
```
/admin/credijoya/pagos/reversar
```

O agrega este botón en tu menú principal:
```html
<a href="{{ route('pagocredijoya.index-reversar') }}" class="nav-link">
    <i class="fas fa-undo"></i> Reversar Pago Credijoya
</a>
```

---

## **¿Qué hace el Sistema de Reversión?**

### **Si el pago fue SIN renovación:**
1. ✅ Elimina el registro en tabla `ingresos`
2. ✅ Restaura estado del crédito a "pagado"
3. ✅ Marca joyas como no devueltas
4. ✅ Descuenta monto de `caja_transacciones`
5. ✅ Registra la operación en logs

### **Si el pago fue CON renovación:**
1. ✅ Elimina el NUEVO crédito creado
2. ✅ Elimina cronograma del nuevo crédito
3. ✅ Transfiere joyas de vuelta al crédito anterior
4. ✅ Restaura estado del crédito anterior a "pagado"
5. ✅ Elimina el registro en `ingresos`
6. ✅ Descuenta monto de `caja_transacciones`
7. ✅ Registra la operación en logs

---

## **Proceso Paso a Paso**

### **1. Acceder a Reversión de Pagos**
- Ve a `/admin/credijoya/pagos/reversar`
- Verás una tabla con los últimos pagos registrados

### **2. Seleccionar Pago**
- Identifica el pago incorrecto en la tabla
- Revisa:
  - **ID Pago**: número único del pago
  - **Cliente**: nombre del cliente
  - **Monto**: cantidad pagada
  - **¿Renovación?**: SI/NO (indica si creó nuevo crédito)

### **3. Hacer Clic en "Reversar"**
- Se abrirá un modal de confirmación
- ⚠️ **Lee bien las advertencias**
- Puedes agregar un motivo (opcional)
- Confirma haciendo clic en "SÍ, Reversar Pago"

### **4. Confirmación**
- El sistema procesará la reversión
- Si es exitoso, verás un mensaje de éxito
- La página se recargará automáticamente
- El pago habrá sido eliminado

---

## **Seguridad y Auditoría**

✅ **Todo está protegido:**
- Transacciones atómicas (todo o nada)
- Validación de permisos (middleware `auth`)
- Logs detallados en `storage/logs/laravel.log`
- No se puede ejecutar sin estar autenticado

✅ **Registros en Log:**
```
[timestamp] createdijoya.DEBUG: Reversión de pago (sin renovación)
{
  "pago_id": 123,
  "credito_id": 45,
  "monto": 1000.50,
  "usuario": 5,
  "trace": "uuid-único"
}
```

---

## **Archivos Modificados**

1. **Controlador**: `app/Http/Controllers/CrediJoyaController.php`
   - Método: `reversarPago()` - Ejecuta la reversión
   - Método: `indexReversarPago()` - Muestra lista de pagos

2. **Rutas**: `routes/web.php`
   - GET `/admin/credijoya/pagos/reversar` - Ver página
   - POST `/admin/credijoya/pago/{pago}/reversar` - Ejecutar reversión

3. **Vista**: `resources/views/admin/credijoya/reversar-pago.blade.php`
   - Tabla de pagos
   - Modal de confirmación
   - JavaScript para AJAX

---

## **Casos de Uso**

### **Caso 1: Cliente pagó dos veces por error**
```
1. Ir a /admin/credijoya/pagos/reversar
2. Encontrar el pago duplicado
3. Hacer clic en "Reversar"
4. Confirmar
✓ El pago se elimina, la caja se descuenta
```

### **Caso 2: Se registró pago de cliente equivocado**
```
1. Reversionar el pago incorrecto
2. Volver a registrar con cliente correcto
✓ Todo vuelve al estado anterior
```

### **Caso 3: Pago fue con renovación, pero debe cancelarse completo**
```
1. Reversionar el pago (el nuevo crédito se elimina)
2. Registrar pago total del crédito original
✓ Se restaura el crédito anterior
```

---

## **Errores Comunes**

❌ **"No hay caja abierta"**
- Asegúrate de que la caja esté abierta
- El pago debe tener un `transaccion_id` válido

❌ **"Error al reversionar el pago"**
- Revisar `storage/logs/laravel.log`
- Puede ser problema con joyas o cuotas

✅ **Solución**: Contacta al administrador con el trace_id del error

---

## **SQL Manual (si es necesario)**

Si necesitas hacerlo manualmente por SQL:

```sql
-- 1. Ver el pago
SELECT id, prestamo_id, cliente_id, monto, nuevo_id, tipo, modo
FROM ingresos WHERE id = 123;

-- 2. Si NO hay renovación, solo elimina
DELETE FROM ingresos WHERE id = 123;

-- 3. Si HAY renovación
-- Primero elimina el nuevo crédito:
DELETE FROM cronograma WHERE id_prestamo = 456;
DELETE FROM credito_cliente WHERE prestamo_id = 456;
DELETE FROM credijoya_joyas WHERE prestamo_id = 456;
DELETE FROM prestamos WHERE id = 456;

-- Luego restaura el anterior:
UPDATE prestamos SET estado = 'pagado', fecha_fin = NULL WHERE id = 123;

-- Finalmente:
DELETE FROM ingresos WHERE id = 123;
UPDATE caja_transacciones SET cantidad_ingresos = cantidad_ingresos - 1000 WHERE id = caja_id;
```

---

**¡Sistema de reversión completamente funcional! 🎉**
