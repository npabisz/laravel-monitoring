<?php

namespace Npabisz\LaravelMetrics\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleChatChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $webhookUrl = $notifiable->routeNotificationFor('googleChat');

        if (empty($webhookUrl)) {
            return;
        }

        $data = $notification->toGoogleChat($notifiable);

        try {
            Http::post($webhookUrl, $data);
        } catch (\Throwable $e) {
            Log::error('[Monitoring] Google Chat notification failed: ' . $e->getMessage());
        }
    }
}
