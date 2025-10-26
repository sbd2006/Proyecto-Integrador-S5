<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nombre'=>'Efectivo', 'slug'=>'efectivo', 'descripcion'=>'Pago en tienda', 'instrucciones'=>'Paga al recibir en el punto de entrega.'],
            ['nombre'=>'Nequi', 'slug'=>'nequi', 'descripcion'=>'Transferencia Nequi', 'instrucciones'=>'Envía a 3001234567 y adjunta comprobante.'],
            ['nombre'=>'Bancolombia', 'slug'=>'bancolombia', 'descripcion'=>'Transferencia Bancolombia', 'instrucciones'=>'Cuenta de ahorros 123-456789-01, titular Postres María José.'],
            ['nombre'=>'Datáfono', 'slug'=>'datafono', 'descripcion'=>'Tarjeta en punto de venta', 'instrucciones'=>'Se realizará el cobro al recoger.'],
        ];

        foreach ($rows as $r) {
            PaymentMethod::firstOrCreate(['slug' => $r['slug']], $r);
        }
    }
}
