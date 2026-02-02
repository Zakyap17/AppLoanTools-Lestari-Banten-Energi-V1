<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $loanDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($loanDetails)
    {
        $this->loanDetails = $loanDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Peminjaman Barang - ' . ($this->loanDetails['loan_code'] ?? ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan_created',
            with: [
                'loanDetails' => $this->loanDetails,
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
