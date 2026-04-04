---
name: laravel-12-best-practices
description: Guía estricta de mejores prácticas sugeridas por Laravel 12 para cada carpeta y componente del proyecto (controllers, modelos, vistas, APIs, rutas, middleware, DTOs, testing, seguridad, naming conventions y estructura de carpetas).
---

# 🏗️ Mejores Prácticas de Laravel 12 — Guía Estricta

> **Versión de Laravel:** 12.x (lanzado en febrero 2025)  
> **PHP mínimo requerido:** 8.2+  
> **Stack del proyecto:** Laravel 12 + Inertia.js + Vue/React + Jetstream + Sanctum + Ziggy

---

## 📁 1. Estructura de Carpetas del Proyecto

### 1.1 Estructura Base (Estándar Laravel 12)

```
proyecto/
├── app/
│   ├── Actions/           # Clases de acción de propósito único
│   ├── Console/           # Comandos Artisan personalizados
│   ├── DTOs/              # Data Transfer Objects (inmutables)
│   ├── Enums/             # Enumeraciones PHP 8.1+ (backed enums)
│   ├── Events/            # Clases de eventos
│   ├── Exceptions/        # Handlers de excepciones personalizados
│   ├── Http/
│   │   ├── Controllers/   # Controllers (skinny, sin lógica de negocio)
│   │   ├── Middleware/    # Middleware personalizado
│   │   ├── Requests/      # Form Requests (validación separada)
│   │   └── Resources/     # API Resources (transformación JSON)
│   ├── Jobs/              # Jobs para colas
│   ├── Listeners/         # Listeners de eventos
│   ├── Mail/              # Clases de correo
│   ├── Models/            # Modelos Eloquent
│   ├── Notifications/     # Notificaciones
│   ├── Observers/         # Observers de modelos
│   ├── Policies/          # Policies de autorización
│   ├── Providers/         # Service Providers
│   ├── Rules/             # Reglas de validación personalizadas
│   ├── Scopes/            # Global Scopes reutilizables
│   └── Services/          # Clases de servicio (lógica de negocio compleja)
├── bootstrap/
├── config/
├── database/
│   ├── factories/         # Factories para testing
│   ├── migrations/        # Migraciones de BD
│   └── seeders/           # Seeders de datos
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   │   ├── Components/    # Componentes Vue/React reutilizables
│   │   ├── Layouts/       # Layouts de la aplicación
│   │   └── Pages/         # Páginas Inertia.js
│   ├── markdown/
│   └── views/             # Vistas Blade (mails, etc.)
├── routes/
│   ├── api.php            # Rutas API (stateless, Sanctum)
│   ├── console.php        # Rutas de consola
│   └── web.php            # Rutas web (con sesión, CSRF)
├── storage/
├── tests/
│   ├── Feature/           # Tests de funcionalidad/integración
│   └── Unit/              # Tests unitarios
└── vendor/
```

### 1.2 Reglas de Estructura

- **NUNCA** colocar lógica de negocio directamente en controllers o modelos masivos.
- **SIEMPRE** crear carpetas como `DTOs/`, `Services/`, `Actions/`, `Enums/`, `Scopes/` cuando el proyecto las necesite.
- **ORGANIZAR** por dominio cuando el proyecto escale (DDD): `app/Domain/Producto/Models/`, `app/Domain/Producto/Actions/`, etc.
- Los archivos de configuración personalizados van en `config/`.
- Los assets compilados con Vite van en `resources/js/` y `resources/css/`.

---

## 🎮 2. Controllers

### 2.1 Principio: "Skinny Controllers, Fat Models"

Los controllers **NO deben contener lógica de negocio**. Su responsabilidad es:
1. Recibir el request HTTP.
2. Delegar a un Service, Action o Model.
3. Retornar la respuesta.

### 2.2 Convenciones de Nombrado

| Regla | Ejemplo Correcto | Ejemplo Incorrecto |
|-------|-------------------|---------------------|
| `PascalCase` + sufijo `Controller` | `ProductoController` | `productoController`, `Producto` |
| Singular para el recurso | `CategoriaController` | `CategoriasController` |
| Verbos RESTful como métodos | `index`, `show`, `store`, `update`, `destroy` | `getProducts`, `addProduct` |

### 2.3 Resource Controllers (CRUD)

```php
// ✅ CORRECTO: Resource Controller con 7 métodos RESTful
class ProductoController extends Controller
{
    public function __construct(
        private readonly ProductoService $productoService
    ) {}

    public function index(): \Inertia\Response
    {
        $productos = $this->productoService->listarTodos();
        return Inertia::render('Productos/Index', compact('productos'));
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $this->productoService->crear(
            ProductoDTO::fromRequest($request)
        );
        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Producto $producto): \Inertia\Response
    {
        return Inertia::render('Productos/Show', compact('producto'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        $this->productoService->actualizar($producto, ProductoDTO::fromRequest($request));
        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $this->productoService->eliminar($producto);
        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado.');
    }
}
```

```php
// ❌ INCORRECTO: Lógica directa en el controller
class ProductoController extends Controller
{
    public function store(Request $request)
    {
        // ❌ Validación dentro del controller
        $request->validate(['nombre' => 'required']);
        
        // ❌ Lógica de negocio en el controller
        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio * 1.13; // ❌ Cálculo de impuesto aquí
        $producto->save();
        
        // ❌ Notificaciones directas
        Mail::to($admin)->send(new NuevoProductoMail($producto));
        
        return redirect()->back();
    }
}
```

### 2.4 Invokable Controllers (Acción Única)

Usa `__invoke()` para controllers con una sola acción:

```php
// ✅ Controller invocable para una sola responsabilidad
class GenerarReporteVentasController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $reporte = app(GenerarReporteVentasAction::class)->execute(
            desde: $request->date('desde'),
            hasta: $request->date('hasta'),
        );

        return response()->download($reporte->ruta);
    }
}

// En routes/web.php:
Route::get('/reportes/ventas', GenerarReporteVentasController::class)
    ->name('reportes.ventas');
```

### 2.5 Inyección de Dependencias

```php
// ✅ CORRECTO: Inyección por constructor
class VentaController extends Controller
{
    public function __construct(
        private readonly VentaService $ventaService,
        private readonly NotificacionService $notificacionService,
    ) {}
}

// ❌ INCORRECTO: Instanciar directamente
class VentaController extends Controller
{
    public function store(Request $request)
    {
        $service = new VentaService(); // ❌ No testeable
    }
}
```

### 2.6 Autorización en Controllers

```php
// ✅ Usar Policies y authorize()
public function update(UpdateProductoRequest $request, Producto $producto)
{
    $this->authorize('update', $producto);
    // ...
}

// ✅ O usar middleware de autorización
Route::put('/productos/{producto}', [ProductoController::class, 'update'])
    ->can('update', 'producto');
```

---

## 📊 3. Modelos (Eloquent)

### 3.1 Convenciones de Nombrado

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Modelo | Singular, `PascalCase` | `Producto`, `CategoriaProducto` |
| Tabla BD | Plural, `snake_case` | `productos`, `categoria_productos` |
| Columna BD | `snake_case` | `precio_unitario`, `created_at` |
| Clave foránea | `snake_case` + `_id` | `categoria_id`, `usuario_id` |
| Tabla pivote | Singular de ambos, orden alfabético | `categoria_producto` |
| Propiedad fillable | `snake_case` | `nombre`, `precio_venta` |

### 3.2 Organización Interna del Modelo

Sigue este **orden estricto** dentro de cada modelo:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\EstadoProducto;

class Producto extends Model
{
    // 1️⃣ Traits
    use HasFactory, SoftDeletes;

    // 2️⃣ Constantes
    protected const STOCK_MINIMO_DEFAULT = 5;

    // 3️⃣ Propiedades del modelo
    protected $table = 'productos'; // Solo si no sigue convención
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'categoria_id',
        'estado',
    ];

    // 4️⃣ Casts (usar método casts() en Laravel 12)
    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'stock' => 'integer',
            'estado' => EstadoProducto::class,  // Enum casting
            'metadatos' => 'array',
            'activo' => 'boolean',
            'fecha_vencimiento' => 'date',
        ];
    }

    // 5️⃣ Relaciones (con return types explícitos)
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    // 6️⃣ Accessors y Mutators (sintaxis moderna con Attribute)
    protected function precioConImpuesto(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->precio_venta * 1.13, 2),
        );
    }

    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    // 7️⃣ Scopes Locales
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function scopeDeCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // 8️⃣ Métodos de negocio propios del modelo
    public function tieneStockSuficiente(int $cantidad): bool
    {
        return $this->stock >= $cantidad;
    }

    public function calcularGanancia(): float
    {
        return $this->precio_venta - $this->precio_compra;
    }
}
```

### 3.3 Eager Loading (Prevenir N+1)

```php
// ✅ CORRECTO: Eager loading
$productos = Producto::with(['categoria', 'ventaItems'])->get();

// ✅ Eager loading con selección de columnas
$productos = Producto::with(['categoria:id,nombre'])->get();

// ✅ Prevenir lazy loading globalmente (en AppServiceProvider)
public function boot(): void
{
    Model::preventLazyLoading(! $this->app->isProduction());
}

// ❌ INCORRECTO: N+1 queries
$productos = Producto::all();
foreach ($productos as $producto) {
    echo $producto->categoria->nombre; // ❌ Una query por producto
}
```

### 3.4 Enums (PHP 8.1+)

```php
// app/Enums/EstadoProducto.php
<?php

namespace App\Enums;

enum EstadoProducto: string
{
    case Activo = 'activo';
    case Inactivo = 'inactivo';
    case Agotado = 'agotado';
    case Descontinuado = 'descontinuado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Inactivo => 'Inactivo',
            self::Agotado => 'Agotado',
            self::Descontinuado => 'Descontinuado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Activo => 'green',
            self::Inactivo => 'gray',
            self::Agotado => 'red',
            self::Descontinuado => 'yellow',
        };
    }
}
```

---

## 📝 4. Form Requests (Validación)

### 4.1 Regla Fundamental

**NUNCA** validar dentro del controller. **SIEMPRE** usar Form Requests.

```bash
# Generar un Form Request
php artisan make:request StoreProductoRequest
php artisan make:request UpdateProductoRequest
```

### 4.2 Ejemplo Completo

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\EstadoProducto;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreProductoRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para esta solicitud.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Producto::class);
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos')],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'precio_compra' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'precio_venta' => ['required', 'numeric', 'min:0', 'gt:precio_compra'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'estado' => ['required', new Enum(EstadoProducto::class)],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'precio_venta.gt' => 'El precio de venta debe ser mayor al precio de compra.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }

    /**
     * Preparar datos para validación.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim($this->nombre),
        ]);
    }
}
```

### 4.3 Update Request (con exclusión del propio registro)

```php
class UpdateProductoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos')->ignore($this->route('producto')),
            ],
            // ...resto de reglas
        ];
    }
}
```

---

## 🌐 5. Rutas (Routes)

### 5.1 Convenciones de Nombrado

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| URLs | `kebab-case`, plural | `/productos`, `/categorias-producto` |
| Nombres de ruta | `snake_case` con punto | `productos.index`, `productos.show` |
| Parámetros | `camelCase` o `snake_case` singular | `{producto}`, `{categoriaProducto}` |

### 5.2 Resource Routes

```php
// ✅ CORRECTO: Resource route (genera index, create, store, show, edit, update, destroy)
Route::resource('productos', ProductoController::class);

// ✅ Con restricción de métodos
Route::resource('productos', ProductoController::class)
    ->only(['index', 'store', 'update', 'destroy']);

// ✅ API Resource (sin create ni edit)
Route::apiResource('productos', Api\ProductoController::class);
```

### 5.3 Agrupación de Rutas

```php
// routes/web.php

// ✅ Agrupar por middleware y prefijo
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

        Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
            ->name('dashboard');

        // Recursos principales
        Route::resource('productos', ProductoController::class);
        Route::resource('categorias', CategoriaController::class);
        Route::resource('ventas', VentaController::class);

        // Rutas de acción única
        Route::get('/reportes/ventas', GenerarReporteVentasController::class)
            ->name('reportes.ventas');

        // Agrupación por prefijo
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('usuarios', Admin\UsuarioController::class);
            Route::resource('roles', Admin\RolController::class);
        });
    });
```

### 5.4 Rutas API

```php
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    // ✅ API Resources con versionado por prefijo
    Route::prefix('v1')->name('api.v1.')->group(function () {
        Route::apiResource('productos', Api\V1\ProductoController::class);
        Route::apiResource('categorias', Api\V1\CategoriaController::class);

        // Rutas personalizadas de API
        Route::get('productos/stock-bajo', [Api\V1\ProductoController::class, 'stockBajo'])
            ->name('productos.stock-bajo');
    });
});
```

### 5.5 Route Model Binding

```php
// ✅ Implicit binding (automático, Laravel lo resuelve por {producto} => Producto)
Route::get('/productos/{producto}', [ProductoController::class, 'show']);

// ✅ Custom key para binding
Route::get('/productos/{producto:slug}', [ProductoController::class, 'show']);

// ✅ Scoped binding (relación padre-hijo)
Route::get('/categorias/{categoria}/productos/{producto}', function (
    Categoria $categoria,
    Producto $producto
) {
    // $producto está restringido a la $categoria automáticamente
});
```

---

## 🔌 6. API Resources (Transformación JSON)

### 6.1 Cuándo Usar

**SIEMPRE** usar API Resources para respuestas JSON en endpoints API. **NUNCA** retornar modelos directamente.

```bash
php artisan make:resource ProductoResource
php artisan make:resource ProductoCollection
```

### 6.2 Ejemplo de Resource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio_compra' => $this->precio_compra,
            'precio_venta' => $this->precio_venta,
            'precio_con_impuesto' => $this->precio_con_impuesto,
            'stock' => $this->stock,
            'stock_minimo' => $this->stock_minimo,
            'estado' => $this->estado->value,
            'estado_etiqueta' => $this->estado->etiqueta(),

            // Relaciones condicionales (solo si están cargadas)
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'venta_items_count' => $this->whenCounted('ventaItems'),

            // Timestamps formateados
            'creado_en' => $this->created_at->toISOString(),
            'actualizado_en' => $this->updated_at->toISOString(),
        ];
    }
}
```

### 6.3 Uso en Controller API

```php
// ✅ CORRECTO
public function index(): AnonymousResourceCollection
{
    $productos = Producto::with('categoria')
        ->activo()
        ->paginate(15);

    return ProductoResource::collection($productos);
}

public function show(Producto $producto): ProductoResource
{
    return new ProductoResource($producto->load('categoria', 'ventaItems'));
}

// ❌ INCORRECTO: Retornar modelo directamente
public function show(Producto $producto)
{
    return $producto; // ❌ Expone toda la estructura y campos sensibles
}
```

---

## 📦 7. DTOs (Data Transfer Objects)

### 7.1 Ubicación y Propósito

Carpeta: `app/DTOs/`

Los DTOs transfieren datos entre capas sin lógica de negocio. Usa propiedades `readonly`.

```php
<?php

namespace App\DTOs;

use App\Http\Requests\StoreProductoRequest;
use App\Enums\EstadoProducto;

final readonly class ProductoDTO
{
    public function __construct(
        public string $nombre,
        public ?string $descripcion,
        public float $precioCompra,
        public float $precioVenta,
        public int $stock,
        public int $stockMinimo,
        public int $categoriaId,
        public EstadoProducto $estado,
    ) {}

    /**
     * Crear DTO desde un Form Request.
     */
    public static function fromRequest(StoreProductoRequest $request): self
    {
        return new self(
            nombre: $request->validated('nombre'),
            descripcion: $request->validated('descripcion'),
            precioCompra: (float) $request->validated('precio_compra'),
            precioVenta: (float) $request->validated('precio_venta'),
            stock: (int) $request->validated('stock'),
            stockMinimo: (int) $request->validated('stock_minimo'),
            categoriaId: (int) $request->validated('categoria_id'),
            estado: EstadoProducto::from($request->validated('estado')),
        );
    }

    /**
     * Crear DTO desde un array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            nombre: $data['nombre'],
            descripcion: $data['descripcion'] ?? null,
            precioCompra: (float) $data['precio_compra'],
            precioVenta: (float) $data['precio_venta'],
            stock: (int) $data['stock'],
            stockMinimo: (int) $data['stock_minimo'],
            categoriaId: (int) $data['categoria_id'],
            estado: EstadoProducto::from($data['estado']),
        );
    }

    /**
     * Convertir a array para Eloquent.
     */
    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio_compra' => $this->precioCompra,
            'precio_venta' => $this->precioVenta,
            'stock' => $this->stock,
            'stock_minimo' => $this->stockMinimo,
            'categoria_id' => $this->categoriaId,
            'estado' => $this->estado,
        ];
    }
}
```

---

## ⚡ 8. Actions (Clases de Acción)

### 8.1 Ubicación y Propósito

Carpeta: `app/Actions/`

Las Actions encapsulan una sola operación de negocio. Son la alternativa a Services para operaciones discretas.

```php
<?php

namespace App\Actions\Producto;

use App\DTOs\ProductoDTO;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

final class CrearProductoAction
{
    public function execute(ProductoDTO $dto): Producto
    {
        return DB::transaction(function () use ($dto) {
            $producto = Producto::create($dto->toArray());

            // Lógica adicional: log, evento, etc.
            event(new ProductoCreado($producto));

            return $producto;
        });
    }
}
```

```php
// Uso en el controller
public function store(StoreProductoRequest $request, CrearProductoAction $action): RedirectResponse
{
    $action->execute(ProductoDTO::fromRequest($request));

    return redirect()->route('productos.index')
        ->with('success', 'Producto creado exitosamente.');
}
```

---

## 🛡️ 9. Services (Capa de Servicio)

### 9.1 Ubicación y Propósito

Carpeta: `app/Services/`

Los Services contienen lógica de negocio compleja que involucra múltiples modelos o acciones.

```php
<?php

namespace App\Services;

use App\DTOs\ProductoDTO;
use App\Models\Producto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

final class ProductoService
{
    public function listarTodos(int $porPagina = 15): LengthAwarePaginator
    {
        return Producto::with('categoria')
            ->activo()
            ->orderBy('nombre')
            ->paginate($porPagina);
    }

    public function crear(ProductoDTO $dto): Producto
    {
        return DB::transaction(function () use ($dto) {
            $producto = Producto::create($dto->toArray());
            $this->invalidarCache();
            return $producto;
        });
    }

    public function actualizar(Producto $producto, ProductoDTO $dto): Producto
    {
        return DB::transaction(function () use ($producto, $dto) {
            $producto->update($dto->toArray());
            $this->invalidarCache();
            return $producto->fresh();
        });
    }

    public function eliminar(Producto $producto): void
    {
        DB::transaction(function () use ($producto) {
            $producto->delete(); // Soft delete si está habilitado
            $this->invalidarCache();
        });
    }

    public function obtenerStockBajo(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('productos.stock_bajo', 300, function () {
            return Producto::conStockBajo()
                ->with('categoria:id,nombre')
                ->get();
        });
    }

    private function invalidarCache(): void
    {
        Cache::forget('productos.stock_bajo');
    }
}
```

---

## 🛡️ 10. Middleware

### 10.1 Principios

- Cada middleware debe tener **una sola responsabilidad**.
- Nombrar en `PascalCase` describiendo la acción.
- Registrar middleware en `bootstrap/app.php` (Laravel 12 ya no usa `Kernel.php`).

```php
// app/Http/Middleware/EnsureUserIsAdmin.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
```

```php
// bootstrap/app.php — Registro de middleware en Laravel 12
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

---

## 🔐 11. Policies (Autorización)

### 11.1 Convenciones

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Policy | `PascalCase` + `Policy` | `ProductoPolicy` |
| Métodos | Verbos del resource | `viewAny`, `view`, `create`, `update`, `delete` |

```bash
php artisan make:policy ProductoPolicy --model=Producto
```

```php
<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Todos los autenticados pueden listar
    }

    public function view(User $user, Producto $producto): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('inventario');
    }

    public function update(User $user, Producto $producto): bool
    {
        return $user->hasRole('admin') || $user->hasRole('inventario');
    }

    public function delete(User $user, Producto $producto): bool
    {
        return $user->hasRole('admin');
    }
}
```

---

## 🗃️ 12. Migraciones y Base de Datos

### 12.1 Convenciones de Nombrado

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Archivo migración | `YYYY_MM_DD_HHMMSS_accion_tabla` | `2026_03_27_create_productos_table` |
| Tabla | Plural, `snake_case` | `productos`, `categoria_productos` |
| Columna | `snake_case` | `precio_venta`, `stock_minimo` |
| Clave foránea | `modelo_singular_id` | `categoria_id` |
| Tabla pivote | Singular, orden alfabético | `categoria_producto` |
| Índice | `tabla_columna_tipo` | `productos_nombre_index` |

### 12.2 Ejemplo de Migración Completa

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('stock_minimo')->default(5);
            $table->string('estado')->default('activo');
            $table->foreignId('categoria_id')
                ->constrained()
                ->onDelete('restrict');
            $table->softDeletes();
            $table->timestamps();

            // Índices explícitos
            $table->index('estado');
            $table->index(['categoria_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
```

### 12.3 Factories

```php
<?php

namespace Database\Factories;

use App\Enums\EstadoProducto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        $precioCompra = fake()->randomFloat(2, 5, 500);

        return [
            'nombre' => fake()->unique()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio_compra' => $precioCompra,
            'precio_venta' => round($precioCompra * 1.35, 2),
            'stock' => fake()->numberBetween(0, 200),
            'stock_minimo' => fake()->numberBetween(3, 20),
            'estado' => fake()->randomElement(EstadoProducto::cases()),
            'categoria_id' => Categoria::factory(),
        ];
    }

    // Estados personalizados
    public function agotado(): self
    {
        return $this->state(fn () => [
            'stock' => 0,
            'estado' => EstadoProducto::Agotado,
        ]);
    }

    public function activo(): self
    {
        return $this->state(fn () => [
            'estado' => EstadoProducto::Activo,
            'stock' => fake()->numberBetween(10, 200),
        ]);
    }
}
```

### 12.4 Seeders

```php
<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías primero
        $categorias = Categoria::factory(5)->create();

        // Crear productos asociados a cada categoría
        $categorias->each(function (Categoria $categoria) {
            Producto::factory(10)->create([
                'categoria_id' => $categoria->id,
            ]);
        });
    }
}
```

---

## 🖼️ 13. Vistas (Blade + Inertia/Vue/React)

### 13.1 Convenciones para Blade

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Archivo | `snake_case.blade.php` | `product_card.blade.php` |
| Directorio | `snake_case/` por feature | `views/emails/`, `views/pdf/` |
| Componente Blade | `kebab-case` en uso | `<x-product-card />` |

### 13.2 Convenciones para Inertia (Vue/React Pages)

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Página | `PascalCase` en carpeta contextual | `Pages/Productos/Index.vue` |
| Componente | `PascalCase` | `Components/ProductoCard.vue` |
| Layout | `PascalCase` | `Layouts/AppLayout.vue` |
| Composable/Hook | `camelCase` con prefijo `use` | `useProductos.js` |

### 13.3 Estructura de Pages con Inertia

```
resources/js/
├── Components/
│   ├── ui/                    # Componentes genéricos UI
│   │   ├── Button.vue
│   │   ├── Modal.vue
│   │   └── DataTable.vue
│   └── productos/             # Componentes específicos del módulo
│       ├── ProductoCard.vue
│       └── ProductoForm.vue
├── Composables/               # Hooks/Composables reutilizables
│   ├── useProductos.js
│   └── useNotificaciones.js
├── Layouts/
│   └── AppLayout.vue
├── Pages/
│   ├── Dashboard.vue
│   ├── Productos/
│   │   ├── Index.vue
│   │   ├── Show.vue
│   │   ├── Create.vue
│   │   └── Edit.vue
│   └── Categorias/
│       ├── Index.vue
│       └── Show.vue
└── app.js
```

### 13.4 Reglas para Vistas

- **NUNCA** poner lógica de negocio, queries, ni cálculos complejos en las vistas.
- **SIEMPRE** recibir datos ya procesados desde el controller.
- Separar CSS y JS de las vistas Blade en archivos dedicados.
- Usar componentes Blade (`<x-...>`) o componentes Vue/React para reutilización.
- Usar `@csrf` en **todos** los formularios Blade con métodos POST/PUT/PATCH/DELETE.

```blade
{{-- ✅ CORRECTO: Solo presentación --}}
<x-app-layout>
    <h1>{{ $producto->nombre }}</h1>
    <p>Precio: ${{ number_format($producto->precio_venta, 2) }}</p>
    <span class="badge badge-{{ $producto->estado->color() }}">
        {{ $producto->estado->etiqueta() }}
    </span>
</x-app-layout>

{{-- ❌ INCORRECTO: Query en la vista --}}
@foreach(App\Models\Producto::where('activo', true)->get() as $producto)
    {{-- ❌ Nunca hacer queries en Blade --}}
@endforeach
```

---

## 🔤 14. Convenciones de Nombrado — Resumen Completo

### 14.1 Variables y Propiedades

| Contexto | Convención | Ejemplo |
|----------|------------|---------|
| Variables PHP | `$camelCase` | `$precioTotal`, `$ventaItem` |
| Propiedades de modelo | `snake_case` | `$this->precio_venta` |
| Constantes de clase | `UPPER_SNAKE_CASE` | `self::STOCK_MINIMO` |
| Variables JS/TS | `camelCase` | `const precioTotal = ...` |
| Props Vue/React | `camelCase` | `<ProductoCard :precioVenta="..." />` |
| Variables `.env` | `UPPER_SNAKE_CASE` | `DB_DATABASE`, `APP_DEBUG` |

### 14.2 Métodos y Funciones

| Contexto | Convención | Ejemplo |
|----------|------------|---------|
| Métodos PHP | `camelCase` | `calcularTotal()`, `obtenerActivos()` |
| Métodos de Scope | `scopeCamelCase` | `scopeActivo()`, `scopeConStockBajo()` |
| Métodos de relación | `camelCase` (descripción de relación) | `ventaItems()`, `categoria()` |
| Helpers globales | `snake_case` | `calcular_iva()` |

### 14.3 Archivos y Carpetas

| Tipo de Archivo | Convención | Ejemplo |
|-----------------|------------|---------|
| Clases PHP | `PascalCase.php` | `ProductoController.php` |
| Vistas Blade | `snake_case.blade.php` | `product_list.blade.php` |
| Páginas Inertia | `PascalCase.vue/.jsx` | `Index.vue`, `Show.jsx` |
| Componentes Vue/React | `PascalCase.vue/.jsx` | `ProductoCard.vue` |
| Config | `snake_case.php` | `custom_settings.php` |
| Migraciones | `YYYY_MM_DD_HHMMSS_snake_case.php` | `2026_01_01_000000_create_productos_table.php` |
| Seeders | `PascalCase` + `Seeder` | `ProductoSeeder.php` |
| Factories | `PascalCase` + `Factory` | `ProductoFactory.php` |
| Tests | `PascalCase` + `Test` | `ProductoServiceTest.php` |
| Middleware | `PascalCase` | `EnsureUserIsAdmin.php` |
| Mail | `PascalCase` | `ProductoAgotadoMail.php` |
| Jobs | `PascalCase` | `ProcesarImportacionJob.php` |
| Events | `PascalCase` (pasado) | `ProductoCreado.php` |
| Listeners | `PascalCase` (acción) | `EnviarNotificacionStock.php` |
| Policies | `PascalCase` + `Policy` | `ProductoPolicy.php` |
| Rules | `PascalCase` | `PrecioVentaValido.php` |
| Enums | `PascalCase` | `EstadoProducto.php` |
| DTOs | `PascalCase` + `DTO` | `ProductoDTO.php` |
| Resources | `PascalCase` + `Resource` | `ProductoResource.php` |
| Form Requests | `PascalCase` + `Request` | `StoreProductoRequest.php`, `UpdateProductoRequest.php` |
| Artisan Commands | `kebab-case` al ejecutar | `php artisan producto:importar` |

---

## 🧪 15. Testing

### 15.1 Estructura y Convenciones

```
tests/
├── Feature/
│   ├── Producto/
│   │   ├── CrearProductoTest.php
│   │   ├── ActualizarProductoTest.php
│   │   └── EliminarProductoTest.php
│   └── Auth/
│       └── LoginTest.php
└── Unit/
    ├── DTOs/
    │   └── ProductoDTOTest.php
    ├── Services/
    │   └── ProductoServiceTest.php
    └── Models/
        └── ProductoTest.php
```

### 15.2 Patrones de Test (AAA — Arrange, Act, Assert)

```php
<?php

namespace Tests\Feature\Producto;

use App\Models\Producto;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_puede_crear_producto(): void
    {
        // Arrange (Preparar)
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->create();

        $datosProducto = [
            'nombre' => 'Whisky Premium',
            'precio_compra' => 150.00,
            'precio_venta' => 250.00,
            'stock' => 50,
            'stock_minimo' => 5,
            'categoria_id' => $categoria->id,
            'estado' => 'activo',
        ];

        // Act (Actuar)
        $response = $this->actingAs($usuario)
            ->post(route('productos.store'), $datosProducto);

        // Assert (Verificar)
        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Whisky Premium',
            'precio_venta' => 250.00,
        ]);
    }

    public function test_usuario_no_autenticado_no_puede_crear_producto(): void
    {
        $response = $this->post(route('productos.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_validacion_rechaza_datos_invalidos(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)
            ->post(route('productos.store'), [
                'nombre' => '', // vacío
                'precio_venta' => -10, // negativo
            ]);

        $response->assertSessionHasErrors(['nombre', 'precio_venta']);
    }
}
```

### 15.3 Names de Test Descriptivos

```php
// ✅ CORRECTO: Nombres descriptivos
public function test_producto_con_stock_bajo_aparece_en_alertas(): void { }
public function test_no_se_puede_eliminar_categoria_con_productos(): void { }
public function test_precio_venta_debe_ser_mayor_a_precio_compra(): void { }

// ❌ INCORRECTO: Nombres vagos
public function test_store(): void { }
public function test_1(): void { }
public function testProduct(): void { }
```

---

## 🔒 16. Seguridad

### 16.1 Reglas Críticas

1. **CSRF**: Usar `@csrf` en **TODO** formulario Blade. Inertia lo maneja automáticamente.
2. **Mass Assignment**: **SIEMPRE** definir `$fillable` o `$guarded` en cada modelo.
3. **Validación**: **SIEMPRE** validar con Form Requests, nunca confiar en input del frontend.
4. **SQL Injection**: Usar Eloquent o Query Builder. **NUNCA** concatenar input en queries raw.
5. **XSS**: Blade escapa automáticamente con `{{ }}`. Usar `{!! !!}` **SOLO** cuando sea seguro.
6. **`.env`**: **NUNCA** hacer commit de `.env`. Usar `.env.example` como plantilla.
7. **Debug mode**: `APP_DEBUG=false` en producción. **SIEMPRE**.
8. **HTTPS**: Forzar HTTPS en producción con `URL::forceScheme('https')`.
9. **Passwords**: Usar `Hash::make()`. **NUNCA** almacenar passwords en texto plano.
10. **Rate Limiting**: Aplicar rate limiting a endpoints de login y APIs públicas.

```php
// ✅ Rate limiting en rutas
Route::middleware('throttle:60,1')->group(function () {
    Route::apiResource('productos', ProductoController::class);
});

// ✅ Forzar HTTPS en producción (AppServiceProvider)
public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

---

## ⚡ 17. Rendimiento

### 17.1 Optimizaciones Esenciales

```bash
# En producción, ejecutar:
php artisan config:cache    # Cachear configuración
php artisan route:cache     # Cachear rutas
php artisan view:cache      # Cachear vistas compiladas
php artisan event:cache     # Cachear eventos
php artisan optimize        # Todo junto
```

### 17.2 Buenas Prácticas de Rendimiento

```php
// ✅ Chunking para operaciones masivas
Producto::where('activo', true)->chunk(200, function ($productos) {
    foreach ($productos as $producto) {
        // Procesar cada uno
    }
});

// ✅ Usar select() para limitar columnas
Producto::select(['id', 'nombre', 'precio_venta'])->get();

// ✅ Cachear consultas pesadas
$estadisticas = Cache::remember('dashboard.estadisticas', 600, function () {
    return [
        'total_productos' => Producto::count(),
        'stock_bajo' => Producto::conStockBajo()->count(),
        'ventas_hoy' => Venta::whereDate('created_at', today())->sum('total'),
    ];
});

// ✅ Queue para operaciones lentas
dispatch(new EnviarReporteEmail($reporte));

// ❌ INCORRECTO: Cargar todo en memoria
$todosLosProductos = Producto::all(); // ❌ Millones de registros
```

---

## 📋 18. Comandos Artisan Útiles de Laravel 12

```bash
# Generar componentes
php artisan make:model Producto -mfsc  # Modelo + Migración + Factory + Seeder + Controller
php artisan make:controller ProductoController --resource --model=Producto
php artisan make:request StoreProductoRequest
php artisan make:resource ProductoResource
php artisan make:policy ProductoPolicy --model=Producto
php artisan make:middleware EnsureUserIsAdmin
php artisan make:enum EstadoProducto  # Si se tiene el paquete adecuado
php artisan make:observer ProductoObserver --model=Producto
php artisan make:event ProductoCreado
php artisan make:listener EnviarNotificacionStock --event=ProductoCreado
php artisan make:job ProcesarImportacion
php artisan make:mail ProductoAgotadoMail
php artisan make:rule PrecioVentaValido
php artisan make:test CrearProductoTest
php artisan make:test ProductoServiceTest --unit

# Base de datos
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed --class=ProductoSeeder

# Inspección
php artisan route:list
php artisan model:show Producto

# Calidad de código
./vendor/bin/pint          # Laravel Pint para formato de código
php artisan test           # Ejecutar tests
php artisan test --filter=CrearProductoTest
```

---

## 🆕 19. Novedades de Laravel 12

### 19.1 Cambios Clave vs Laravel 11

| Aspecto | Laravel 11 | Laravel 12 |
|---------|-----------|-----------|
| **Starter Kits** | Breeze / Jetstream | Nuevos Starter Kits (React, Vue, Livewire) reemplazan a Breeze |
| **Jetstream** | Mantenimiento activo | Aún funcional pero los nuevos kits son la opción recomendada para nuevos proyectos |
| **PHP mínimo** | 8.2 | 8.2 (compatible con 8.3/8.4, forward-compatible con PHP 9) |
| **Frontend** | Tailwind CSS v3 | Tailwind CSS v4 en starter kits |
| **Kernel.php** | Eliminado en L11 | No existe, usar `bootstrap/app.php` |
| **Middleware** | En `bootstrap/app.php` | En `bootstrap/app.php` |
| **Config** | Archivos opcionales (solo sobrescrituras) | Misma filosofía: publicar solo lo que necesitas |
| **Casts** | Método `casts()` preferido | Método `casts()` es el estándar |
| **Breaking changes** | — | Mínimos (release de mantenimiento) |

### 19.2 Bootstrap de la Aplicación (Sin Kernel)

```php
// bootstrap/app.php — Laravel 12
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global, aliases, grupos, etc.
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo personalizado de excepciones
    })->create();
```

---

## ✅ 20. Checklist de Revisión de Código

Antes de hacer commit, verifica que tu código cumpla con:

### Controllers
- [ ] Controller NO contiene lógica de negocio
- [ ] Validación delegada a Form Requests
- [ ] Inyección de dependencias por constructor
- [ ] Autorización con Policies/Gates
- [ ] Route Model Binding utilizado
- [ ] Respuestas con Resources para API

### Modelos
- [ ] Fillable definido (no usar `$guarded = []` en producción)
- [ ] Casts definidos con método `casts()`
- [ ] Relaciones con return types explícitos
- [ ] Scopes para queries reutilizables
- [ ] Accessors/Mutators con sintaxis `Attribute`

### Rutas
- [ ] URLs en `kebab-case` y plurales
- [ ] Nombres con notación punto (`productos.index`)
- [ ] Resource routes cuando aplique
- [ ] Agrupación por middleware

### Vistas
- [ ] Sin queries ni lógica de negocio
- [ ] Componentes reutilizables
- [ ] `@csrf` en formularios Blade
- [ ] Datos recibidos del controller, no calculados

### Base de Datos
- [ ] Migraciones con índices apropiados
- [ ] Foreign keys con restricciones
- [ ] Factories con estados personalizados
- [ ] Seeders para datos de prueba

### Seguridad
- [ ] Form Requests validan todo input
- [ ] Rate limiting en APIs
- [ ] `.env` no está versionado
- [ ] `APP_DEBUG=false` en producción
- [ ] Passwords hasheados con `Hash::make()`

### Rendimiento
- [ ] Eager loading para relaciones
- [ ] `preventLazyLoading()` habilitado en desarrollo
- [ ] Cache para queries pesadas
- [ ] Jobs para operaciones lentas
- [ ] `select()` para limitar columnas

### Testing
- [ ] Test para cada caso de uso principal
- [ ] Tests con nombres descriptivos
- [ ] Patrón AAA (Arrange, Act, Assert)
- [ ] RefreshDatabase en feature tests
- [ ] Factories para datos de prueba

---

## 📚 Referencias Oficiales

- [Documentación Laravel 12](https://laravel.com/docs/12.x)
- [Laravel News — Lanzamiento L12](https://laravel-news.com/laravel-12)
- [Laravel Best Practices (GitHub)](https://github.com/alexeymezenin/laravel-best-practices)
- [PHP PSR Standards](https://www.php-fig.org/psr/)
- [Laravel Pint (Code Style)](https://laravel.com/docs/12.x/pint)
