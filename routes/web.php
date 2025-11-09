<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VentaController;
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
    return view('welcome');
})->middleware(['auth', 'role:user'])->name('welcome');

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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('venta', VentaController::class)->only(['index', 'show']);
    Route::put('ventas/{venta}/estado', [VentaController::class, 'cambiarEstado'])->name('venta.cambiarEstado');
});

Route::get('/venta', [VentaController::class, 'index'])->name('venta.index');

Route::get('/pdfProductos', [PdfController::class, 'pdfProductos'])->name('producto.pdf');


Route::resource('categoria', CategoriaController::class)
    ->parameters(['categoria' => 'categoria']) // evita 'categorium'
    ->except(['show']);

Route::get('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [PaymentController::class, 'pagar'])->name('checkout.pagar');

Route::prefix('reportes')->name('reportes.')->group(function () {
    // Formulario
    Route::get('ventas', [ReportController::class, 'ventasForm'])->name('ventas');
    // Generar PDF
    Route::get('ventas/pdf', [ReportController::class, 'ventasPdf'])->name('ventas.pdf');

    Route::get('ventas/resumen', [ReportController::class, 'resumen'])->name('ventas.resumen');

    Route::get('ventas/resumen/pdf', [ReportController::class, 'resumenPdf'])->name('ventas.resumen.pdf');
});

// Venta rápida (sin detalle)
Route::prefix('ventas/rapida')->name('ventas.rapida.')->group(function () {
    Route::get('/', [VentaController::class, 'createQuick'])->name('create');
    Route::post('/', [VentaController::class, 'storeQuick'])->name('store');
});

// Finalización de venta DESDE carrito externo (tu compañero)
Route::post('/ventas/finalizar', [VentaController::class, 'finalizarDesdeCarrito'])
    ->name('ventas.finalizar');

// Ver factura PDF
Route::get('/ventas/{order}/factura', [VentaController::class, 'factura'])
    ->name('ventas.factura');

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

    // 🔄 Cambiar el estado del pedido (lo usa el admin)
    Route::patch('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado'])
        ->name('pedidos.actualizar');
});

// 🔒 Rutas del cliente autenticado (rol: user)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/mis-pedidos', [PedidoController::class, 'vistaPedidosCliente'])
        ->name('cliente.pedidos');

    Route::get('/mis-pedidos/json', [PedidoController::class, 'pedidosPorCliente'])
        ->name('cliente.pedidos.json');

    Route::get('/cliente/pedidos/{id}/pago', [PedidoController::class, 'mostrarPago'])
        ->name('cliente.pedidos.checkout');

    Route::post('/cliente/pedidos/{id}/cancelar', [PedidoController::class, 'cancelar'])
        ->name('cliente.pedidos.cancelar');
});

/*
// 🔹 Ruta temporal para evitar error de "categoria.index"
Route::get('/categoria', function () {
    return 'Página de categorías (temporal)';
})->name('categoria.index');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('producto', ProductoController::class);
});

*/
Route::get('/mis-pedidos/cantidad', [PedidoController::class, 'contarPedidosCliente'])
    ->name('cliente.pedidos.cantidad');

require __DIR__ . '/auth.php';
