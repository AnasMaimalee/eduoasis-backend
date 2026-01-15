<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;
class WalletDebitedNotification extends Notification
{
    use Queueable;

    public float $amount;
    public string $purpose;
    public string $referenceId;

    public function __construct(float $amount, string $purpose, string $referenceId)
    {
        $this->amount = $amount;
        $this->purpose = $purpose;
        $this->referenceId = $referenceId;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // store in DB & optionally email
    }

    public function toDatabase($notifiable)
    {
        return [
            'amount' => $this->amount,
            'purpose' => $this->purpose,
            'reference_id' => $this->referenceId,
            'message' => "Your wallet has been debited ₦{$this->amount} for {$this->purpose}.",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');

        return (new MailMessage)
            ->subject('Wallet Debited')
            ->greeting('Hello ' . $notifiable->name)
            ->line("Your wallet has been debited ₦" . number_format($this->amount, 2) . " for {$this->purpose}.")
            ->action(
                'View Exam',
                "{$frontendUrl}/user/cbt/exams/{$this->referenceId}"
            )
            ->line('Thank you for using our service!');
    }
}
