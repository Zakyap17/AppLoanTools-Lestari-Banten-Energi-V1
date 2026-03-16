<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $loans;
    public $daysLeft;

    /**
     * Create a new message instance.
     */
    public function __construct($loans, $daysLeft)
    {
        $this->loans = $loans;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $firstLoan = $this->loans->first();
        $status = $this->daysLeft < 0 ? 'TERLAMBAT' : 'Reminder Pengembalian';
        $timeStr = $this->daysLeft < 0
            ? abs($this->daysLeft) . ' Hari'
            : ($this->daysLeft == 0 ? 'HARI INI' : $this->daysLeft . ' Hari Lagi');

        return new Envelope(
            subject: "[$status] $timeStr - Peminjaman #" . $firstLoan->loan_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan_reminder',
            with: [
                'loans' => $this->loans,
                'daysLeft' => $this->daysLeft,
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
