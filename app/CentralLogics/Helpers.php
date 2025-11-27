<?php
namespace App\CentralLogics;

use App\Models\Allergy;
use App\Models\Nutrition;
use DateTime;
use Exception;
use DatePeriod;
use DateInterval;
use App\Models\Food;
use App\Models\User;
use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Order;
use App\Library\Payer;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Expense;
use App\Models\TimeLog;
use App\Models\Vehicle;
use App\Traits\Payment;
use App\Mail\PlaceOrder;
use App\Models\CashBack;
use App\Models\Category;
use App\Models\Currency;
use App\Models\DMReview;
use App\Library\Receiver;
use App\Models\Restaurant;
use App\Models\VisitorLog;
use App\Models\DataSetting;
use App\Models\DeliveryMan;
use App\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\Models\VariationOption;
use App\Models\RestaurantWallet;
use App\CentralLogics\OrderLogic;
use App\Models\DeliveryManWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderVerificationMail;
use App\Models\NotificationMessage;
use App\Models\NotificationSetting;
use App\Models\SubscriptionPackage;
use App\Traits\PaymentGatewayTrait;
use Illuminate\Support\Facades\App;
use App\Mail\SubscriptionSuccessful;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\CentralLogics\RestaurantLogic;
use App\Mail\SubscriptionRenewOrShift;
use App\Models\RestaurantSubscription;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Library\Payment as PaymentInfo;
use App\Models\SubscriptionTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\RestaurantNotificationSetting;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Models\SubscriptionBillingAndRefundHistory;

use App\Traits\NotificationDataSetUpTrait;
use App\Services\Jalali\JalaliDateService;

class Helpers
{
    use PaymentGatewayTrait, NotificationDataSetUpTrait;

    public static function to_jalali($date, string $format = 'Y/m/d H:i:s'): ?string
    {
        if (!$date) {
            return null;
        }
        try {
            return JalaliDateService::toJalali($date, $format);
        } catch (\Exception $e) {
            return $date; // Return original on failure
        }
    }

    public static function to_gregorian(?string $jalaliDate): ?Carbon
    {
        if (!$jalaliDate) {
            return null;
        }
        try {
            return JalaliDateService::toGregorian($jalaliDate);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function jalali_date_format($data)
    {
        return self::to_jalali($data, 'd M Y');
    }

    public static function jalali_time_date_format($data)
    {
        $time_format = config('timeformat') ?? 'H:i';
        return self::to_jalali($data, 'd M Y, ' . $time_format);
    }

    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => translate($error[0]) ]);
        }
        return $err_keeper;
    }

    // ... (rest of the original file content)
    public static function time_date_format($data){
        return self::jalali_time_date_format($data);
    }
    public static function date_format($data){
        return self::jalali_date_format($data);
    }
    public static function time_format($data){
            $time=config('timeformat') ?? 'H:i';
        return  Carbon::parse($data)->locale(app()->getLocale())->translatedFormat($time);
    }

 // ... (rest of the original file content)
}
