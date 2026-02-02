# Flujo de Datos del Motivo de Reversión

## 1. VISTA → SERVIDOR (Envío de Datos)

```
┌─────────────────────────────────────┐
│  Página: reversar-pago.blade.php    │
│                                     │
│  Modal con textarea:                │
│  ┌─────────────────────────────────┐│
│  │ Describa el motivo de la reversal││
│  │ [usuario escribe aquí]           ││
│  │ id="motivoTextarea"              ││
│  └─────────────────────────────────┘│
│                                     │
│  Botón "Reversar Pago" (onclick)   │
│  ↓                                  │
│  $.ajax POST                        │
└─────────────────────────────────────┘
              ↓
         ENVÍO AJAX
         ──────────
    Headers incluyen:
    'X-CSRF-TOKEN': meta tag value
    'Content-Type': 'application/json'
    
    Body JSON:
    { motivo: "el texto ingresado" }
              ↓
```

## 2. SERVIDOR → BASE DE DATOS (Almacenamiento)

```
┌──────────────────────────────┐
│ CrediJoyaController          │
│ reversarPago(Request)        │
│                              │
│ 1. $motivo = $request        │
│    .input('motivo')          │
│    ↓                         │
│ 2. Lógica de reversión       │
│    (3 cambios en BD)         │
│    ↓                         │
│ 3. ReversionPago::create([   │
│      'ingreso_id' => ...,    │
│      'prestamo_id' => ...,   │
│      'user_id' => ...,       │
│      'monto' => ...,         │
│      'motivo' => $motivo,    │
│      'detalles' => ...       │
│    ])                        │
└──────────────────────────────┘
              ↓
    ┌─────────────────────────┐
    │ Base de Datos           │
    │ tabla: reversiones_pagos│
    │                         │
    │ Registro INSERT:        │
    │ ┌─────────────────────┐ │
    │ │ id: 1               │ │
    │ │ ingreso_id: 123     │ │
    │ │ prestamo_id: 45     │ │
    │ │ user_id: 2          │ │
    │ │ monto: 500.00       │ │
    │ │ motivo: "error en..." │ │ ← AQUÍ!
    │ │ detalles: "..."     │ │
    │ │ created_at: ...     │ │
    │ └─────────────────────┘ │
    └─────────────────────────┘
```

## 3. CONSULTAS PARA RECUPERAR EL MOTIVO

```php
// Opción 1: Obtener todos los motivos de reversión
$reversiones = RevisionPago::all();
foreach ($reversiones as $rev) {
    echo $rev->motivo;  // Imprime el motivo
}

// Opción 2: Filtrar por crédito específico
$motivos = RevisionPago::where('prestamo_id', $creditoId)
    ->pluck('motivo');
// Resultado: ["Error de digitación", "Doble pago"]

// Opción 3: Con Eloquent relations
$rev = RevisionPago::with(['ingreso', 'prestamo', 'usuario'])
    ->first();
    
echo $rev->motivo;           // "Error de digitación"
echo $rev->ingreso->id;      // ID del pago original
echo $rev->prestamo->nombre; // Nombre del cliente
echo $rev->usuario->name;    // Quién lo reversó
echo $rev->created_at;       // Cuándo

// Opción 4: Reporte de auditoría
$reporte = RevisionPago::select('id', 'monto', 'motivo', 'created_at')
    ->whereBetween('created_at', [$inicio, $fin])
    ->get();
```

## 4. ARQUITECTURA COMPLETA

```
VISTA
─────────────────────────────────────────────────────────────────
resources/views/admin/credijoya/reversar-pago.blade.php
  • Formulario con textarea (id="motivoTextarea")
  • AJAX POST al endpoint /admin/credijoya/pago/{pago}/reversar
  • Headers con X-CSRF-TOKEN desde meta tag
  • Body JSON con { motivo: "valor" }

                            ↓ AJAX POST

RUTAS
─────────────────────────────────────────────────────────────────
routes/web.php
  POST /admin/credijoya/pago/{pago}/reversar
    → CrediJoyaController@reversarPago
    → Route Model Binding: {pago} = Ingreso model
    → Middleware: auth

                            ↓ RUTEADOR

CONTROLADOR
─────────────────────────────────────────────────────────────────
app/Http/Controllers/CrediJoyaController.php::reversarPago()
  1. Obtiene motivo: $motivo = $request->input('motivo')
  2. Inicia transacción: DB::beginTransaction()
  3. Ejecuta lógica de reversión (4 casos)
  4. Crea registro de auditoría:
     RevisionPago::create([
       'ingreso_id' => $pago->id,
       'prestamo_id' => $creditoId,
       'user_id' => auth()->id(),
       'monto' => $montoTotal,
       'motivo' => $motivo,  ← AQUÍ SE GUARDA
       'detalles' => "..."
     ])
  5. Realiza commit: DB::commit()
  6. Retorna JSON response

                            ↓ SAVE

BASE DE DATOS
─────────────────────────────────────────────────────────────────
Schema: reversiones_pagos

CREATE TABLE reversiones_pagos (
  id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ingreso_id bigint unsigned NOT NULL,
  prestamo_id bigint unsigned NOT NULL,
  user_id bigint unsigned NOT NULL,
  monto decimal(15,3) NOT NULL,
  motivo varchar(255) NULL,                  ← CAMPO MOTIVO
  detalles longtext NULL,
  created_at timestamp NULL,
  updated_at timestamp NULL,
  
  FOREIGN KEY (ingreso_id) REFERENCES ingresos(id) ON DELETE CASCADE,
  FOREIGN KEY (prestamo_id) REFERENCES prestamos(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  
  INDEX idx_created_at (created_at)
);

                            ↓ RETRIEVE

RECUPERACIÓN DE DATOS
─────────────────────────────────────────────────────────────────
// En cualquier parte de tu código:
$reversiones = RevisionPago::with('ingreso', 'prestamo', 'usuario')->get();

foreach ($reversiones as $rev) {
  // Acceder al motivo:
  echo $rev->motivo;  // "Error de digitación"
}

// O reporte completo:
$rev = RevisionPago::findOrFail($id);
echo "Motivo: {$rev->motivo}";
echo "Monto: {$rev->monto}";
echo "Usuario: {$rev->usuario->name}";
echo "Fecha: {$rev->created_at->format('d/m/Y H:i')}";
```

## 5. CAMBIOS REALIZADOS

### a) Nueva Tabla (Migración)
```
Archivo: database/migrations/2026_02_02_000001_create_reversiones_pagos_table.php
Cambio: Crea tabla con campo motivo
Estado: Pendiente de ejecutar: php artisan migrate
```

### b) Nuevo Modelo
```
Archivo: app/Models/RevisionPago.php
Cambio: ORM para la tabla reversiones_pagos
Incluye: Fillable array con 'motivo'
Incluye: Relaciones con Ingreso, Credito, User
```

### c) Controlador Actualizado
```
Archivo: app/Http/Controllers/CrediJoyaController.php
Cambios:
  • Línea ~10: Agregado use App\Models\ReversionPago
  • Línea ~1343: reversarPago(Request $request)
  • Línea ~1348: $motivo = $request->input('motivo', 'Sin especificar')
  • Línea ~1430-1440: ReversionPago::create([...])
  • Línea ~1470-1480: ReversionPago::create([...]) para caso con renovación
```

### d) Layout Actualizado (CSRF FIX)
```
Archivo: resources/views/layouts/admin.blade.php
Cambio: Agregada línea en <head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
Motivo: Permite que AJAX obtenga el token CSRF correctamente
```

## 6. VERIFICACIÓN

### Comando para verificar si se guardó:
```bash
# En terminal Laravel
php artisan tinker

# Luego ejecuta:
> \App\Models\RevisionPago::all()

# Resultado esperado:
Collection {
  #items: array:1 [
    0 => RevisionPago {
      #attributes: array:9 [
        "id" => 1
        "ingreso_id" => 123
        "prestamo_id" => 45
        "user_id" => 2
        "monto" => "500.00"
        "motivo" => "Error de digitación del monto"  ← AQUÍ!
        "detalles" => "Reversión sin renovación"
        "created_at" => "2024-02-02 14:30:00"
        "updated_at" => "2024-02-02 14:30:00"
      ]
    }
  ]
}
```

## 7. RESOLUCIÓN DE PROBLEMAS

### Error: "CSRF token mismatch"
**Causa:** Meta tag no existe en layout
**Solución:** Verificar que `admin.blade.php` tenga:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### El motivo no se guarda
**Causa:** Tabla no fue creada (migración no ejecutada)
**Solución:** 
```bash
php artisan migrate
```

### Error: "Class RevisionPago not found"
**Causa:** Autoloader no tiene el modelo
**Solución:**
```bash
composer dump-autoload
```
