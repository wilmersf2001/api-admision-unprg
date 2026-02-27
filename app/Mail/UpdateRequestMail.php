<?php

namespace App\Mail;

use App\Http\Utils\UtilFunction;
use App\Models\Postulant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpdateRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $applicantName;
    public int $sexo;
    public string $uniqueCode;
    public string $expiresAt;
    public string $today;

    public function __construct(Postulant $postulant, string $uniqueCode, string $expiresAt)
    {
        $this->applicantName = trim($postulant->nombres . ' ' . $postulant->ap_paterno . ' ' . $postulant->ap_materno);
        $this->sexo          = $postulant->sexo_id;
        $this->uniqueCode    = $uniqueCode;
        $this->expiresAt     = $expiresAt;
        $this->today         = UtilFunction::getDateToday();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'UNPRG ADMISIÓN - Código de Actualización de Datos',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'update-request-email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}