<?php

namespace FinnWiel\ShazzooMobile\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceNotificationPreference extends Model
{
    protected $fillable = ['device_id', 'notification_type_id', 'enabled'];

    public function device()
    {
        return $this->belongsTo(RegisteredDevice::class);
    }

    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }
}
