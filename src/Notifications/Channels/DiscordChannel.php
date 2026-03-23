<?php

namespace Npabisz\LaravelMetrics\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $webhookUrl = $notifiable->routeNotificationFor('discord');

        if (empty($webhookUrl)) {
            return;
        }

        $data = $notification->toDiscord($notifiable);

        try {
            Http::post($webhookUrl, $data);
        } catch (\Throwable $e) {
            Log::error('[Monitoring] Discord notification failed: ' . $e->getMessage());
        }
    }
}
