<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Jalali\JalaliDateService;

class UserNotification extends Model
{
    use HasFactory;
    
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCreatedAtAttribute($value)
    {
        return JalaliDateService::toJalali($value, 'Y-m-d H:i:s');
    }
}
