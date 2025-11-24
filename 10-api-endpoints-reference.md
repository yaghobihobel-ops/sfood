# API Endpoints Reference (V1)

> All changes must remain additive: keep existing routes/JSON/schema/required params intact per compatibility guardrails.

## Public Discovery
| Method & Path | Controller@method | Notes |
| --- | --- | --- |
| GET `/zone/list` | ZoneController@get_zones | Zone listing (localization/react middleware).【F:routes/api/v1/api.php†L62-L66】 |
| GET `/zone/check` | ZoneController@zonesCheck | Location check against configured zones.【F:routes/api/v1/api.php†L62-L66】 |
| GET `/advertisement/list` | AdvertisementController@get_adds | Public ads feed.【F:routes/api/v1/api.php†L69-L70】 |
| GET `/addon-category/list` | AddonCategoryController@getList | Add-on categories for menu items.【F:routes/api/v1/api.php†L69-L71】 |

## Authentication (`prefix auth`)
| Method & Path | Controller@method | Validation/Key Dependencies |
| --- | --- | --- |
| POST `/auth/sign-up` | Auth\CustomerAuthController@register | FormRequest validation + Passport token issuance.【F:routes/api/v1/api.php†L73-L88】【F:app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php†L31-L115】 |
| POST `/auth/login` | Auth\CustomerAuthController@login | Password auth → Passport token.【F:routes/api/v1/api.php†L73-L88】 |
| POST `/auth/verify-phone` | Auth\CustomerAuthController@verify_phone_or_email | OTP verification, merges guest data if needed.【F:routes/api/v1/api.php†L75-L88】 |
| POST `/auth/update-info` | Auth\CustomerAuthController@update_info | Completes profile data post-auth.【F:routes/api/v1/api.php†L75-L88】 |
| POST `/auth/guest/request` | Auth\CustomerAuthController@guest_request | Issues guest identity/token.【F:routes/api/v1/api.php†L85-L88】 |
| POST `/auth/firebase-verify-token` | Auth\CustomerAuthController@firebase_auth_verify | Firebase credential validation.【F:routes/api/v1/api.php†L85-L88】 |
| Password reset trio | Auth\PasswordResetController@resetPasswordRequest / verifyToken / resetPasswordSubmit | OTP + password update + Firebase reset route.【F:routes/api/v1/api.php†L80-L83】 |

### Delivery Auth (`auth/delivery-man`, middleware `actch:deliveryman_app`)
Login/register/reset/verify/fcm via DeliveryManLoginController & DMPasswordResetController; all routes gated to delivery app channel.【F:routes/api/v1/api.php†L89-L96】

### Vendor Auth (`auth/vendor`, middleware `actch:restaurant_app`)
Vendor login/register/reset/verify handled by VendorLoginController & VendorPasswordResetController with restaurant-app channel enforcement.【F:routes/api/v1/api.php†L97-L103】

## Delivery-Man Panel (`prefix delivery-man`)
- **Core:** profile, notifications, active status, FCM token, shifts, remove-account via DeliverymanController under `dm.api` + `actch:deliveryman_app`.【F:routes/api/v1/api.php†L114-L171】
- **Orders:** current/latest/all/history, accept/update status/payment, details/tracking endpoints.【F:routes/api/v1/api.php†L124-L171】
- **Location:** record_location_data + last-location feed for live tracking.【F:routes/api/v1/api.php†L114-L171】
- **Chat:** conversation list/search/details/send via ConversationController (`delivery-man/message/*`).【F:routes/api/v1/api.php†L147-L153】
- **Withdrawals:** withdraw-method CRUD + disbursement reports and make_payment/make_wallet_adjustment hooks.【F:routes/api/v1/api.php†L155-L171】【F:routes/api/v1/api.php†L205-L210】

## Vendor Panel (`prefix vendor`, middleware `vendor.api` + `actch:restaurant_app`)
- **Profile & Earnings:** notifications/profile/earning-info/update-profile/update-active-status/campaign join-leave, withdraw list, bank info, request withdraw, announcements, Firebase token.【F:routes/api/v1/api.php†L174-L210】
- **Orders & POS:** order listing/detail/update, searched food, customer address update, POS placement/list, dine-in OTP/table helpers.【F:routes/api/v1/api.php†L182-L207】【F:routes/api/v1/api.php†L330-L342】
- **Reports:** expense, tax, transaction statement, order/campaign/food/disbursement reports, subscription transaction export via ReportController/SubscriptionController.【F:routes/api/v1/api.php†L211-L223】
- **Withdraw Methods:** withdraw-method CRUD/defaults plus list of methods.【F:routes/api/v1/api.php†L224-L231】
- **Coupons & Ads:** coupon CRUD/search/view and advertisement CRUD/status/copy under Vendor namespaces.【F:routes/api/v1/api.php†L233-L250】
- **Business Setup:** update basic info/setup, characteristic suggestions; attribute/addon CRUD; food CRUD/status/availability/stock/replies; delivery-man CRUD/search/assignment.【F:routes/api/v1/api.php†L256-L342】

## Customer & Orders
- **Profile/utility:** profile/notification list/update, update-zone, interest/FCM, guest removal under CustomerController (`auth:api`).【F:routes/api/v1/api.php†L293-L314】
- **Wishlist/Addresses/Conversation:** CRUD endpoints for wishlist, addresses, and chat via ConversationController.【F:routes/api/v1/api.php†L293-L342】
- **Wallet/Loyalty:** wallet transactions/add-fund/bonus/refund transfer; loyalty point earn/burn history via WalletController/LoyaltyPointController.【F:routes/api/v1/api.php†L293-L342】
- **Orders & Cart (with `apiGuestCheck`):** cart CRUD/apply coupons, place orders, reorder/order-again, payment status/refund status/track order via CartController & OrderController; subscriptions managed by OrderSubscriptionController.【F:routes/api/v1/api.php†L335-L375】
- **Promotions:** coupon apply/list, restaurant-wise coupons, cashback list/calc, campaign item/basic listings via CouponController/CashBackController/CampaignController.【F:routes/api/v1/api.php†L344-L370】

## Catalog & Config
- **Products/Restaurants/Categories/Cuisines/Banners:** browsing/searching/filtering/latest/popular/recommended/most-reviewed/set-menu with ProductController, RestaurantController, CategoryController, CuisineController, BannerController.【F:routes/api/v1/api.php†L238-L314】
- **Config endpoints:** `/config` payload, zone lookup, map place/geocode/distance, analytic scripts, vehicle lists, payment/offline methods, extra charges, most-tips via ConfigController endpoints at end of file.【F:routes/api/v1/api.php†L492-L508】【F:app/Http/Controllers/Api/V1/ConfigController.php†L33-L200】

## API V2
`POST /api/v2/ls-lib-update` → `Api\V2\LsLibController@lib_update` for library update checks (keeps existing V1 stable).【F:routes/api/v2/api.php†L1-L7】
