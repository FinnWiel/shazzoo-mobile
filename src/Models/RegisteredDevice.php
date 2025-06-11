<?php

namespace FinnWiel\ShazzooMobile\Models;

use Illuminate\Database\Eloquent\Model;

class RegisteredDevice extends Model
{
    protected $fillable = ['user_id', 'token', 'device_type'];

    public function notificationPreferences()
    {
        return $this->hasMany(DeviceNotificationPreference::class, 'device_id');
    }
}
