<?php

namespace App\Mail;

use App\Models\JambUploadStatusRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JambUploadStatusCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public JambUploadStatusRequest $job;

    /**
     * Create a new message instance.
     */
    public function __construct(JambUploadStatusRequest $job)
    {
        $this->job = $job;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your JAMB Result is Ready')
            ->view('emails.services.jamb-upload-status-completed')
            ->with([
                'job' => $this->job, // <-- make $job available in the blade
            ]);
    }
}
