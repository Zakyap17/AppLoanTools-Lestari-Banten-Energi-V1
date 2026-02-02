<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueLoanNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $borrowerName;
    public $loans;

    /**
     * Create a new message instance.
     */
    public function __construct($borrowerName, $loans)
    {
        $this->borrowerName = $borrowerName;
        $this->loans = $loans;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'URGENT: Overdue Equipment Return - ' . $this->borrowerName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue_loan',
            with: [
                'borrowerName' => $this->borrowerName,
                'loans' => $this->loans,
            ],
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
