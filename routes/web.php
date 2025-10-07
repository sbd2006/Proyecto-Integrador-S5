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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


    //ruta categorias
    Route::resource('/categoria', CategoriaController::class);
    //ruta productos
    Route::resource('/producto', ProductoController::class);
    //pdf
    Route::get('/pdfProductos', [PdfController::class,'pdfProductos'])->name('producto.pdf');
 
    
Route::resource('categoria', CategoriaController::class)
    ->parameters(['categoria' => 'categoria'])
    ->except(['show']);                      


require __DIR__.'/auth.php';

