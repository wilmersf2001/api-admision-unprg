<?php

namespace App\Jobs;

use App\Mail\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    protected $email;
    protected $destinatario;
    protected $sexo;
    protected $isValid;
    protected $processNumber;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $destinatario, $sexo, $isValid, $processNumber)
    {
        $this->email = $email;
        $this->destinatario = $destinatario;
        $this->sexo = $sexo;
        $this->isValid = $isValid;
        $this->processNumber = $processNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new SendMail($this->destinatario, $this->sexo, $this->isValid, $this->processNumber));
    }
}
