# Ubicación del Motivo de Reversión de Pagos

## Resumen
El motivo (descripción) del reversal de pago se guarda en la tabla `reversiones_pagos`, que es una tabla de auditoría creada específicamente para registrar todos los eventos de reversión.

## Detalles de Almacenamiento

### Tabla: `reversiones_pagos`
**Propósito:** Mantener un registro auditable de todas las reversiones de pagos realizadas.

**Campos:**
- `id` (primary key)
- `ingreso_id` - Referencia al pago original que fue reversado
- `prestamo_id` - ID del crédito asociado
- `user_id` - ID del usuario que realizó la reversión
- `monto` - Monto del pago reversado
- **`motivo`** - La descripción/motivo del reversal (TEXT, nullable)
- `detalles` - Información adicional sobre la reversión (TEXT, nullable)
- `created_at` - Timestamp de cuándo se registró
- `updated_at` - Timestamp de última actualización

### Relaciones
```php
ReversionPago belongsTo Ingreso
ReversionPago belongsTo Credito (como prestamo)
ReversionPago belongsTo User (como usuario)
```

## Migración Creada
Archivo: `database/migrations/2026_02_02_000001_create_reversiones_pagos_table.php`

```php
Schema::create('reversiones_pagos', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('ingreso_id');
    $table->unsignedBigInteger('prestamo_id');
    $table->unsignedBigInteger('user_id');
    $table->decimal('monto', 15, 3);
    $table->string('motivo')->nullable();  // ← AQUí SE GUARDA
    $table->text('detalles')->nullable();
    $table->timestamps();
    
    // Foreign keys
    $table->foreign('ingreso_id')->references('id')->on('ingresos')->onDelete('cascade');
    $table->foreign('prestamo_id')->references('id')->on('prestamos')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    
    $table->index('created_at');
});
```

## Modelo Creado
Archivo: `app/Models/ReversionPago.php`

```php
class ReversionPago extends Model
{
    protected $table = 'reversiones_pagos';

    protected $fillable = [
        'ingreso_id',
        'prestamo_id',
        'user_id',
        'monto',
        'motivo',         // ← CAMPO FILLABLE
        'detalles',
    ];

    // Relaciones
    public function ingreso(): BelongsTo { ... }
    public function prestamo(): BelongsTo { ... }
    public function usuario(): BelongsTo { ... }
}
```

## Cómo se Guarda en el Controlador
Archivo: `app/Http/Controllers/CrediJoyaController.php`

En el método `reversarPago()`:

```php
public function reversarPago(Ingreso $pago, Request $request)
{
    $motivo = $request->input('motivo', 'Sin especificar');  // ← SE OBTIENE DEL FORMULARIO
    
    // ... lógica de reversión ...
    
    // Guardar en auditoría
    ReversionPago::create([
        'ingreso_id'  => $pago->id,
        'prestamo_id' => $creditoId,
        'user_id'     => auth()->id(),
        'monto'       => $montoTotal,
        'motivo'      => $motivo,  // ← SE GUARDA AQUÍ
        'detalles'    => 'Reversión sin renovación',
    ]);
}
```

## Vista (Desde el Formulario)
Archivo: `resources/views/admin/credijoya/reversar-pago.blade.php`

El motivo se captura del modal:

```html
<textarea class="form-control" id="motivoTextarea" placeholder="Describa el motivo de la reversión..."></textarea>
```

En el AJAX:
```javascript
const motivo = $('#motivoTextarea').val();
data: JSON.stringify({ motivo: motivo })  // ← SE ENVÍA AL SERVIDOR
```

## Consulta para Ver Reversiones
```php
// Todas las reversiones
$reversiones = ReversionPago::with('ingreso', 'prestamo', 'usuario')->get();

// Reversiones de un crédito específico
$reversiones = ReversionPago::where('prestamo_id', $creditoId)->get();

// Reversiones por usuario
$reversiones = ReversionPago::where('user_id', auth()->id())->get();

// Reversiones con sus motivos
$reversiones = ReversionPago::select('id', 'monto', 'motivo', 'created_at')
    ->where('prestamo_id', $creditoId)
    ->get();
```

## Problema CSRF Solucionado
El error "CSRF token mismatch" se resolvió agregando la meta etiqueta al layout:

**Archivo:** `resources/views/layouts/admin.blade.php`

```html
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">  <!-- ← AGREGADO -->
    <title>Sistema de Prestamos</title>
</head>
```

## Resumen de Cambios

| Componente | Cambio |
|-----------|--------|
| Base de Datos | Nueva tabla `reversiones_pagos` para auditoría |
| Modelo | Nuevo `ReversionPago` para interactuar con la tabla |
| Controlador | Método `reversarPago()` ahora guarda motivo y auditoría |
| Layout | Se agregó meta etiqueta CSRF en `admin.blade.php` |

## Pasos Siguientes

1. **Ejecutar migración:**
   ```bash
   php artisan migrate
   ```

2. **Verificar que funciona:**
   - Acceder a reversiones: `GET /admin/credijoya/pagos/reversar`
   - Ingresar motivo y enviar
   - Verificar que no hay error CSRF
   - Consultar BD: `SELECT * FROM reversiones_pagos;`

3. **Opcional - Crear vista de auditoría:**
   Mostrar historial de reversiones con motivos para análisis.
