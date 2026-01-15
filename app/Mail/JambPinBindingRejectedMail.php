<?php

namespace App\Mail;

use App\Models\JambPinBindingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JambPinBindingRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JambPinBindingRequest $job
    ) {}

    public function build()
    {
        return $this
            ->subject('JAMB Pin Binding Request Rejected')
            ->view('emails.services.jamb-pin-binding-rejected');
    }
}
