# گزارش تغییرات انجام‌شده در پروژه sfood

## ۱. پیکربندی نقشه و کلیدهای محیطی
- افزوده شدن پیکربندی جامع `config/maps.php` شامل درایور پیش‌فرض ایران، کلیدهای گوگل/نشان، مختصات مرکز پیش‌فرض و قالب/فهرست آدرس‌های تایل برای نقشه که از `.env` خوانده می‌شوند. این کار امکان تغییر ارائه‌دهنده نقشه و سرور تایل را بدون تغییر کد فراهم می‌کند.【F:config/maps.php†L4-L27】
- به‌روزرسانی `.env.example` با متغیرهای جدید `MAP_DRIVER`, `NESHAN_API_KEY`, `MAP_TILE_URL`, `MAP_TILE_URLS` تا استقرار بتواند منبع تایل قابل‌دسترسی را تنظیم کند.【F:.env.example†L8-L14】

## ۲. لایه انتزاعی ارائه‌دهنده نقشه
- تعریف اینترفیس `MapProviderInterface` با عملیات autocomplete، place details، distance/route matrix، geocode و پیکربندی وب برای یکسان‌سازی خروجی‌ها بین ارائه‌دهنده‌ها.【F:app/Services/Map/MapProviderInterface.php†L5-L56】
- پیاده‌سازی `MapService` به‌عنوان روکش نازک روی اینترفیس برای فراخوانی وابسته به زبان فعال برنامه.【F:app/Services/Map/MapService.php†L7-L48】
- رجیستر شدن بایندینگ در `MapServiceProvider` برای سوییچ بین `IranMapProvider` و `GoogleMapProvider` بر اساس پیکربندی و ارائه singleton سراسری `MapService`.【F:app/Providers/MapServiceProvider.php†L11-L33】

## ۳. ارائه‌دهندگان نقشه
- پیاده‌سازی کامل `GoogleMapProvider` که منطق قدیمی گوگل را با استفاده از Http facade منتقل می‌کند و وب‌کانفیگ شامل کلید JS و تنظیمات تایل/مرکز پیش‌فرض را برمی‌گرداند.【F:app/Services/Map/GoogleMapProvider.php†L8-L170】
- پیاده‌سازی `IranMapProvider` برای مصرف API نشان، نگاشت خروجی‌ها به ساختارهای مشابه گوگل (autocomplete، place details، distance، geocode) و انتشار پیکربندی وب شامل آدرس‌های تایل از تنظیمات جدید.【F:Modules/Iran/Services/IranMapProvider.php†L156-L191】

## ۴. تغییرات بک‌اند استفاده‌کننده از لایه نقشه
- تزریق `MapService` در `ConfigController` و جایگزینی تماس‌های مستقیم گوگل با فراخوانی‌های انتزاعی، در حالی‌که پیاده‌سازی‌های قدیمی برای مرجع کامنت شده باقی مانده‌اند.【F:app/Http/Controllers/Api/V1/ConfigController.php†L25-L43】【F:app/Http/Controllers/Api/V1/ConfigController.php†L380-L511】
- به‌روزرسانی Trait سفارش (`PlaceNewOrder`) برای استفاده از `MapService->distance` با بازگشت graceful به Haversine در صورت خطا و نگه‌داشتن کد قدیمی گوگل در کامنت.【F:app/Traits/PlaceNewOrder.php†L1076-L1124】

## ۵. پردازش مختصات و داده مناطق
- بهبود `Helpers::format_coordiantes` برای پیمایش آرایه‌های تو در تو و برگرداندن اشیاء `lat/lng` معتبر، جلوگیری از ورودی‌های معیوب قبل از ارسال به فرانت‌اند.【F:app/CentralLogics/Helpers.php†L2227-L2246】

## ۶. لایه مشترک فرانت‌اند (Leaflet/AppMap)
- ایجاد و گسترش partial `resources/views/partials/map-script.blade.php` برای بارگذاری Leaflet/leaflet.draw، تعریف `window.AppMap` با ایجاد نقشه، افزودن لایه تایل با fallback و پیام کاربرپسند در صورت شکست، افزودن marker/polygon، تناسب محدوده و استخراج مختصات. تمامی آدرس‌های تایل از پیکربندی خوانده می‌شوند و هیچ آدرس OSM هاردکد نشده است.【F:resources/views/partials/map-script.blade.php†L1-L237】

## ۷. صفحات مدیریت محدوده (Zone)
- بازنویسی منطق نقشه در `admin-views/zone/index.blade.php` برای کار با Leaflet: نرمال‌سازی مختصات دریافتی به ساختار `lat/lng`، رسم چندضلعی‌ها با `AppMap`, حذف ایمن آخرین لایه ترسیم‌شده به‌جای `setMap`, و تطبیق محدوده نقشه بر اساس تمام چندضلعی‌های معتبر.【F:resources/views/admin-views/zone/index.blade.php†L417-L606】
- صفحه ویرایش مناطق نیز به همین منطق Leaflet و ساختار مختصات سازگار مجهز شد تا چندضلعی اولیه و همپوشانی‌ها بدون خطا نمایش داده شوند.【F:resources/views/admin-views/zone/edit.blade.php†L350-L486】

