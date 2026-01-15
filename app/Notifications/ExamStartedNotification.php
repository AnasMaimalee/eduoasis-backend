<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ExamStartedNotification extends Notification
{
    public string $examId;

    public function __construct(string $examId)
    {
        $this->examId = $examId;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // sends both email and database notification
    }

    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');
        return (new MailMessage)
            ->subject('CBT Exam Started')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your CBT exam has started successfully.')
            ->action(
                'Continue Exam',
                "{$frontendUrl}/exams/{$this->examId}"
            )
            ->line('Best of luck!');
    }

    public function toArray($notifiable)
    {
        return [
            'exam_id' => $this->examId,
            'message' => 'Your CBT exam has started successfully!',
        ];
    }
}
