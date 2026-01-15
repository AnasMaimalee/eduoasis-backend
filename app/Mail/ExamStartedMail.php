<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExamStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $examId;
    public $user;

    public function __construct($user, string $examId)
    {
        $this->user = $user;
        $this->examId = $examId;
    }

    public function build()
    {
        return $this->subject('🟢 CBT Exam Started')
            ->view('emails.cbt-exam-started')
            ->with([
                'user' => $this->user,
                'examId' => $this->examId,
            ]);
    }
}
