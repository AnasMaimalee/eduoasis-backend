<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ExamSubmittedNotification extends Notification
{
    public string $examId;

    public function __construct(string $examId)
    {
        $this->examId = $examId;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CBT Exam Submitted')
            ->view('emails.exams.exam-submitted', [
                'user' => $notifiable,
                'examId' => $this->examId,
                'frontendUrl' => config('app.frontend_url'),
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'exam_id' => $this->examId,
            'message' => 'Your CBT exam has been submitted successfully!',
        ];
    }
}
