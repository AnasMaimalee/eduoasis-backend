<?php

namespace App\Mail;

use App\Models\JambAdmissionResultNotificationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JambAdmissionResultNotificationCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JambAdmissionResultNotificationRequest $job
    ) {}

    public function build()
    {
        return $this
            ->subject('Your JAMB Admission Result Notification is Ready')
            ->view('emails.services.jamb-admission-result-notification-completed');
    }
}
