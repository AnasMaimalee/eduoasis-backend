<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\JambResultRequest;

class ServiceWorkCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public JambResultRequest $job;

    /**
     * Create a new message instance.
     */
    public function __construct(JambResultRequest $job)
    {
        $this->job = $job;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $mail = $this->subject('Your JAMB Result Service is Complete!')
            ->markdown('emails.service.completed');

        // Attach the file uploaded by admin
        if ($this->job->result_file) {
            $mail->attach(
                storage_path('app/public/' . $this->job->result_file),
                [
                    'as'   => 'JAMB_Result.' . pathinfo($this->job->result_file, PATHINFO_EXTENSION),
                    'mime' => mime_content_type(storage_path('app/public/' . $this->job->result_file)),
                ]
            );
        }

        return $mail;
    }
}
