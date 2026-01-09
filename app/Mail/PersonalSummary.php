<?php

namespace App\Mail;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PersonalSummary extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Person $person,
        public Collection $activities
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Activity Summary - ' . now()->format('d M Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $asLead = $this->activities->filter(fn($a) => $a->lead_id === $this->person->id);
        $asParty = $this->activities->filter(fn($a) => $a->lead_id !== $this->person->id);

        return new Content(
            view: 'emails.personal-summary',
            with: [
                'person' => $this->person,
                'asLead' => $asLead,
                'asParty' => $asParty,
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
