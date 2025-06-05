<?php

namespace FinnWiel\ShazzooMobile\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use FinnWiel\ShazzooMobile\Models\ExpoToken;
use Illuminate\Support\Facades\Http;

class SendNotification extends Command
{
    protected $signature = 'shazzoo:notify
                            {--user= : Email or ID of a user}
                            {--type= : Notification type slug (e.g. calls, reminders)}
                            {--title= : Notification title}
                            {--body= : Notification body}';

    protected $description = 'Send an Expo push notification to users/devices respecting notification preferences';

    public function handle()
    {
        $this->info('Sending notifications...');

        $type = $this->option('type');
        $title = $this->option('title', 'Notification');
        $body = $this->option('body', '');

        if (! $type) {
            return $this->error('Please specify a notification type with --type');
        }

        if ($this->option('user')) {
            $user = User::where('email', $this->option('user'))
                ->orWhere('id', $this->option('user'))
                ->first();

            if (! $user) {
                return $this->error("User not found.");
            }

            $expoTokens = $user->expoTokens()
                ->whereHas('notificationPreferences', function ($query) use ($type) {
                    $query->where('enabled', true)
                        ->whereHas('notificationType', function ($q) use ($type) {
                            $q->where('name', $type);
                        });
                })
                ->get();
        } else {
            $expoTokens = ExpoToken::whereHas('notificationPreferences', function ($query) use ($type) {
                $query->where('enabled', true)
                    ->whereHas('notificationType', function ($q) use ($type) {
                        $q->where('name', $type);
                    });
            })->get();
        }

        if ($expoTokens->isEmpty()) {
            return $this->info('No devices found with that notification type enabled.');
        }

        foreach ($expoTokens as $token) {
            $payload = [
                'to' => $token->token,
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ];

            Http::post('https://exp.host/--/api/v2/push/send', $payload);
            $this->line("✓ Sent to {$token->token}");
        }

        $this->info("Done.");
    }
}
