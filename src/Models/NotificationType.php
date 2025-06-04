<?php

namespace FinnWiel\ShazzooMobile\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function userPreferences()
    {
        return $this->hasMany(UserNotificationPreference::class);
    }
}
