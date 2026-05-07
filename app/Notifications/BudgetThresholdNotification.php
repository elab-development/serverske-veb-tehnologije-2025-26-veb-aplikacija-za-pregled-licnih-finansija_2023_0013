<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetThresholdNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Budget $budget,
        public readonly int $threshold,
        public readonly float $spent,
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upozorenje o budzetu - ' . $this->budget->category->name);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'budget_id' => $this->budget->id,
            'category_id' => $this->budget->category_id,
            'category_name' => $this->budget->category->name,
            'threshold' => $this->threshold,
            'limit_amount' => (float) $this->budget->limit_amount,
            'spent' => $this->spent,
        ];
    }
}
