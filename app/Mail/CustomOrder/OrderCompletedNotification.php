<?php

namespace App\Mail\CustomOrder;

use App\Models\CustomOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCompletedNotification extends Mailable
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
            subject: 'Your Custom Order Has Been Completed - Yakan E-commerce',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-orders.order-completed',
            with: [
                'order' => $this->customOrder,
                'user' => $this->customOrder->user,
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
