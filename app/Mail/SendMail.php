<?php

namespace App\Mail;

use App\Http\Utils\UtilFunction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $today;
    public $applicantName;
    public $sexo;
    public $isValid;
    public $processNumber;

    /**
     * Create a new message instance.
     */
    public function __construct($applicantName, $sexo, $isValid, $processNumber)
    {
        $this->applicantName = $applicantName;
        $this->sexo = $sexo;
        $this->today = UtilFunction::getDateToday();
        $this->isValid = $isValid;
        $this->processNumber = $processNumber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'UNPRG ADMISIÓN',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email-content',
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
