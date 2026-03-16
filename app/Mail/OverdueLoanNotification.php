<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueLoanNotification extends Mailable implements ShouldQueue
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

    public function envelope(): Envelope
    {
        $firstLoan = $this->loans->first();
        return new Envelope(
            subject: 'URGENT: Overdue Equipment Return - ' . ($firstLoan->loan_code ?? 'Manual') . ' (' . $this->borrowerName . ')',
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
