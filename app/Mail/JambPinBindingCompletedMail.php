<?php

namespace App\Mail;

use App\Models\JambPinBindingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\JambAdmissionLetterRequest;

class JambPinBindingCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JambPinBindingRequest $job
    ) {}

    public function build()
    {
        return $this
            ->subject('Your JAMB Pin Letter is Ready')
            ->view('emails.services.jamb-pin-binding-completed');
    }
}
