<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VentaController;


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


require __DIR__.'/auth.php';