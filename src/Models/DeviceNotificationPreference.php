<?php

namespace FinnWiel\ShazzooMobile\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceNotificationPreference extends Model
{
    protected $fillable = ['expo_token_id', 'notification_type_id', 'enabled'];

    public function expoToken()
    {
        return $this->belongsTo(ExpoToken::class);
    }

    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }
}
