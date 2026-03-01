<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $to,
        protected Mailable $mailable
    ) {
        $this->onQueue('mail');
    }

    public function handle(): void
    {
        Mail::to($this->to)->send($this->mailable);
    }
}