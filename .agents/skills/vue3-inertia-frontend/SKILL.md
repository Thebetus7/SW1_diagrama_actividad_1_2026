---
name: vue3-inertia-frontend
description: Guía estricta de mejores prácticas para el frontend con Vue 3, Inertia.js 2, Tailwind CSS y Vite integrado en Laravel 12 (stack VILT). Cubre estructura de carpetas, componentes, composables, Pages, Layouts, formularios, estado, slots, rendimiento, SEO, naming conventions y patrones de diseño.
---

# 🎨 Mejores Prácticas Frontend — Vue 3 + Inertia.js + Laravel 12

> **Stack VILT:** Vue 3 + Inertia.js 2 + Laravel 12 + Tailwind CSS  
> **Bundler:** Vite 7+  
> **Router de navegación:** Ziggy (genera rutas de Laravel en JS)  
> **Auth scaffolding:** Jetstream + Sanctum  
> **Sintaxis obligatoria:** `<script setup>` + Composition API

---

## 📁 1. Estructura de Carpetas del Frontend

### 1.1 Estructura Completa Recomendada

```
resources/
├── css/
│   ├── app.css                     # Entry point de estilos (importa Tailwind)
│   └── components/                 # Estilos CSS aislados por componente (si aplica)
│       └── _data-table.css
│
├── js/
│   ├── app.js                      # Entry point principal (createInertiaApp)
│   ├── bootstrap.js                # Configuración de Axios, Echo, etc.
│   │
│   ├── Components/                 # Componentes Vue reutilizables
│   │   ├── ui/                     # Componentes genéricos de UI (botones, modales, inputs)
│   │   │   ├── AppButton.vue
│   │   │   ├── AppModal.vue
│   │   │   ├── AppDataTable.vue
│   │   │   ├── AppDropdown.vue
│   │   │   ├── AppTextInput.vue
│   │   │   ├── AppCheckbox.vue
│   │   │   ├── AppInputLabel.vue
│   │   │   ├── AppInputError.vue
│   │   │   ├── AppBadge.vue
│   │   │   ├── AppCard.vue
│   │   │   ├── AppConfirmationModal.vue
│   │   │   └── AppPagination.vue
│   │   │
│   │   ├── productos/              # Componentes específicos del módulo Productos
│   │   │   ├── ProductoCard.vue
│   │   │   ├── ProductoForm.vue
│   │   │   └── ProductoStockBadge.vue
│   │   │
│   │   ├── ventas/                 # Componentes específicos del módulo Ventas
│   │   │   ├── VentaResumen.vue
│   │   │   └── VentaItemRow.vue
│   │   │
│   │   ├── categorias/
│   │   │   └── CategoriaSelector.vue
│   │   │
│   │   └── charts/                 # Componentes de gráficos/reportes
│   │       ├── VentasChart.vue
│   │       └── StockChart.vue
│   │
│   ├── Composables/                # Composables (lógica reutilizable)
│   │   ├── useProductos.js
│   │   ├── useNotificaciones.js
│   │   ├── useConfirmacion.js
│   │   ├── usePaginacion.js
│   │   ├── usePermisos.js
│   │   └── useBusqueda.js
│   │
│   ├── Layouts/                    # Layouts persistentes de Inertia
│   │   ├── AppLayout.vue           # Layout principal (authenticated)
│   │   ├── GuestLayout.vue         # Layout para visitantes (login, register)
│   │   └── AdminLayout.vue         # Layout del panel admin (si aplica)
│   │
│   ├── Pages/                      # Páginas Inertia (mapeadas desde controllers)
│   │   ├── Dashboard.vue
│   │   ├── Welcome.vue
│   │   ├── Productos/
│   │   │   ├── Index.vue
│   │   │   ├── Show.vue
│   │   │   ├── Create.vue
│   │   │   └── Edit.vue
│   │   ├── Ventas/
│   │   │   ├── Index.vue
│   │   │   ├── Show.vue
│   │   │   └── Create.vue
│   │   ├── Categorias/
│   │   │   ├── Index.vue
│   │   │   └── Show.vue
│   │   ├── Auth/
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   └── ForgotPassword.vue
│   │   └── Profile/
│   │       └── Show.vue
│   │
│   ├── Stores/                     # Stores Pinia (estado global, si se necesita)
│   │   ├── useCartStore.js
│   │   └── useNotificationStore.js
│   │
│   └── Utils/                      # Funciones utilitarias puras
│       ├── formatters.js           # Formateo de moneda, fechas, etc.
│       ├── validators.js           # Validaciones del lado del cliente
│       └── constants.js            # Constantes globales del frontend
│
└── views/                          # Vistas Blade (solo para emails, PDFs, layouts base)
    ├── app.blade.php               # Template raíz de Inertia
    └── emails/
        └── bienvenida.blade.php
```

### 1.2 Reglas de Estructura

- **Pages/** mapea **1:1** con las rutas del backend. Si la ruta es `/productos`, la Page está en `Pages/Productos/Index.vue`.
- **Components/** se organiza por **módulo/feature**, NO mezclados en la raíz.
- **Componentes genéricos UI** van en `Components/ui/` con prefijo `App` para diferenciarlos.
- **Composables/** contienen lógica reutilizable con prefijo `use`.
- **Stores/** solo usar si necesitas estado **global persistente** entre páginas. Para datos de una sola página, usa props de Inertia.
- **Utils/** para funciones puras sin estado (formateo, constantes).
- **NUNCA** poner lógica de negocio en componentes de UI.
- **NUNCA** mezclar componentes de distintos módulos en una misma carpeta.

---

## 🧩 2. Componentes Vue 3

### 2.1 Convenciones de Nombrado

| Tipo | Convención | Ejemplo |
|------|------------|---------|
| Componente UI genérico | `PascalCase` + prefijo `App` | `AppButton.vue`, `AppModal.vue` |
| Componente de módulo | `PascalCase` + nombre del módulo | `ProductoCard.vue`, `VentaResumen.vue` |
| Página Inertia | `PascalCase` (CRUD names) | `Index.vue`, `Show.vue`, `Create.vue`, `Edit.vue` |
| Layout | `PascalCase` + sufijo `Layout` | `AppLayout.vue`, `GuestLayout.vue` |
| Composable | `camelCase` + prefijo `use` | `useProductos.js`, `useNotificaciones.js` |
| Store Pinia | `camelCase` + prefijo `use` + sufijo `Store` | `useCartStore.js` |
| Utilidad | `camelCase` | `formatters.js`, `validators.js` |
| Evento emitido | `camelCase` | `@update:modelValue`, `@productoSeleccionado` |
| Prop | `camelCase` en JS, `kebab-case` en template | `:precio-venta="..."` → `const props = defineProps({ precioVenta: ... })` |
| Variable CSS | `kebab-case` con prefijo `--` | `--color-primary`, `--spacing-md` |

### 2.2 Estructura Interna de un Componente (Orden Estricto)

```vue
<!-- ✅ CORRECTO: Orden estricto dentro de un SFC (Single File Component) -->
<script setup>
// 1️⃣ Imports (externos primero, luego internos)
import { ref, computed, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// 2️⃣ Imports de componentes
import AppButton from '@/Components/ui/AppButton.vue'
import AppModal from '@/Components/ui/AppModal.vue'
import ProductoCard from '@/Components/productos/ProductoCard.vue'

// 3️⃣ Imports de composables
import { useConfirmacion } from '@/Composables/useConfirmacion'
import { useNotificaciones } from '@/Composables/useNotificaciones'

// 4️⃣ Props (siempre tipadas con defaults si aplica)
const props = defineProps({
    productos: {
        type: Array,
        required: true,
    },
    categorias: {
        type: Array,
        default: () => [],
    },
    filtroActual: {
        type: String,
        default: '',
    },
})

// 5️⃣ Emits (declarados explícitamente)
const emit = defineEmits(['productoSeleccionado', 'filtroAplicado'])

// 6️⃣ Composables (desestructurados)
const { confirmar, modalVisible } = useConfirmacion()
const { notificar } = useNotificaciones()

// 7️⃣ Estado reactivo (ref y reactive)
const busqueda = ref('')
const productoSeleccionado = ref(null)
const cargando = ref(false)

// 8️⃣ Computed properties
const productosFiltrados = computed(() => {
    if (!busqueda.value) return props.productos
    const termino = busqueda.value.toLowerCase()
    return props.productos.filter(p =>
        p.nombre.toLowerCase().includes(termino)
    )
})

const totalProductos = computed(() => productosFiltrados.value.length)

// 9️⃣ Watchers
watch(busqueda, (nuevoValor) => {
    emit('filtroAplicado', nuevoValor)
})

// 🔟 Métodos / Funciones
function seleccionarProducto(producto) {
    productoSeleccionado.value = producto
    emit('productoSeleccionado', producto)
}

async function eliminarProducto(producto) {
    const confirmado = await confirmar(
        '¿Eliminar producto?',
        `Se eliminará "${producto.nombre}" permanentemente.`
    )
    if (!confirmado) return

    router.delete(route('productos.destroy', producto.id), {
        onSuccess: () => notificar('Producto eliminado', 'success'),
    })
}

// 1️⃣1️⃣ Lifecycle hooks
onMounted(() => {
    // Inicialización
})
</script>

<template>
    <!-- El template va DESPUÉS del script -->
    <div class="productos-lista">
        <!-- Contenido -->
    </div>
</template>

<style scoped>
/* Estilos al final, siempre con scoped */
.productos-lista {
    /* ... */
}
</style>
```

### 2.3 Reglas Críticas para Componentes

```vue
<!-- ✅ CORRECTO: Usar <script setup> SIEMPRE -->
<script setup>
import { ref } from 'vue'
const count = ref(0)
</script>

<!-- ❌ INCORRECTO: Options API -->
<script>
export default {
    data() {
        return { count: 0 }  // ❌ No usar Options API
    }
}
</script>
```

```vue
<!-- ✅ CORRECTO: Props tipadas explícitamente -->
<script setup>
const props = defineProps({
    titulo: {
        type: String,
        required: true,
    },
    items: {
        type: Array,
        default: () => [],
        validator: (value) => value.every(item => item.id),
    },
})
</script>

<!-- ❌ INCORRECTO: Props sin tipar -->
<script setup>
const props = defineProps(['titulo', 'items'])  // ❌ Sin tipos
</script>
```

```vue
<!-- ✅ CORRECTO: Emits declarados -->
<script setup>
const emit = defineEmits(['actualizar', 'eliminar', 'update:modelValue'])

function handleClick() {
    emit('actualizar', { id: 1 })
}
</script>

<!-- ❌ INCORRECTO: Emits no declarados -->
<script setup>
// ❌ No declarar emits hace el componente impredecible
</script>
```

---

## 🔄 3. Composables (Lógica Reutilizable)

### 3.1 Principios

- Un composable resuelve **un solo problema** de forma reutilizable.
- **SIEMPRE** prefijo `use`.
- Retornan estado reactivo y/o funciones.
- **NO** contienen lógica de presentación (eso es del componente).

### 3.2 Ejemplo Completo: `useProductos.js`

```javascript
// resources/js/Composables/useProductos.js
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

export function useProductos(productosIniciales = []) {
    // Estado reactivo
    const productos = ref(productosIniciales)
    const busqueda = ref('')
    const cargando = ref(false)

    // Computed
    const productosFiltrados = computed(() => {
        if (!busqueda.value) return productos.value
        const termino = busqueda.value.toLowerCase()
        return productos.value.filter(p =>
            p.nombre.toLowerCase().includes(termino)
        )
    })

    const totalProductos = computed(() => productos.value.length)

    const productosConStockBajo = computed(() =>
        productos.value.filter(p => p.stock <= p.stock_minimo)
    )

    // Métodos
    function buscar(termino) {
        busqueda.value = termino
    }

    function eliminar(productoId) {
        cargando.value = true
        router.delete(route('productos.destroy', productoId), {
            preserveScroll: true,
            onSuccess: () => {
                productos.value = productos.value.filter(p => p.id !== productoId)
            },
            onFinish: () => {
                cargando.value = false
            },
        })
    }

    // Retornar SOLO lo que el componente necesita
    return {
        productos,
        busqueda,
        cargando,
        productosFiltrados,
        totalProductos,
        productosConStockBajo,
        buscar,
        eliminar,
    }
}
```

### 3.3 Ejemplo: `useConfirmacion.js`

```javascript
// resources/js/Composables/useConfirmacion.js
import { ref } from 'vue'

export function useConfirmacion() {
    const visible = ref(false)
    const titulo = ref('')
    const mensaje = ref('')
    let resolverPromesa = null

    function confirmar(tituloParam, mensajeParam) {
        titulo.value = tituloParam
        mensaje.value = mensajeParam
        visible.value = true

        return new Promise((resolve) => {
            resolverPromesa = resolve
        })
    }

    function aceptar() {
        visible.value = false
        resolverPromesa?.(true)
    }

    function cancelar() {
        visible.value = false
        resolverPromesa?.(false)
    }

    return {
        visible,
        titulo,
        mensaje,
        confirmar,
        aceptar,
        cancelar,
    }
}
```

### 3.4 Ejemplo: `usePermisos.js`

```javascript
// resources/js/Composables/usePermisos.js
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermisos() {
    const page = usePage()

    const usuario = computed(() => page.props.auth?.user)
    const roles = computed(() => usuario.value?.roles ?? [])

    function tieneRol(rol) {
        return roles.value.includes(rol)
    }

    function tienePermiso(permiso) {
        return usuario.value?.permissions?.includes(permiso) ?? false
    }

    const esAdmin = computed(() => tieneRol('admin'))
    const esInventario = computed(() => tieneRol('inventario'))

    return {
        usuario,
        roles,
        tieneRol,
        tienePermiso,
        esAdmin,
        esInventario,
    }
}
```

---

## 📄 4. Pages (Páginas Inertia)

### 4.1 Convenciones

| Acción CRUD | Archivo en Pages | Ruta Laravel | Método del Controller |
|-------------|------------------|--------------|-----------------------|
| Listar | `Pages/Productos/Index.vue` | `GET /productos` | `index()` |
| Ver detalle | `Pages/Productos/Show.vue` | `GET /productos/{id}` | `show()` |
| Crear (form) | `Pages/Productos/Create.vue` | `GET /productos/create` | `create()` |
| Editar (form) | `Pages/Productos/Edit.vue` | `GET /productos/{id}/edit` | `edit()` |

### 4.2 Persistent Layouts (Layouts Persistentes)

```vue
<!-- ✅ CORRECTO: Definir layout persistente con defineOptions -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({
    layout: AppLayout,
})

// Props recibidas del controller
const props = defineProps({
    productos: Array,
    categorias: Array,
})
</script>

<template>
    <div>
        <h1>Productos</h1>
        <!-- Contenido de la página -->
    </div>
</template>
```

```javascript
// ✅ O definir layout por defecto en app.js
createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        )
        // Layout por defecto para todas las páginas
        page.then((module) => {
            module.default.layout = module.default.layout || AppLayout
        })
        return page
    },
    // ...
})
```

### 4.3 Ejemplo de Page Completa: `Productos/Index.vue`

```vue
<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Componentes
import AppButton from '@/Components/ui/AppButton.vue'
import AppDataTable from '@/Components/ui/AppDataTable.vue'
import AppConfirmationModal from '@/Components/ui/AppConfirmationModal.vue'
import AppPagination from '@/Components/ui/AppPagination.vue'
import ProductoStockBadge from '@/Components/productos/ProductoStockBadge.vue'

// Composables
import { useConfirmacion } from '@/Composables/useConfirmacion'
import { usePermisos } from '@/Composables/usePermisos'

defineOptions({
    layout: AppLayout,
})

// Props desde el controller Laravel
const props = defineProps({
    productos: {
        type: Object, // Paginado de Laravel
        required: true,
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },
})

// Composables
const { confirmar, visible, titulo, mensaje, aceptar, cancelar } = useConfirmacion()
const { esAdmin } = usePermisos()

// Estado local
const busqueda = ref(props.filtros.busqueda ?? '')

// Métodos
function buscar() {
    router.get(route('productos.index'), {
        busqueda: busqueda.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

async function eliminar(producto) {
    const confirmado = await confirmar(
        'Eliminar producto',
        `¿Estás seguro de eliminar "${producto.nombre}"?`
    )
    if (!confirmado) return

    router.delete(route('productos.destroy', producto.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <!-- SEO: Head dinámico con título de página -->
    <Head title="Productos" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Productos</h1>

                <Link
                    v-if="esAdmin"
                    :href="route('productos.create')"
                    class="btn btn-primary"
                >
                    + Nuevo Producto
                </Link>
            </div>

            <!-- Barra de búsqueda -->
            <div class="mb-4">
                <input
                    v-model="busqueda"
                    type="search"
                    placeholder="Buscar producto..."
                    class="input-search"
                    @keyup.enter="buscar"
                />
            </div>

            <!-- Tabla de productos -->
            <AppDataTable
                :columns="['Nombre', 'Categoría', 'Precio', 'Stock', 'Estado', 'Acciones']"
            >
                <tr v-for="producto in productos.data" :key="producto.id">
                    <td>{{ producto.nombre }}</td>
                    <td>{{ producto.categoria?.nombre }}</td>
                    <td>Bs. {{ producto.precio_venta }}</td>
                    <td>
                        <ProductoStockBadge
                            :stock="producto.stock"
                            :stock-minimo="producto.stock_minimo"
                        />
                    </td>
                    <td>{{ producto.estado_etiqueta }}</td>
                    <td class="flex gap-2">
                        <Link :href="route('productos.edit', producto.id)">
                            <AppButton variant="secondary" size="sm">Editar</AppButton>
                        </Link>
                        <AppButton
                            v-if="esAdmin"
                            variant="danger"
                            size="sm"
                            @click="eliminar(producto)"
                        >
                            Eliminar
                        </AppButton>
                    </td>
                </tr>
            </AppDataTable>

            <!-- Paginación -->
            <AppPagination :links="productos.links" class="mt-4" />
        </div>
    </div>

    <!-- Modal de confirmación -->
    <AppConfirmationModal
        :show="visible"
        :title="titulo"
        :message="mensaje"
        @confirm="aceptar"
        @cancel="cancelar"
    />
</template>
```

---

### 4.4 Estructura de Subdirectorios de `Pages/` — Convención por Dominio

La carpeta `Pages/` se organiza **por dominio funcional**. Cada subdirectorio representa un contexto de la aplicación, directamente mapeado con las rutas del backend. Esta regla es **adaptable**: cuando se crea un nuevo módulo, simplemente se añade un nuevo subdirectorio siguiendo el mismo patrón.

#### Patrón de estructura (aplica a cualquier módulo presente o futuro):

```
resources/js/Pages/
├── Dashboard.vue                   # Páginas sin módulo propio (mínimo)
├── Welcome.vue
│
├── [Dominio]/                      # ← Un directorio por cada módulo/dominio
│   ├── Index.vue                   # Listado principal del dominio
│   ├── Show.vue                    # Vista de detalle
│   ├── Create.vue                  # Formulario de creación
│   ├── Edit.vue                    # Formulario de edición
│   └── Partials/                   # Sub-secciones privadas de este dominio
│       └── [SeccionEspecifica].vue
│
│  ── Módulos existentes en el proyecto ──
├── Auth/                     → Autenticación (Login, Register, ForgotPassword…)
├── API/                      → Gestión de tokens de API
│   └── Partials/             → ApiTokenManager.vue, etc.
├── Profile/                  → Perfil del usuario
│   └── Partials/             → UpdatePasswordForm.vue, etc.
└── PoliticaNegocio/          → Diagramas de política de negocio
    └── Partials/             → (secciones internas del diagrama si creciera)
```

> **Regla de oro:** Cuando aparece un nuevo módulo (ej. `Reportes`, `Usuarios`, `Inventario`),
> simplemente se crea `Pages/Reportes/` siguiendo exactamente el mismo patrón.
> **No hay excepciones a la organización por dominio.**

#### Reglas de subdirectorios de `Pages/`

| Regla | Descripción |
|-------|-------------|
| **Subdirectorio por dominio** | Cada módulo funcional tiene su propia carpeta. Siempre. |
| **`Partials/` dentro de Pages** | Secciones privadas de una Page compleja que NO aplican en otros contextos. |
| **Archivos en raíz de `Pages/`** | Solo páginas sin módulo claro (`Dashboard.vue`, `Welcome.vue`). Mantener al mínimo. |
| **Nombres CRUD estándar** | Preferir: `Index.vue`, `Show.vue`, `Create.vue`, `Edit.vue`. |
| **1 Page = 1 ruta** | Nunca combinar múltiples rutas en un mismo archivo `.vue`. |

```
✅ Pages/Reportes/Index.vue           → GET /reportes
✅ Pages/Usuarios/Edit.vue            → GET /usuarios/{id}/edit
✅ Pages/Auth/Login.vue               → GET /login
✅ Pages/Profile/Partials/UpdatePasswordForm.vue  → sub-sección de Profile/Show
❌ Pages/Auth/AuthHelper.vue          → lógica de soporte (va en Composables/)
❌ Pages/General.vue                  → nombre ambiguo, sin dominio claro
```

---

### 4.5 Política de Reutilización: Cuándo extraer a su propio archivo

Este principio responde a la pregunta más frecuente del desarrollo:
**¿escribo esto aquí o lo extraigo a un componente?**

#### Árbol de decisión completo

```
¿El bloque visual/lógico es extenso O complejo?  (muchas líneas, props, lógica propia)
│
├─ NO → Déjalo inline en la Page o Partial actual.
│
└─ SÍ → ¿Se usa en MÁS DE UNA Page o Partial?
          │
          ├─ NO (solo aquí por ahora)
          │    └─ Créalo en Pages/[Dominio]/Partials/[Nombre].vue
          │       (es privado de este dominio; si después se reutiliza, se promueve)
          │
          └─ SÍ (se reutiliza en múltiples lugares)
                 ├─ ¿Solo dentro del mismo módulo?
                 │    └─ Components/[modulo]/[NombreComponente].vue
                 │
                 └─ ¿En módulos distintos (o es genérico)?
                      └─ Components/ui/App[NombreComponente].vue
```

**Regla de la duplicación:** si estás a punto de copiar/pegar un Partial en un segundo lugar → **promúevelo a `Components/`**.

---

### 4.6 Caso Práctico 1 — Tabla Extensa: Extraer a Componente con Props

Cuando una tabla tiene columnas, estilos, paginación y lógica propia que resultan extensos,
se extrae a su propio archivo y se alimenta **solo con props y datos variables**.

#### Paso 1 — Crear el componente de tabla en `Components/[modulo]/`

```vue
<!-- ✅ Components/usuarios/UsuariosTable.vue -->
<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// Solo recibe los datos variables como props
const props = defineProps({
    usuarios: {
        type: Object,   // objeto paginado de Laravel
        required: true,
    },
    columnas: {
        type: Array,
        default: () => ['Nombre', 'Email', 'Rol', 'Estado', 'Acciones'],
    },
})

const emit = defineEmits(['eliminar'])
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="col in columnas"
                        :key="col"
                        class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider"
                    >
                        {{ col }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr
                    v-for="usuario in usuarios.data"
                    :key="usuario.id"
                    class="hover:bg-gray-50 transition-colors"
                >
                    <td class="px-4 py-3">{{ usuario.nombre }}</td>
                    <td class="px-4 py-3">{{ usuario.email }}</td>
                    <td class="px-4 py-3">
                        <span class="badge">{{ usuario.rol }}</span>
                    </td>
                    <td class="px-4 py-3">{{ usuario.estado }}</td>

                    <!-- Slot para las acciones: el padre decide qué botones mostrar -->
                    <td class="px-4 py-3">
                        <slot name="acciones" :usuario="usuario" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
```

#### Paso 2 — Usar el componente en la Page, pasando solo las variables

```vue
<!-- ✅ Pages/Usuarios/Index.vue -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import UsuariosTable from '@/Components/usuarios/UsuariosTable.vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

defineOptions({ layout: AppLayout })

const props = defineProps({
    usuarios: { type: Object, required: true },
})

function eliminar(id) {
    router.delete(route('usuarios.destroy', id), { preserveScroll: true })
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="py-6 max-w-7xl mx-auto px-4">
        <h1 class="text-2xl font-bold mb-6">Usuarios</h1>

        <!-- ✅ Solo se pasan los datos; el diseño extenso vive en el componente -->
        <UsuariosTable :usuarios="usuarios">
            <template #acciones="{ usuario }">
                <Link :href="route('usuarios.edit', usuario.id)">Editar</Link>
                <button @click="eliminar(usuario.id)">Eliminar</button>
            </template>
        </UsuariosTable>
    </div>
</template>
```

**Beneficio:** La Page queda limpia. Si el diseño de la tabla cambia, solo hay que editar `UsuariosTable.vue`.

---

### 4.7 Caso Práctico 2 — Variación por Rol: Mismo Componente, Distintas Acciones

Cuando el **mismo componente** (ej. una tabla) se usa para dos roles, pero:
- **Rol Admin (CRUD completo):** puede ver, editar y eliminar.
- **Rol Lector (solo lectura):** solo puede ver.

**La solución es el slot `acciones`** — el componente ofrece el slot pero **no lo rellena**.
Cada Page (o cada llamada) decide qué acciones mostrar.

```vue
<!-- ✅ Patrón: el MISMO UsuariosTable.vue para ambos roles -->

<!-- Para el Admin (CRUD completo): -->
<UsuariosTable :usuarios="usuarios">
    <template #acciones="{ usuario }">
        <Link :href="route('usuarios.edit', usuario.id)">
            <SecondaryButton size="sm">Editar</SecondaryButton>
        </Link>
        <DangerButton size="sm" @click="eliminar(usuario.id)">
            Eliminar
        </DangerButton>
    </template>
</UsuariosTable>

<!-- Para el Lector (solo lectura): se omite el slot de acciones o se pasa uno vacío -->
<UsuariosTable :usuarios="usuarios">
    <template #acciones="{ usuario }">
        <Link :href="route('usuarios.show', usuario.id)">
            <SecondaryButton size="sm">Ver</SecondaryButton>
        </Link>
    </template>
</UsuariosTable>
```

#### Alternativa con prop `modo` cuando las diferencias son simples

Si la variante es pequeña (solo mostrar/ocultar botones, sin lógica extra), se puede usar una prop `modo`:

```vue
<!-- ✅ Components/usuarios/UsuariosTable.vue — con prop modo -->
<script setup>
const props = defineProps({
    usuarios: { type: Object, required: true },
    modo: {
        type: String,
        default: 'lectura',
        validator: (v) => ['lectura', 'admin'].includes(v),
    },
})

const emit = defineEmits(['editar', 'eliminar'])
</script>

<template>
    <table>
        <!-- ... columnas ... -->
        <tbody>
            <tr v-for="usuario in usuarios.data" :key="usuario.id">
                <!-- ...celdas de datos... -->
                <td>
                    <!-- Acciones condicionales por rol -->
                    <button @click="$emit('editar', usuario)">Ver</button>

                    <!-- Solo visible en modo admin -->
                    <template v-if="modo === 'admin'">
                        <button @click="$emit('editar', usuario)">Editar</button>
                        <button @click="$emit('eliminar', usuario.id)">Eliminar</button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
</template>
```

```vue
<!-- En la Page del Admin: -->
<UsuariosTable :usuarios="usuarios" modo="admin"
    @editar="irAEditar"
    @eliminar="confirmarEliminar"
/>

<!-- En la Page del Lector: -->
<UsuariosTable :usuarios="usuarios" modo="lectura"
    @editar="irADetalle"
/>
```

#### ¿Cuándo usar slot vs prop `modo`?

| Criterio | Usa slot `#acciones` | Usa prop `modo` |
|----------|---------------------|-----------------|
| Las acciones tienen **markup muy diferente** entre roles | ✅ | ❌ |
| Solo hay que **mostrar/ocultar** elementos similares | ❌ | ✅ |
| Hay **más de 2 variantes** posibles | ✅ (más flexible) | ❌ (demasiados ifs) |
| El componente es **genérico** (ui/) | ✅ (preferir slots) | Solo si es sencillo |
| El componente es de **módulo** específico | Cualquiera | Cualquiera |

---

### 4.8 Política de Reutilización: `Pages/Partials/` vs `Components/`

#### Estado de `Components/` en el proyecto (Jetstream base)

El proyecto tiene componentes Jetstream en la raíz. Los **nuevos componentes** siempre van en subcarpetas.

```
resources/js/Components/
│
│  ── Componentes Jetstream (raíz, NO colocar nuevos aquí) ──
├── AuthenticationCard.vue     ← usar en Pages/Auth/
├── Checkbox.vue               ← reutilizar donde se necesite
├── ConfirmationModal.vue      ← reutilizar para modales de confirmación
├── DangerButton.vue           ← reutilizar para acciones destructivas
├── DialogModal.vue
├── Dropdown.vue / DropdownLink.vue
├── FormSection.vue / ActionSection.vue / SectionTitle.vue
├── InputError.vue / InputLabel.vue / TextInput.vue
├── Modal.vue
├── NavLink.vue / ResponsiveNavLink.vue
├── PrimaryButton.vue / SecondaryButton.vue
└── ...
│
│  ── NUEVOS componentes (siempre en subcarpetas) ──
├── ui/                        ← Genéricos multi-módulo, prefijo App
│   ├── AppBadge.vue
│   ├── AppCard.vue
│   ├── AppPagination.vue
│   └── AppStatusBadge.vue
│
├── [modulo]/                  ← Componentes del módulo (ej: usuarios/, reportes/)
│   └── [Nombre]Table.vue      ← Nombrarlo según su función, no su tipo
│
└── (nuevo-modulo)/            ← Al agregar un módulo, crear su subcarpeta aquí
```

#### Cuándo PROMOVER un Partial a Componente

| Situación | Dónde va |
|-----------|----------|
| Aparece por primera vez en una Page | `Pages/[Dominio]/Partials/[Nombre].vue` |
| Se necesita en **dos Pages del mismo módulo** | Mover a `Components/[modulo]/[Nombre].vue` |
| Se necesita en **módulos distintos** | Mover a `Components/ui/App[Nombre].vue` |
| Un componente Jetstream ya cubre el caso | ✅ Reutilizarlo directamente desde `Components/` |

#### Reglas críticas

- **NUNCA** crear en `Components/` un componente de uso único.
- **SIEMPRE** verificar si ya existe un componente Jetstream que resuelva el caso.
- **Los nuevos archivos** nunca van en la raíz de `Components/`, siempre en subcarpetas.
- **Componentes en `ui/`** deben llevar prefijo `App` y ser agnósticos al negocio.


## 📝 5. Formularios con Inertia `useForm`

### 5.1 Principio Fundamental

**SIEMPRE** usar `useForm` de Inertia para formularios. **NUNCA** gestionar formularios manualmente con `ref` + `axios`.

### 5.2 Ejemplo: Formulario de Crear Producto

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/ui/AppButton.vue'
import AppTextInput from '@/Components/ui/AppTextInput.vue'
import AppInputLabel from '@/Components/ui/AppInputLabel.vue'
import AppInputError from '@/Components/ui/AppInputError.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    categorias: {
        type: Array,
        required: true,
    },
})

// ✅ useForm: maneja datos, errors, processing, etc.
const form = useForm({
    nombre: '',
    descripcion: '',
    precio_compra: '',
    precio_venta: '',
    stock: 0,
    stock_minimo: 5,
    categoria_id: '',
    estado: 'activo',
})

function enviarFormulario() {
    form.post(route('productos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset() // Limpiar después de éxito
        },
    })
}
</script>

<template>
    <Head title="Crear Producto" />

    <div class="max-w-2xl mx-auto py-6 px-4">
        <h1 class="text-2xl font-bold mb-6">Nuevo Producto</h1>

        <form @submit.prevent="enviarFormulario" class="space-y-4">
            <!-- Nombre -->
            <div>
                <AppInputLabel for="nombre" value="Nombre" />
                <AppTextInput
                    id="nombre"
                    v-model="form.nombre"
                    type="text"
                    required
                    autofocus
                />
                <AppInputError :message="form.errors.nombre" />
            </div>

            <!-- Precio Compra -->
            <div>
                <AppInputLabel for="precio_compra" value="Precio de Compra" />
                <AppTextInput
                    id="precio_compra"
                    v-model="form.precio_compra"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                />
                <AppInputError :message="form.errors.precio_compra" />
            </div>

            <!-- Precio Venta -->
            <div>
                <AppInputLabel for="precio_venta" value="Precio de Venta" />
                <AppTextInput
                    id="precio_venta"
                    v-model="form.precio_venta"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                />
                <AppInputError :message="form.errors.precio_venta" />
            </div>

            <!-- Categoría -->
            <div>
                <AppInputLabel for="categoria_id" value="Categoría" />
                <select
                    id="categoria_id"
                    v-model="form.categoria_id"
                    class="input-select"
                    required
                >
                    <option value="" disabled>Seleccionar categoría...</option>
                    <option
                        v-for="cat in categorias"
                        :key="cat.id"
                        :value="cat.id"
                    >
                        {{ cat.nombre }}
                    </option>
                </select>
                <AppInputError :message="form.errors.categoria_id" />
            </div>

            <!-- Stock -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <AppInputLabel for="stock" value="Stock inicial" />
                    <AppTextInput
                        id="stock"
                        v-model="form.stock"
                        type="number"
                        min="0"
                        required
                    />
                    <AppInputError :message="form.errors.stock" />
                </div>
                <div>
                    <AppInputLabel for="stock_minimo" value="Stock mínimo" />
                    <AppTextInput
                        id="stock_minimo"
                        v-model="form.stock_minimo"
                        type="number"
                        min="0"
                        required
                    />
                    <AppInputError :message="form.errors.stock_minimo" />
                </div>
            </div>

            <!-- Botón enviar -->
            <div class="flex justify-end gap-3">
                <AppButton
                    type="button"
                    variant="secondary"
                    @click="form.reset()"
                >
                    Limpiar
                </AppButton>
                <AppButton
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                    :loading="form.processing"
                >
                    Crear Producto
                </AppButton>
            </div>
        </form>
    </div>
</template>
```

### 5.3 Formulario de Edición (con PUT)

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    producto: { type: Object, required: true },
    categorias: { type: Array, required: true },
})

// ✅ Prellenar con datos existentes
const form = useForm({
    nombre: props.producto.nombre,
    descripcion: props.producto.descripcion ?? '',
    precio_compra: props.producto.precio_compra,
    precio_venta: props.producto.precio_venta,
    stock: props.producto.stock,
    stock_minimo: props.producto.stock_minimo,
    categoria_id: props.producto.categoria_id,
    estado: props.producto.estado,
})

function actualizarProducto() {
    form.put(route('productos.update', props.producto.id), {
        preserveScroll: true,
    })
}
</script>
```

### 5.4 Propiedades Clave de `useForm`

```javascript
const form = useForm({ nombre: '' })

// Propiedades reactivas:
form.data()            // Obtener datos del formulario como objeto
form.errors            // Errores de validación del backend (objeto)
form.errors.nombre     // Error específico de un campo
form.hasErrors         // boolean: ¿tiene errores?
form.processing        // boolean: ¿se está enviando?
form.progress          // Progreso de upload (de 0 a 100)
form.wasSuccessful     // boolean: ¿fue exitoso el último envío?
form.recentlySuccessful // boolean: fue exitoso en los últimos 2 segundos (útil para feedback)

// Métodos:
form.reset()           // Reiniciar todos los campos a valores iniciales
form.reset('nombre')   // Reiniciar un campo específico
form.clearErrors()     // Limpiar todos los errores
form.clearErrors('nombre') // Limpiar error de un campo

// Métodos de envío:
form.get(url, options)
form.post(url, options)
form.put(url, options)
form.patch(url, options)
form.delete(url, options)
form.transform(callback) // Transformar datos antes de enviar
```

---

## 🧱 6. Slots y Scoped Slots

### 6.1 Slots por Defecto

```vue
<!-- Components/ui/AppCard.vue -->
<template>
    <div class="card">
        <div class="card-body">
            <!-- Slot por defecto con fallback -->
            <slot>
                <p class="text-gray-400">Sin contenido</p>
            </slot>
        </div>
    </div>
</template>

<!-- Uso -->
<AppCard>
    <h2>Título del producto</h2>
    <p>Descripción aquí</p>
</AppCard>
```

### 6.2 Named Slots

```vue
<!-- Components/ui/AppCard.vue -->
<template>
    <div class="card">
        <div v-if="$slots.header" class="card-header">
            <slot name="header" />
        </div>

        <div class="card-body">
            <slot />
        </div>

        <div v-if="$slots.footer" class="card-footer">
            <slot name="footer" />
        </div>
    </div>
</template>

<!-- Uso -->
<AppCard>
    <template #header>
        <h2>Producto Premium</h2>
    </template>

    <p>Contenido principal del card</p>

    <template #footer>
        <AppButton @click="comprar">Comprar</AppButton>
    </template>
</AppCard>
```

### 6.3 Scoped Slots

```vue
<!-- Components/ui/AppDataTable.vue -->
<script setup>
const props = defineProps({
    items: { type: Array, required: true },
    columns: { type: Array, required: true },
})
</script>

<template>
    <table class="data-table">
        <thead>
            <tr>
                <th v-for="col in columns" :key="col">{{ col }}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in items" :key="item.id">
                <!-- Scoped slot: expone item e index al padre -->
                <slot name="row" :item="item" :index="index" />
            </tr>
        </tbody>
    </table>
</template>

<!-- Uso con desestructuración -->
<AppDataTable :items="productos" :columns="['Nombre', 'Precio', 'Stock']">
    <template #row="{ item, index }">
        <td>{{ item.nombre }}</td>
        <td>Bs. {{ item.precio_venta }}</td>
        <td>{{ item.stock }}</td>
    </template>
</AppDataTable>
```

---

## 🔗 7. Navegación y Links con Inertia

### 7.1 Componente `<Link>` (Reemplaza `<a>`)

```vue
<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
</script>

<template>
    <!-- ✅ CORRECTO: Link de Inertia (SPA navigation, sin full reload) -->
    <Link :href="route('productos.index')">Ver Productos</Link>

    <!-- ✅ Con método HTTP (para logout, etc.) -->
    <Link :href="route('logout')" method="post" as="button">
        Cerrar Sesión
    </Link>

    <!-- ✅ Preservar scroll en navegación -->
    <Link :href="route('productos.show', producto.id)" preserve-scroll>
        {{ producto.nombre }}
    </Link>

    <!-- ❌ INCORRECTO: Usar <a> directamente causa full page reload -->
    <a :href="route('productos.index')">Productos</a>
</template>
```

### 7.2 Navegación Programática con `router`

```javascript
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// Navegar
router.visit(route('productos.index'))

// Con datos
router.get(route('productos.index'), { busqueda: 'whisky' })

// Recargar datos de la página actual
router.reload({ only: ['productos'] })

// Eliminar
router.delete(route('productos.destroy', id), {
    preserveScroll: true,
    onSuccess: () => { /* callback */ },
    onError: (errors) => { /* manejar errores */ },
})
```

---

## 🌍 8. Datos Compartidos (Shared Data)

### 8.1 Desde el Backend (HandleInertiaRequests Middleware)

```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'roles' => $request->user()->roles,
            ] : null,
        ],
        'flash' => [
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ],
        'appName' => config('app.name'),
    ];
}
```

### 8.2 Acceder en Vue con `usePage()`

```vue
<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const usuario = computed(() => page.props.auth?.user)
const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)
const appName = computed(() => page.props.appName)
</script>

<template>
    <div v-if="flashSuccess" class="alert alert-success">
        {{ flashSuccess }}
    </div>
    <p>Bienvenido, {{ usuario?.name }}</p>
</template>
```

---

## 🏷️ 9. SEO y Head Management

### 9.1 Componente `<Head>` de Inertia

```vue
<script setup>
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <!-- ✅ Título dinámico por página -->
    <Head title="Productos — Gestión Licorería" />

    <!-- ✅ Meta tags completos -->
    <Head>
        <title>Productos — Gestión Licorería</title>
        <meta
            head-key="description"
            name="description"
            content="Administra tu inventario de productos de licorería"
        />
        <meta head-key="og:title" property="og:title" content="Productos" />
    </Head>
</template>
```

### 9.2 Title Callback Global (en `app.js`)

```javascript
createInertiaApp({
    title: (title) => title
        ? `${title} — Gestión Licorería`
        : 'Gestión Licorería',
    // ...
})
```

---

## 🎨 10. Componentes UI Genéricos (Patrones Clave)

### 10.1 Componente `AppButton.vue`

```vue
<!-- Components/ui/AppButton.vue -->
<script setup>
const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (v) => ['primary', 'secondary', 'danger', 'ghost'].includes(v),
    },
    size: {
        type: String,
        default: 'md',
        validator: (v) => ['sm', 'md', 'lg'].includes(v),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['click'])
</script>

<template>
    <button
        :class="[
            'btn',
            `btn-${variant}`,
            `btn-${size}`,
            { 'btn-loading': loading },
        ]"
        :disabled="disabled || loading"
        @click="$emit('click', $event)"
    >
        <span v-if="loading" class="spinner" />
        <slot />
    </button>
</template>

<style scoped>
.btn {
    @apply inline-flex items-center justify-center gap-2
           font-medium rounded-lg transition-all duration-200
           focus:outline-none focus:ring-2 focus:ring-offset-2;
}
.btn-primary { @apply bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500; }
.btn-secondary { @apply bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-400; }
.btn-danger { @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500; }
.btn-ghost { @apply bg-transparent text-gray-600 hover:bg-gray-100; }
.btn-sm { @apply px-3 py-1.5 text-sm; }
.btn-md { @apply px-4 py-2 text-sm; }
.btn-lg { @apply px-6 py-3 text-base; }
.btn-loading { @apply opacity-75 cursor-not-allowed; }
</style>
```

### 10.2 v-model en Componentes Personalizados (Vue 3.4+)

```vue
<!-- Components/ui/AppTextInput.vue -->
<script setup>
// ✅ defineModel() — La forma más limpia en Vue 3.4+
const model = defineModel({ type: String, default: '' })

defineProps({
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
})
</script>

<template>
    <input
        v-model="model"
        :type="type"
        :placeholder="placeholder"
        class="input-text"
    />
</template>

<!-- Uso: funciona directamente con v-model -->
<!-- <AppTextInput v-model="form.nombre" placeholder="Nombre..." /> -->
```

```vue
<!-- Para Vue < 3.4: Forma clásica con modelValue -->
<script setup>
const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
})

const emit = defineEmits(['update:modelValue'])

function actualizar(event) {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <input :value="modelValue" @input="actualizar" class="input-text" />
</template>
```

---

## ⚡ 11. Rendimiento Frontend

### 11.1 Lazy Loading de Componentes

```vue
<script setup>
import { defineAsyncComponent } from 'vue'

// ✅ Lazy load para componentes pesados que no se ven inmediatamente
const VentasChart = defineAsyncComponent(() =>
    import('@/Components/charts/VentasChart.vue')
)

// ✅ Con loader y error handling
const AppDataTable = defineAsyncComponent({
    loader: () => import('@/Components/ui/AppDataTable.vue'),
    loadingComponent: { template: '<div class="animate-pulse">Cargando tabla...</div>' },
    delay: 200,
    timeout: 5000,
})
</script>
```

### 11.2 Optimización de Listas

```vue
<template>
    <!-- ✅ SIEMPRE usar :key con un identificador único -->
    <div v-for="producto in productos" :key="producto.id">
        {{ producto.nombre }}
    </div>

    <!-- ❌ INCORRECTO: key con index (causa bugs en reorder) -->
    <div v-for="(producto, index) in productos" :key="index">
        {{ producto.nombre }}
    </div>
</template>
```

### 11.3 `v-show` vs `v-if`

```vue
<template>
    <!-- ✅ v-show: para toggle frecuente (oculta con CSS, no destruye) -->
    <div v-show="panelVisible">Panel lateral</div>

    <!-- ✅ v-if: para condiciones que cambian poco (destruye/crea el DOM) -->
    <AdminPanel v-if="esAdmin" />
</template>
```

### 11.4 Partial Reloads de Inertia

```javascript
// ✅ Recargar solo ciertos props (no toda la página)
router.reload({ only: ['productos'] })

// ✅ En Link
<Link :href="url" :only="['productos', 'filtros']">Actualizar</Link>
```

### 11.5 Reactividad Eficiente

```javascript
import { shallowRef, shallowReactive } from 'vue'

// ✅ shallowRef para objetos grandes que se reemplazan completos
const reporteGrande = shallowRef(null)

// ✅ shallowReactive para objetos con propiedades que no cambian internamente
const configuracion = shallowReactive({
    tema: 'oscuro',
    idioma: 'es',
})

// ✅ v-memo para listas con renders costosos (usar con precaución)
// <div v-for="item in list" :key="item.id" v-memo="[item.id, item.nombre]">
```

---

## 🛠️ 12. Utils (Funciones Utilitarias)

### 12.1 `formatters.js`

```javascript
// resources/js/Utils/formatters.js

/**
 * Formatear como moneda boliviana.
 */
export function formatearMoneda(valor, decimales = 2) {
    return `Bs. ${Number(valor).toFixed(decimales)}`
}

/**
 * Formatear fecha relativa.
 */
export function formatearFecha(fecha) {
    const d = new Date(fecha)
    const hoy = new Date()
    const ayer = new Date(hoy)
    ayer.setDate(ayer.getDate() - 1)

    if (d.toDateString() === hoy.toDateString()) return 'Hoy'
    if (d.toDateString() === ayer.toDateString()) return 'Ayer'

    return d.toLocaleDateString('es-BO', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

/**
 * Truncar texto.
 */
export function truncar(texto, maxLength = 50) {
    if (!texto || texto.length <= maxLength) return texto
    return texto.substring(0, maxLength) + '...'
}
```

### 12.2 `constants.js`

```javascript
// resources/js/Utils/constants.js

export const ESTADOS_PRODUCTO = {
    ACTIVO: 'activo',
    INACTIVO: 'inactivo',
    AGOTADO: 'agotado',
    DESCONTINUADO: 'descontinuado',
}

export const METODOS_PAGO = {
    EFECTIVO: 'efectivo',
    QR: 'qr',
    TARJETA: 'tarjeta',
}

export const PAGINACION_POR_DEFECTO = 15
```

---

## 🔤 13. Naming Conventions — Resumen Completo Frontend

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| **Componente SFC** | `PascalCase.vue` | `ProductoCard.vue` |
| **Componente en template** | `PascalCase` | `<ProductoCard />` |
| **Prop (JS)** | `camelCase` | `precioVenta` |
| **Prop (template)** | `kebab-case` | `:precio-venta="..."` |
| **Evento emitido** | `camelCase` | `@productoSeleccionado` |
| **Variable reactiva** | `camelCase` | `const totalProductos = ref(0)` |
| **Computed** | `camelCase` (sustantivo/adjetivo) | `productosFiltrados`, `esAdmin` |
| **Método/Función** | `camelCase` (verbo) | `eliminarProducto()`, `buscar()` |
| **Composable** | `use` + `PascalCase` | `useProductos`, `usePermisos` |
| **Store Pinia** | `use` + `PascalCase` + `Store` | `useCartStore` |
| **Constante** | `UPPER_SNAKE_CASE` | `ESTADOS_PRODUCTO`, `API_URL` |
| **Archivo util** | `camelCase.js` | `formatters.js`, `validators.js` |
| **Carpeta módulo** | `kebab-case` o `camelCase` | `productos/`, `ventas/` |
| **Clase CSS** | `kebab-case` | `.producto-card`, `.btn-primary` |
| **Variable CSS** | `--kebab-case` | `--color-primary` |
| **Alias de importación** | `@` = `resources/js/` | `import X from '@/Components/...'` |

---

## 📐 14. Configuración de Vite (Aliases y Optimizaciones)

```javascript
// vite.config.js — Configuración recomendada
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            // ✅ Alias @ para imports más limpios
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
})
```

```javascript
// ✅ Con el alias, los imports se simplifican:
import AppButton from '@/Components/ui/AppButton.vue'
// En vez de:
import AppButton from '../../Components/ui/AppButton.vue'
```

---

## ✅ 15. Checklist de Revisión — Frontend

### Componentes
- [ ] Usa `<script setup>` (no Options API)
- [ ] Props definidas con tipos y defaults
- [ ] Emits declarados con `defineEmits`
- [ ] Orden interno correcto (imports → props → emits → composables → state → computed → watch → methods → lifecycle)
- [ ] `style scoped` para evitar colisiones CSS
- [ ] `:key` única en todos los `v-for`

### Formularios
- [ ] Usa `useForm` de Inertia (no ref + axios)
- [ ] Errores mostrados con `form.errors.campo`
- [ ] Botón deshabilitado con `form.processing`
- [ ] `form.reset()` en `onSuccess` si aplica
- [ ] `@submit.prevent` en el `<form>`

### Navegación
- [ ] Usa `<Link>` de Inertia (no `<a>`)
- [ ] Rutas con `route()` de Ziggy (no strings hardcoded)
- [ ] `preserveScroll` donde sea necesario
- [ ] `router.reload({ only: [...] })` para partial reloads

### SEO
- [ ] `<Head title="...">` en cada página
- [ ] Título con formato consistente (vía callback en `app.js`)

### Rendimiento
- [ ] Lazy loading para componentes pesados
- [ ] `v-show` para toggles frecuentes
- [ ] `shallowRef` para datos grandes
- [ ] Sin lazy loading accidental de relaciones (le corresponde vigilar al backend)

### Estructura
- [ ] Pages alineadas con rutas del backend
- [ ] Componentes organizados por módulo
- [ ] Composables con prefijo `use`
- [ ] Utils sin estado reactivo
- [ ] Sin lógica de negocio en componentes de UI

---

## 📚 Referencias Oficiales

- [Vue 3 — Documentación oficial](https://vuejs.org/guide/introduction.html)
- [Vue 3 — Composition API](https://vuejs.org/api/composition-api-setup.html)
- [Inertia.js — Documentación v2](https://inertiajs.com/)
- [Inertia.js — Forms](https://inertiajs.com/forms)
- [Inertia.js — Shared Data](https://inertiajs.com/shared-data)
- [Inertia.js — Persistent Layouts](https://inertiajs.com/pages#persistent-layouts)
- [Ziggy — Documentación](https://github.com/tightenco/ziggy)
- [Tailwind CSS v4](https://tailwindcss.com/docs)
- [Vite — Documentación](https://vitejs.dev/)
- [Pinia — State Management](https://pinia.vuejs.org/)
