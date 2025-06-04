<?php

namespace FinnWiel\ShazzooMobile\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotificationPreference extends Model
{
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'notification_type_id',
        'enabled',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }
}
