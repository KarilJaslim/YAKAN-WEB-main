<?php

namespace App\Mail\CustomOrder;

use App\Models\CustomOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PriceQuotedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $customOrder;

    /**
     * Create a new message instance.
     */
    public function __construct(CustomOrder $customOrder)
    {
        $this->customOrder = $customOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Custom Order Has Been Priced - Yakan E-commerce',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-orders.price-quoted',
            with: [
                'order' => $this->customOrder,
                'user' => $this->customOrder->user,
                'finalPrice' => number_format($this->customOrder->final_price, 2),
                'adminNotes' => $this->customOrder->admin_notes,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
