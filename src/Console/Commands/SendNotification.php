<?php

namespace FinnWiel\ShazzooMobile\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use FinnWiel\ShazzooMobile\Models\NotificationType;
use Illuminate\Support\Facades\Http;

class SendNotification extends Command
{
    protected $signature = 'shazzoo:notify
                            {--user= : Email or ID of a user}
                            {--type= : Notification type slug (e.g. new_message)}
                            {--title= : Notification title}
                            {--body= : Notification body}
                            {--data= : JSON payload to send (optional)}';

    protected $description = 'Send an Expo push notification to a user or all users with preferences respected';

    public function handle()
    {
        $this->info('Sending notifications...');

        $users = collect();

        // Get single user or all
        if ($this->option('user')) {
            $user = User::where('email', $this->option('user'))
                        ->orWhere('id', $this->option('user'))
                        ->first();

            if (! $user) {
                return $this->error("User not found.");
            }

            $users->push($user);
        } else {
            $users = User::with('expoTokens')->get();
        }

        $type = $this->option('type');
        $title = $this->option('title', 'Notification');
        $body = $this->option('body', '');
        $extraData = json_decode($this->option('data', '{}'), true);

        foreach ($users as $user) {
            foreach ($user->expoTokens as $token) {
                // Respect per-device preferences
                if ($type) {
                    $enabled = $token->notificationPreferences()
                        ->whereHas('notificationType', fn ($q) => $q->where('name', $type))
                        ->where('enabled', true)
                        ->exists();

                    if (! $enabled) {
                        continue;
                    }
                }

                $payload = array_merge([
                    'to' => $token->token,
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ], $extraData);

                Http::post('https://exp.host/--/api/v2/push/send', $payload);
                $this->line("✓ Sent to {$token->token}");
            }
        }

        $this->info("Done.");
    }
}
