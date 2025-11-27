
<?php

namespace App\Services\Jalali;

use Morilog\Jalali\Jalalian;

class JalaliDateService
{
    public static function toJalali(string $gregorianDate): string
    {
        return Jalalian::fromCarbon(new \Carbon\Carbon($gregorianDate))->format('Y/m/d');
    }

    public static function toGregorian(string $jalaliDate): string
    {
        return Jalalian::fromFormat('Y/m/d', $jalaliDate)->toCarbon()->toDateString();
    }
}
