<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ClientRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Client $client)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New client registered',
            'body' => "{$this->client->full_name} ({$this->client->client_id}) has been registered.",
            'url' => route('clients.edit', $this->client),
            'icon' => 'user-plus',
        ];
    }
}