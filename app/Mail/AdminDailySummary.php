<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AdminDailySummary extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Collection $activities,
        public Collection $users
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Master Activity Summary - ' . now()->format('d M Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $overdue = $this->activities->filter(fn($a) => $a->is_overdue);
        $dueSoon = $this->activities->filter(fn($a) =>
            !$a->is_overdue &&
            $a->days_until_due !== null &&
            $a->days_until_due >= 0 &&
            $a->days_until_due <= 7
        );
        $inProgress = $this->activities->where('status', 'in_progress');

        return new Content(
            view: 'emails.admin-daily-summary',
            with: [
                'activities' => $this->activities,
                'users' => $this->users,
                'overdue' => $overdue,
                'dueSoon' => $dueSoon,
                'inProgress' => $inProgress,
                'totalCount' => $this->activities->count(),
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
