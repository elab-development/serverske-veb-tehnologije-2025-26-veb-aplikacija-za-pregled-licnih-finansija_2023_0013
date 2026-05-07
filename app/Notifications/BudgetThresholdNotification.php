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
        if (! empty($notifiable->email) && $notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $percent = $this->budget->limit_amount > 0
            ? round(($this->spent / (float) $this->budget->limit_amount) * 100)
            : 0;

        return (new MailMessage)
            ->subject('Upozorenje o budzetu - ' . $this->budget->category->name)
            ->view('emails.budget-threshold', [
                'user' => $notifiable,
                'category' => $this->budget->category,
                'limit' => (float) $this->budget->limit_amount,
                'spent' => $this->spent,
                'percent' => $percent,
                'message' => $this->messageLine(),
                'url' => url('/budgets'),
            ]);
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
            'message' => $this->messageLine(),
        ];
    }

    private function messageLine(): string
    {
        return $this->threshold >= 100
            ? "Presli ste mesecni budzet za kategoriju \"{$this->budget->category->name}\"."
            : "Potrosili ste preko 80% mesecnog budzeta za kategoriju \"{$this->budget->category->name}\".";
    }
}
