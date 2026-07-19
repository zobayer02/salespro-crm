<?php

namespace App\Mail;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SaleInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Sale $sale)
    {
        $this->sale->loadMissing(['customer', 'branch', 'items']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SalesPro Invoice #' . $this->sale->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sale-invoice',
        );
    }
}
