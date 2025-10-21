<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return view('welcome');
})->name('inicio');


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

Route::get('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [PaymentController::class, 'pagar'])->name('checkout.pagar');


require __DIR__.'/auth.php';