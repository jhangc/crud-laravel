# 🎯 EJEMPLOS DE USO - Sistema de Reversión de Pagos

## Ejemplo 1: Acceder a la Interfaz

### Método 1: Via URL directa
```
http://localhost/admin/credijoya/pagos/reversar
```

### Método 2: Via nombre de ruta en Blade
```blade
<a href="{{ route('pagocredijoya.index-reversar') }}" class="btn btn-primary">
    Reversar Pagos
</a>
```

### Método 3: Via redirect en controlador
```php
return redirect()->route('pagocredijoya.index-reversar');
```

---

## Ejemplo 2: Ver Pagos en SQL

### Ver últimos pagos de Credijoya
```sql
SELECT 
    i.id AS pago_id,
    i.prestamo_id,
    c.nombre AS cliente,
    i.monto,
    i.tipo,
    i.modo,
    i.nuevo_id,
    i.fecha_pago,
    i.created_at
FROM ingresos i
LEFT JOIN clientes c ON i.cliente_id = c.id
LEFT JOIN prestamos p ON i.prestamo_id = p.id
WHERE p.subproducto = 'credijoya'
ORDER BY i.created_at DESC
LIMIT 20;
```

**Resultado esperado:**
```
pago_id | prestamo_id | cliente          | monto   | tipo    | modo      | nuevo_id | fecha_pago | created_at
--------|-------------|------------------|---------|---------|-----------|----------|------------|--------------------
123     | 45          | Juan Pérez       | 1000.50 | total   | totalhoy  | NULL     | 2026-02-02 | 2026-02-02 14:35:22
124     | 45          | Juan Pérez       | 2500.75 | parcial | cuota     | 456      | 2026-02-02 | 2026-02-02 14:36:15
```

---

## Ejemplo 3: Reversionar vía UI

### Paso a Paso

**Pantalla 1: Listado de Pagos**
```
┌─────────────────────────────────────────────────────────────┐
│ ID | Cliente | Monto | Tipo | Modo | ¿Renovación? | Acciones│
├─────────────────────────────────────────────────────────────┤
│123 | Juan P. │1000.5│total │total │      NO      │[Reversar]│
│124 | Juan P. │2500.7│part. │cuota │      SÍ      │[Reversar]│
└─────────────────────────────────────────────────────────────┘
```

**Paso 1:** Haz clic en "Reversar" para pago #123

**Pantalla 2: Modal de Confirmación**
```
┌────────────────────────────────────────────────────┐
│ ⚠️ CONFIRMAR REVERSIÓN DE PAGO                     │
├────────────────────────────────────────────────────┤
│                                                    │
│ • Eliminará el ingreso registrado                  │
│ • Restaurará el estado anterior del crédito        │
│ • Descontará monto de caja                         │
│                                                    │
│ Cliente: Juan Pérez                                │
│ Monto:   S/ 1,000.50                               │
│                                                    │
│ Motivo (opcional):                                 │
│ [Pago duplicado por error]                         │
│                                                    │
│        [CANCELAR]  [SÍ, REVERSAR]                 │
└────────────────────────────────────────────────────┘
```

**Paso 2:** Confirma haciendo clic "SÍ, REVERSAR"

**Pantalla 3: Resultado**
```
✅ ¡Éxito! Pago reversado exitosamente
Trace ID: abc-123-def

La página se recargará en 3 segundos...
```

---

## Ejemplo 4: Reversión Manual vía API CURL

### Reversión SIN renovación
```bash
curl -X POST http://localhost/admin/credijoya/pago/123/reversar \
  -H "X-CSRF-TOKEN: tu_token_csrf" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "motivo": "Cliente pagó dos veces"
  }'
```

**Response Exitoso:**
```json
{
  "ok": true,
  "message": "Pago reversado exitosamente",
  "trace_id": "abc-123-def"
}
```

**Response con Error:**
```json
{
  "ok": false,
  "error": "No hay una caja abierta para el usuario actual",
  "trace_id": "xyz-789-qwe"
}
```

---

## Ejemplo 5: Verificación post-Reversión

### Verificar que el ingreso fue eliminado
```sql
SELECT * FROM ingresos WHERE id = 123;
-- Resultado: deleted_at tendrá una fecha (SoftDelete)
```

### Verificar que caja se actualizó
```sql
SELECT id, cantidad_ingresos, monto_cierre 
FROM caja_transacciones 
WHERE id = 10;
-- Resultado: cantidad_ingresos se redujo en 1000.50
```

### Verificar estado del crédito
```sql
SELECT id, estado, fecha_fin 
FROM prestamos 
WHERE id = 45;
-- Resultado: estado = 'pagado', fecha_fin = NULL
```

### Verificar joyas restauradas
```sql
SELECT id, devuelta, fecha_pago 
FROM credijoya_joyas 
WHERE prestamo_id = 45;
-- Resultado: devuelta = 0, fecha_pago = NULL
```

---

## Ejemplo 6: Reversión con Renovación

### Verificar que hay nuevo crédito
```sql
SELECT id, nuevo_id FROM ingresos WHERE id = 124;
-- Resultado: nuevo_id = 456
```

### Reversionar pago con renovación
```bash
curl -X POST http://localhost/admin/credijoya/pago/124/reversar \
  -H "X-CSRF-TOKEN: tu_token_csrf" \
  -H "Content-Type: application/json" \
  -d '{
    "motivo": "Se debe pagar completo, no renovar"
  }'
```

### Verificar que nuevo crédito fue eliminado
```sql
SELECT * FROM prestamos WHERE id = 456;
-- Resultado: Not found (fue eliminado)
```

### Verificar que cronograma del nuevo fue eliminado
```sql
SELECT * FROM cronograma WHERE id_prestamo = 456;
-- Resultado: vacío
```

### Verificar que joyas fueron transferidas
```sql
SELECT prestamo_id FROM credijoya_joyas WHERE id IN (1,2,3);
-- Resultado: prestamo_id = 45 (crédito anterior)
```

---

## Ejemplo 7: Ver Reversiones en Logs

### Ver últimas reversiones
```bash
tail -n 50 storage/logs/laravel.log | grep -i "reversión"
```

**Salida esperada:**
```
[2026-02-02 14:35:22] createdijoya.INFO: Reversión de pago (sin renovación) 
{"pago_id":123,"credito_id":45,"monto":1000.50,"trace":"abc-123-def","usuario":5}

[2026-02-02 14:36:15] createdijoya.INFO: Reversión de pago (con renovación) 
{"pago_id":124,"credito_anterior":45,"nuevo_credito":456,"monto":2500.75,"trace":"xyz-789-qwe","usuario":5}
```

---

## Ejemplo 8: Buscar por Trace ID

### Cuando algo falla, guardar el trace_id
```json
{
  "ok": false,
  "error": "Error al reversionar el pago: ...",
  "trace_id": "err-001-abc"
}
```

### Buscar error en logs con trace_id
```bash
grep "err-001-abc" storage/logs/laravel.log
```

---

## Ejemplo 9: Casos Reales

### Caso Real #1: Reversión Simple
```
SITUACIÓN: Cliente Juan pagó 1000 soles, pero el operador lo registró dos veces
REVERSIÓN: 
  • Pago ID: 123
  • Tipo: total (sin renovación)
  • Acción: Eliminar ingreso + restaurar estado
RESULTADO: ✅ Caja correcta, cliente descargado
```

### Caso Real #2: Reversión Compleja
```
SITUACIÓN: Pago parcial de 2500 soles creó renovación, pero debe ser total
REVERSIÓN:
  • Pago ID: 124
  • Tipo: parcial (con renovación nuevo_id=456)
  • Acción: Eliminar nuevo crédito + restaurar anterior
RESULTADO: ✅ Crédito original vuelve a estar en cuota
```

### Caso Real #3: Cliente Equivocado
```
SITUACIÓN: Pago de Cliente A se registró como Cliente B
REVERSIÓN:
  • Pago ID: 125
  • Tipo: total
  • Acción: Reversiar + registrar nuevamente
RESULTADO: ✅ Cliente A con pago, Cliente B sin cargo
```

---

## Ejemplo 10: Integración en Menú

### Agregar a layout principal
```blade
<!-- sidebar.blade.php o navbar.blade.php -->

<li class="nav-item">
    <a href="{{ route('pagocredijoya.index-reversar') }}" class="nav-link">
        <i class="fas fa-undo" style="color: #ff9800;"></i>
        <span>⚠️ Reversar Pago</span>
    </a>
</li>
```

### Con separador visual
```blade
<li class="nav-divider"></li>
<li class="nav-item">
    <a href="{{ route('pagocredijoya.index-reversar') }}" class="nav-link text-warning">
        <i class="fas fa-undo"></i> Reversar Pago Credijoya
    </a>
</li>
<li class="nav-divider"></li>
```

---

## Ejemplo 11: Validaciones Automáticas

### El sistema valida automáticamente:

❌ **No permitirá reversiar si:**
- Usuario no está autenticado
- Pago ID no existe
- Caja no está abierta
- Relaciones faltantes

✅ **Garantiza:**
- Todo o nada (transacción)
- Logs registrados
- Auditoría completa
- Estado consistente

---

## Ejemplo 12: Recuperar Reversión (Undelete)

### Si necesitas recuperar una reversión
```sql
-- Ver pagos eliminados
SELECT * FROM ingresos WHERE deleted_at IS NOT NULL;

-- Recuperar uno específico
UPDATE ingresos SET deleted_at = NULL WHERE id = 123;
```

---

## 📝 Resumen de Ejemplos

| Acción | Método | Complejidad |
|--------|--------|------------|
| Ver pagos UI | GET /admin/credijoya/pagos/reversar | Fácil |
| Reversar UI | Modal + POST AJAX | Fácil |
| Ver en SQL | SELECT ingresos WHERE ... | Media |
| Reversar CURL | POST con headers | Media |
| Verificar cambios | SELECT en varias tablas | Difícil |
| Buscar por trace | grep en logs | Fácil |

---

**¡Todos los ejemplos listos para copiar y pegar! 🎉**
