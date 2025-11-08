
# 🍰 Postres María José

Sistema en **Laravel** para gestionar **productos, categorías, pedidos/ventas** y **reportes** con exporte PDF.

## 🧭 Índice por módulos

* [Módulo 1 · Catálogo Cliente](#módulo-1--catálogo-cliente)
* [Módulo 2 · Productos (Admin)](#módulo-2--productos-admin)
* [Módulo 3 · Categorías (Admin)](#módulo-3--categorías-admin)
* [Módulo 4 · Pedidos (Admin)](#módulo-4--pedidos-admin)
* [Módulo 5 · Reporte de Ventas (Admin)](#módulo-5--reporte-de-ventas-admin)
* [Módulo 6 · Métodos de Pago](#módulo-6--métodos-de-pago)
* [Módulo 7 · Stock (Servicio)](#módulo-7--stock-servicio)
* [Módulo 8 · Unificación de Tablas](#módulo-8--unificación-de-tablas)
* [Módulo 9 · Autenticación y Roles](#módulo-9--autenticación-y-roles)
* [Módulo 10 · PDF](#módulo-10--pdf)
* [Módulo 11 · Optimización](#módulo-11--optimización)
* [Módulo 12 · Troubleshooting](#módulo-12--troubleshooting)
* [Instalación (paso a paso)](#instalación-paso-a-paso)

---

## Módulo 1 · Catálogo Cliente

**Propósito:** Mostrar productos al cliente con filtros por **nombre**, **categoría** y **precio**.

**Ruta**

```
GET /productos   (name: productos.index)
```

**Controlador sugerido**: `ProductoClienteController@index`
**Vista**: `resources/views/cliente/productos/index.blade.php`
**Datos**: `Producto` con `categoria` (eager load). Paginación 12.
**Validación**: `nombre`(string), `categoria`(int), `min/max`(numeric ≥0).

---

## Módulo 2 · Productos (Admin)

**Propósito:** CRUD + filtros y PDF.

**Rutas**

```
GET /producto           (producto.index)
GET /producto/create    (producto.create)
POST /producto          (producto.store)
GET /producto/{id}/edit (producto.edit)
PUT /producto/{id}      (producto.update)
DELETE /producto/{id}   (producto.destroy)
```

**Vistas**: `resources/views/admin/producto/*`

**Notas**

* Filtros: nombre, categoría, precio min/max, stock (con/sin).
* Botón **“Crear categoría”** disponible desde Productos.

---

## Módulo 3 · Categorías (Admin)

**Propósito:** CRUD categorías.

**Rutas**

```
GET /categoria          (categoria.index)
GET /categoria/create   (categoria.create)
POST /categoria         (categoria.store)
GET /categoria/{id}/edit(categoria.edit)
PUT /categoria/{id}     (categoria.update)
DELETE /categoria/{id}  (categoria.destroy)
```

**Vista index**: título centrado, botones uniformes, link **↩️ Volver a mis Productos**.

---

## Módulo 4 · Pedidos (Admin)

**Propósito:** Gestión y monitoreo de pedidos.

**Ruta**

```
GET /admin/pedidos   (admin.pedidos)
```

**Filtros**: fecha (desde/hasta o por día), `status`, `payment_method_id`.
**KPIs**: finalizadas / canceladas (controlador debe pasar `$finished`, `$canceled`).
**UI**: tabla paginada; chips de filtros activos.

---

## Módulo 5 · Reporte de Ventas (Admin)

**Propósito:** KPIs y tablas de ventas + PDF.

**Ruta**

```
GET /reportes/ventas        (reportes.ventas.resumen)
GET /reportes/ventas/pdf    (reportes.ventas.resumen.pdf)
```

**Vista**: `resources/views/admin/reportes/ventas/resumen.blade.php`
**Incluye**: KPIs (Ingresos, Órdenes, Ticket), **por método de pago**, **ventas por día**, **top productos**.
**UX**: chips rápidos (Hoy/Mes/Año), botón **PDF** con mismos filtros.

---

## Módulo 6 · Métodos de Pago

**Propósito:** CRUD básico de métodos de pago.

**Rutas** (recomendadas)

```php
Route::resource('payment-methods', PaymentMethodController::class)
     ->names('payment_methods');
```

**Uso en filtros/reportes** vía `payment_method_id`.

---

## Módulo 7 · Stock (Servicio)

**Propósito:** Descontar stock al crear venta y restaurar al cancelar, de forma **atómica**.

**Servicio**: `app/Services/ProcesarVenta.php`

* `ejecutar(items, meta)` → crea `Order` + `OrderItems` y **descuenta** (`lockForUpdate()`).
* `restaurarStock(Order)` → **repone** en cancelación.

**Integración**: `PaymentController@pagar`, `VentaController@store`.

---

## Módulo 8 · Unificación de Tablas

**Objetivo:** pasar de 4 tablas (`orders/order_items` + `pedidos/pedido_detalles`) a **2** (`orders/order_items`).

**Pasos**

1. Extiende `orders`: `shipping_address`, `payment_method_text`, `legacy_pedido_id`.
2. **Volcado**: `pedidos → orders` y `pedido_detalles → order_items` con `INSERT…SELECT`.
3. **Mapeo**: tabla `map_pedido_order(pedido_id, order_id)` para enlazar detalles.
4. **Redirige FKs** que apunten a `pedidos` ⇒ `orders` (añade `order_id`, backfill, crea FK nueva, elimina FK/columna vieja).
5. **Freeze**: renombra legacy a `*_legacy` (opcional: crear **VIEW** de solo lectura).

> Si prefieres conservar `pedidos/pedido_detalles`, aplica el proceso inverso.

---

## Módulo 9 · Autenticación y Roles

* Usa **Spatie** (`hasRole('admin')`) o campo `rol` como fallback.
* Sidebar muestra ítems admin (Pedidos, Reporte) sólo si `admin`.
* Protege rutas admin con `->middleware(['auth','role:admin'])`.

---

## Módulo 10 · PDF

* Paquete: `barryvdh/laravel-dompdf`.
* Mantén plantillas simples; si usas tildes/ñ, utiliza fuente compatible (p.ej., DejaVu Sans).

---

## Módulo 11 · Optimización

* Índices sugeridos:

  * `orders(status, created_at, paid_at, payment_method_id)`
  * `order_items(order_id, producto_id)`
  * `productos(categoria_id, nombre, precio_venta)`
* Eager loading en listados (`with(['user','paymentMethod'])`).

---

## Módulo 12 · Troubleshooting

* **APP_KEY**: `php artisan key:generate && php artisan optimize:clear`.
* **FK errno 150**: verifica **orden de migraciones** (timestamp) y tipos (`id` BIGINT UNSIGNED ↔ `foreignId`).
* **Roles (tabla roles no existe)**: publica migraciones de Spatie o crea tablas “caseras” para `roles`.
* **Testing con SQLite**: usa `.env.testing` con `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:` o archivo `database/testing.sqlite`.

---

## Instalación (paso a paso)

### 0) Requisitos

* PHP 8.2+ · Composer 2.x · MySQL 8.x (o MariaDB)
* Node 18+ (si compilas assets)
* Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `intl`

### 1) Clonar e instalar dependencias

```bash
git clone <tu-repo.git> postres-maria-jose
cd postres-maria-jose
composer install
npm install     # opcional (Vite)
```

### 2) Configurar entorno

```bash
cp .env.example .env    # Windows: copy .env.example .env
```

Edita `.env`:

```env
APP_NAME="Postres Maria Jose"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud
DB_USERNAME=root
DB_PASSWORD=secret
```

### 3) Generar APP_KEY y limpiar cachés

```bash
php artisan key:generate
php artisan config:clear
php artisan optimize:clear
```

### 4) Migrar y sembrar

> Si usas **Spatie Permission**:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"
```

Ahora:

```bash
php artisan migrate --seed
# ó para resetear:
php artisan migrate:fresh --seed
```

### 5) (Opcional) Storage y assets

```bash
php artisan storage:link
npm run dev   # o npm run build
```

### 6) Levantar servidor

```bash
php artisan serve
# http://127.0.0.1:8000
```

### 7) (Opcional) Testing

```bash
cp .env .env.testing
php artisan key:generate --env=testing
php artisan migrate:fresh --seed --env=testing
php artisan test
```

SQLite archivo para tests (opcional):

```env
# en .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=./database/testing.sqlite
```

Crear archivo:

```bash
# Linux/Mac
touch database/testing.sqlite
# Windows PowerShell
ni database\testing.sqlite -ItemType File
```

---

Hecho con 💖 para **Postres María José**.
