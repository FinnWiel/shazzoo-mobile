<?php

namespace FinnWiel\ShazzooMobile\Console\Commands;

use FinnWiel\ShazzooMobile\Models\NotificationType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class SyncNotificationTypes extends Command
{
    protected $signature = 'shazzoo-mobile:sync-notification-types';
    protected $description = 'Sync notification types from config to database';

    public function handle()
    {
        $configTypes = Config::get('shazzoo-mobile.notifications.types', []);
        
        $this->info('Syncing notification types...');
        
        foreach ($configTypes as $type => $data) {
            $notificationType = NotificationType::updateOrCreate(
                ['name' => $type],
                [
                    'name' => $type,
                    'description' => $data['description'] ?? null,
                ]
            );
            
            $this->line("Synced type: {$notificationType->name}");
        }
        
        $this->info('Notification types synchronized successfully!');
    }
} 