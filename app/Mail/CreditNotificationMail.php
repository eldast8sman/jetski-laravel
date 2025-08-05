<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreditNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $account_number;
    public $amount;
    public $balance;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $account_number, $amount, $balance)
    {
        $this->name = $name;
        $this->account_number = $account_number;
        $this->amount = $amount;
        $this->balance = $balance;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'support@jetskilagos.com.ng',
            subject: 'Credit Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.credit_notification_mail',
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
