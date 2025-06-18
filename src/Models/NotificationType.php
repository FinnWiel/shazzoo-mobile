<?php

namespace FinnWiel\ShazzooNotify\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $fillable = ['name', 'description'];

    public function devicePreferences()
    {
        return $this->hasMany(DeviceNotificationPreference::class);
    }
}
