<?php

namespace Npabisz\LaravelMonitoring\Notifications;

use Illuminate\Notifications\Notifiable;

/**
 * Anonymous notifiable for sending monitoring alerts
 * without requiring a User model.
 */
class MonitoringAlertNotifiable
{
    use Notifiable;

    public function routeNotificationForMail(): array|string
    {
        $recipients = config('monitoring.notifications.mail.to', []);

        if (is_string($recipients)) {
            return array_map('trim', explode(',', $recipients));
        }

        return $recipients;
    }

    public function routeNotificationForSlack(): ?string
    {
        return config('monitoring.notifications.slack.webhook_url');
    }

    public function routeNotificationForDiscord(): ?string
    {
        return config('monitoring.notifications.discord.webhook_url');
    }

    public function routeNotificationForGoogleChat(): ?string
    {
        return config('monitoring.notifications.google_chat.webhook_url');
    }
}
