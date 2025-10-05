<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('/producto', ProductoController::class);
Route::get('/pdfProductos', [PdfController::class,'pdfProductos'])->name('producto.pdf');