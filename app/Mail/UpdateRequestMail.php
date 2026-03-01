<?php

namespace App\Mail;

use App\Http\Utils\UtilFunction;
use App\Models\Postulant;
use App\Models\UpdateRequest;
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
    public string $status;
    public string $uniqueCode;
    public string $expiresAt;
    public string $note;
    public string $today;

    public function __construct(
        Postulant $postulant,
        string $status,
        string $uniqueCode = '',
        string $expiresAt  = '',
        string $note       = ''
    ) {
        $this->applicantName = trim($postulant->nombres . ' ' . $postulant->ap_paterno . ' ' . $postulant->ap_materno);
        $this->sexo          = $postulant->sexo_id;
        $this->status        = $status;
        $this->uniqueCode    = $uniqueCode;
        $this->expiresAt     = $expiresAt;
        $this->note          = $note;
        $this->today         = UtilFunction::getDateToday();
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === UpdateRequest::STATUS_APPROVED
            ? 'UNPRG ADMISIÓN - Solicitud Aprobada - Código de Actualización de Datos'
            : 'UNPRG ADMISIÓN - Solicitud de Actualización Rechazada';

        return new Envelope(subject: $subject);
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