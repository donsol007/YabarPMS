<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationsBell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function refreshUnread(): void
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function recent(): \Illuminate\Support\Collection
    {
        return auth()->user()->notifications()->take(8)->get();
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        $this->refreshUnread();
    }

    public function markAsRead(string $id): void
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification && ! $notification->read_at) {
            $notification->markAsRead();
        }

        $this->refreshUnread();

        if ($notification?->data['url'] ?? null) {
            $this->redirect($notification->data['url']);
        }
    }

    public function render()
    {
        return view('livewire.notifications-bell');
    }
}