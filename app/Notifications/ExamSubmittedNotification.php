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

    public function via($notifiable)
    {
        return ['mail', 'database']; // email + database notification
    }

    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');

        return (new MailMessage)
            ->subject('CBT Exam Submitted')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your CBT exam has been submitted successfully.')
            ->action(
                'View Result',
                "{$frontendUrl}/user/results/{$this->examId}"
            )
            ->line('Thank you for using our CBT platform.');
    }

    public function toArray($notifiable)
    {
        return [
            'exam_id' => $this->examId,
            'message' => 'Your CBT exam has been submitted successfully!',
        ];
    }
}
