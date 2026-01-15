<?php

namespace App\Mail;

use App\Models\PinBindingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PinBindingRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PinBindingRequest $job
    ) {}

    public function build()
    {
        return $this
            ->subject('JAMB Pin Binding Request Rejected')
            ->view('emails.services.jamb-pin-binding-rejected');
    }
}
