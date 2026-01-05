<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\JambAdmissionResultNotificationRequest;

class JambAdmissionResultNotificationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JambAdmissionResultNotificationRequest $job
    ) {}

    public function build()
    {
        return $this
            ->subject('JAMB Admission Result Notification Request Rejected')
            ->view('emails.services.jamb-admission-result-notification-rejected');
    }
}
