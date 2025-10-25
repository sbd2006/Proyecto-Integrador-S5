<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\CarritoController;



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
    Route::resource('categoria', CategoriaController::class);
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

require __DIR__ . '/auth.php';

