<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $pdfBinary)
    {
    }

    public function build()
    {
        return $this->subject('Factura '.$this->order->referencia)
            ->view('emails.invoice')
            ->with(['order' => $this->order])
            ->attachData(
                $this->pdfBinary,
                'Factura-'.$this->order->referencia.'.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
