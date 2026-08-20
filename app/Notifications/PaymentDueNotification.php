<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $clientName,
        public string $description,
        public string $amount,
        public string $dueDate,
        public string $url,
        public ?string $ref = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Payment due soon',
            'body' => "{$this->clientName} — {$this->description} of {$this->amount} is due on {$this->dueDate}.",
            'url' => $this->url,
            'icon' => 'bell-ring',
            'ref' => $this->ref,
        ];
    }
}