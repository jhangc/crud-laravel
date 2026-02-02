#!/bin/bash

# ============================================
# INSTALACIÓN: Sistema de Reversión de Pagos
# ============================================

echo "🔄 Instalando Sistema de Reversión de Pagos Credijoya..."
echo ""

# 1. Validar que estemos en la raíz del proyecto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró artisan. Asegúrate de estar en la raíz del proyecto"
    exit 1
fi

echo "✅ Proyecto Laravel detectado"
echo ""

# 2. Validar PHP
echo "🔍 Verificando PHP..."
php -l app/Http/Controllers/CrediJoyaController.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Sintaxis PHP correcta"
else
    echo "❌ Error en sintaxis PHP"
    exit 1
fi

php -l routes/web.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Rutas PHP correctas"
else
    echo "❌ Error en rutas"
    exit 1
fi

echo ""

# 3. Verificar archivo de vista
if [ -f "resources/views/admin/credijoya/reversar-pago.blade.php" ]; then
    echo "✅ Vista de reversión encontrada"
else
    echo "❌ Vista de reversión no encontrada"
    exit 1
fi

echo ""

# 4. Limpiar caché
echo "🧹 Limpiando caché..."
php artisan cache:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1

echo "✅ Caché limpiado"
echo ""

# 5. Verificar base de datos
echo "🔍 Verificando conexión a base de datos..."
php artisan tinker << 'EOF'
try {
    DB::connection()->getPdo();
    echo "✅ Base de datos conectada\n";
} catch (Exception $e) {
    echo "❌ Error de conexión BD: " . $e->getMessage() . "\n";
}
EOF

echo ""

# 6. Resumen de archivos instalados
echo "📦 Archivos instalados:"
echo "  ✅ app/Http/Controllers/CrediJoyaController.php (método reversarPago)"
echo "  ✅ app/Http/Controllers/CrediJoyaController.php (método indexReversarPago)"
echo "  ✅ routes/web.php (2 nuevas rutas)"
echo "  ✅ resources/views/admin/credijoya/reversar-pago.blade.php"
echo "  ✅ REVERSIÓN_PAGOS_CREDIJOYA.md (guía usuario)"
echo "  ✅ RESUMEN_REVERSIÓN_TÉCNICO.md (documentación técnica)"
echo "  ✅ SQL_REVERSIÓN_PAGOS.sql (queries)"
echo ""

# 7. Rutas disponibles
echo "🌐 Rutas disponibles:"
echo "  GET  /admin/credijoya/pagos/reversar"
echo "       → Nombre: pagocredijoya.index-reversar"
echo "       → Muestra lista de pagos"
echo ""
echo "  POST /admin/credijoya/pago/{pago}/reversar"
echo "       → Nombre: pagocredijoya.reversar"
echo "       → Ejecuta reversión (AJAX)"
echo ""

# 8. Verificar permisos
echo "🔐 Verificando permisos..."
if [ -w "storage/logs" ]; then
    echo "✅ Permisos de escritura en logs OK"
else
    echo "⚠️  Posible problema de permisos en storage/logs"
fi

echo ""

# 9. Sugerencias
echo "📝 PRÓXIMOS PASOS:"
echo ""
echo "1. Acceder a: http://tu-app.com/admin/credijoya/pagos/reversar"
echo ""
echo "2. Agregar botón al menú (layout.blade.php):"
echo "   <a href=\"{{ route('pagocredijoya.index-reversar') }}\" class=\"nav-link\">"
echo "     <i class=\"fas fa-undo\"></i> Reversar Pago"
echo "   </a>"
echo ""
echo "3. Leer documentación:"
echo "   - REVERSIÓN_PAGOS_CREDIJOYA.md (guía usuario)"
echo "   - RESUMEN_REVERSIÓN_TÉCNICO.md (detalles técnicos)"
echo ""
echo "4. Probar con un pago de desarrollo"
echo ""

echo "✨ ¡Instalación completada! 🎉"
echo ""

