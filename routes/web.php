<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('layout.dashboard');
})->name('dashboard');

// 🔹 Ruta temporal para evitar error de "categoria.index"
Route::get('/categoria', function () {
    return 'Página de categorías (temporal)';
})->name('categoria.index');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');


Route::get('/usuario/dashboard', function () {
    return view('usuario.dashboard');
})->name('usuario.dashboard')->middleware('auth');

//ruta productos
Route::resource('/producto', ProductoController::class);
//pdf
Route::get('/pdfProductos', [PdfController::class, 'pdfProductos'])->name('producto.pdf');


Route::resource('categoria', CategoriaController::class)
    ->parameters(['categoria' => 'categoria']) // evita 'categorium'
    ->except(['show']);



require __DIR__ . '/auth.php';
