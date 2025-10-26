<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PedidoController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/inicio', function () {
    return view('welcome'); // o el controlador que desees
})->name('inicio');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// 🔹 Ruta temporal para evitar error de "categoria.index"
Route::get('/categoria', function () {
    return 'Página de categorías (temporal)';
})->name('categoria.index');

// Dashboard para administrador
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'role:admin'])->name('admin.dashboard');


Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->middleware(['auth', 'role:user'])->name('user.dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
});

//ruta productos
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('producto', ProductoController::class);
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('productos/{producto}', [ProductoController::class, 'show'])->name('productos.detalle');
});
//pdf
Route::get('/pdfProductos', [PdfController::class, 'pdfProductos'])->name('producto.pdf');


Route::resource('categoria', CategoriaController::class)
    ->parameters(['categoria' => 'categoria']) // evita 'categorium'
    ->except(['show']);

Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::get('/carrito', function () {
    $carrito = session('carrito', []);
    return view('carrito.index', compact('carrito'));
})->name('carrito.index');
Route::delete('/carrito/eliminar/{id}', [App\Http\Controllers\CarritoController::class, 'eliminar'])->name('carrito.eliminar');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::post('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
});

Route::middleware('auth')->group(function () {

    // 🧑‍💼 Panel de pedidos (vista general para el admin)
    Route::get('/admin/pedidos', [PedidoController::class, 'panelAdmin'])
        ->name('admin.pedidos');

    // 📦 Listado de pedidos en formato JSON (para actualizar vista del admin)
    Route::get('/pedidos/json', [PedidoController::class, 'indexJson'])
        ->name('pedidos.json');

    // 🛒 El cliente crea un pedido (desde el carrito)
    Route::post('/pedidos', [PedidoController::class, 'store'])
        ->name('pedidos.store');

    // 👤 El cliente ve solo sus pedidos
    Route::get('/mis-pedidos/json', [PedidoController::class, 'pedidosPorCliente'])
        ->name('mis.pedidos.json');

    // 🔄 Cambiar el estado del pedido (lo usa el admin)
    Route::patch('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado'])
        ->name('pedidos.actualizar');
});

// 🔒 Rutas del cliente autenticado (rol: user)
Route::middleware(['auth', 'role:user'])->group(function () {
    // Vista del cliente (Blade)
    Route::get('/mis-pedidos', [PedidoController::class, 'vistaPedidosCliente'])
        ->name('cliente.pedidos');

    // Datos JSON que consume Axios en la vista
    Route::get('/mis-pedidos/json', [PedidoController::class, 'pedidosPorCliente'])
        ->name('cliente.pedidos.json');
});

Route::get('/mis-pedidos/cantidad', [PedidoController::class, 'contarPedidosCliente'])
    ->name('cliente.pedidos.cantidad');

require __DIR__ . '/auth.php';

