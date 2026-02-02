-- ============================================
-- Script de PRUEBA: Reversión de Pagos
-- ============================================

-- Paso 1: Ver los últimos pagos de Credijoya
SELECT 
    i.id,
    i.prestamo_id,
    c.nombre as cliente,
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

-- ============================================
-- Si necesitas REVERSAR manualmente (SIN UI)
-- ============================================

-- Ejemplo: Reversar pago ID = 123 (sin renovación)
SET @pago_id = 123;
SET @prestamo_id = 45;
SET @caja_id = 10;
SET @monto = 1000.50;

-- Verificar antes de eliminar:
SELECT * FROM ingresos WHERE id = @pago_id;

-- Restar de caja
UPDATE caja_transacciones 
SET cantidad_ingresos = cantidad_ingresos - @monto
WHERE id = @caja_id;

-- Restaurar crédito
UPDATE prestamos 
SET estado = 'pagado', fecha_fin = NULL 
WHERE id = @prestamo_id;

-- Restaurar joyas
UPDATE credijoya_joyas 
SET devuelta = 0, fecha_pago = NULL 
WHERE prestamo_id = @prestamo_id;

-- Eliminar ingreso
DELETE FROM ingresos WHERE id = @pago_id;

-- ============================================
-- Verificar resultado
-- ============================================

SELECT * FROM prestamos WHERE id = @prestamo_id;
SELECT * FROM caja_transacciones WHERE id = @caja_id;
SELECT * FROM credijoya_joyas WHERE prestamo_id = @prestamo_id;

-- ============================================
-- Si hay RENOVACIÓN (nuevo_id = 456)
-- ============================================

SET @pago_id = 123;
SET @prestamo_anterior = 45;
SET @nuevo_prestamo = 456;
SET @monto = 1000.50;
SET @caja_id = 10;

-- 1. Eliminar cronograma del nuevo crédito
DELETE FROM cronograma WHERE id_prestamo = @nuevo_prestamo;

-- 2. Eliminar relación cliente
DELETE FROM credito_cliente WHERE prestamo_id = @nuevo_prestamo;

-- 3. Transferir joyas de vuelta
UPDATE credijoya_joyas 
SET prestamo_id = @prestamo_anterior, devuelta = 0, fecha_pago = NULL 
WHERE prestamo_id = @nuevo_prestamo;

-- 4. Eliminar nuevo crédito
DELETE FROM prestamos WHERE id = @nuevo_prestamo;

-- 5. Restaurar crédito anterior
UPDATE prestamos 
SET estado = 'pagado', fecha_fin = NULL 
WHERE id = @prestamo_anterior;

-- 6. Restar de caja
UPDATE caja_transacciones 
SET cantidad_ingresos = cantidad_ingresos - @monto 
WHERE id = @caja_id;

-- 7. Eliminar ingreso
DELETE FROM ingresos WHERE id = @pago_id;

-- ============================================
-- Verificar logs de reversión
-- ============================================

-- Esto se verá en: storage/logs/laravel.log
-- Buscar: "Reversión de pago"
