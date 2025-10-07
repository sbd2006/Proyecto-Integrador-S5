<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::get('/', function () {
    return view('welcome');
})->name('inicio');

// 🔹 Ruta temporal para evitar error de "categoria.index"
Route::get('/categoria', function () {
    return 'Página de categorías (temporal)';
})->name('categoria.index');


Route::get('/dashboard', function () {
    return view('layout.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


    //ruta productos
    Route::resource('/producto', ProductoController::class);
    //pdf
    Route::get('/pdfProductos', [PdfController::class,'pdfProductos'])->name('producto.pdf');
 
    
Route::resource('categoria', CategoriaController::class)
    ->parameters(['categoria' => 'categoria']) // evita 'categorium'
    ->except(['show']);
                    


require __DIR__.'/auth.php';