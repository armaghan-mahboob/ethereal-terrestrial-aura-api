<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order Placed #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-order-notification',
            with: ['order' => $this->order],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
