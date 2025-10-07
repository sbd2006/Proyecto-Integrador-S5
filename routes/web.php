<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('/producto', ProductoController::class);
Route::get('/pdfProductos', [PdfController::class,'pdfProductos'])->name('producto.pdf');
Route::get('/dashboard', function () {
    return view('layout.dashboard');
})->name('dashboard');

// 🔹 Ruta temporal para evitar error de "categoria.index"
Route::get('/categoria', function () {
    return 'Página de categorías (temporal)';
})->name('categoria.index');


