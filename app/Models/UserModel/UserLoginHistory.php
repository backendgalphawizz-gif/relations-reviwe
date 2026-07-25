<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    use HasFactory;

    protected $table = 'user_login_histories';

    protected $fillable = [
        'userId',
        'deviceId',
        'appId',
        'fcmToken',
        'deviceManufacturer',
        'deviceModel',
        'appVersion',
        'deviceLocation',
        'ipAddress',
        'userAgent',
        'status',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];
}
