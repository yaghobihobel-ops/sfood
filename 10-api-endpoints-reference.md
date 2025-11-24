# API Endpoints Reference (V1)

> Controllers live under `app/Http/Controllers/Api/V1` unless noted. Authenticated subgroups rely on Passport (`auth:api`) plus custom middlewares shown per block.

## Zones & Ads
| Method & Path | Controller@method | Notes |
| --- | --- | --- |
| GET `/zone/list` | ZoneController@get_zones | Localization-react base middleware.【F:routes/api/v1/api.php†L62-L66】 |
| GET `/zone/check` | ZoneController@zonesCheck | Zone detection helper.【F:routes/api/v1/api.php†L64-L66】 |
| GET `/advertisement/list` | AdvertisementController@get_adds | Public ads feed.【F:routes/api/v1/api.php†L69-L70】 |
| GET `/addon-category/list` | AddonCategoryController@getList | Public add-on catalog.【F:routes/api/v1/api.php†L69-L71】 |

## Authentication Flows (`auth/*`)
| Method & Path | Controller@method | Validation/Dependencies |
| --- | --- | --- |
| POST `/auth/sign-up` | Auth\CustomerAuthController@register | Validates user credentials, issues Passport token on success.【F:routes/api/v1/api.php†L73-L105】【F:app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php†L31-L100】 |
| POST `/auth/login` | Auth\CustomerAuthController@login | Passport token issuance after credentials.【F:routes/api/v1/api.php†L73-L87】 |
| POST `/auth/verify-phone` | Auth\CustomerAuthController@verify_phone_or_email | OTP validation with Carbon-based throttling and optional guest cart merge.【F:routes/api/v1/api.php†L75-L88】【F:app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php†L31-L140】 |
| POST `/auth/update-info` | Auth\CustomerAuthController@update_info | Profile completion hook.【F:routes/api/v1/api.php†L75-L88】 |
| POST `/auth/forgot-password` | Auth\PasswordResetController@resetPasswordRequest | OTP/request creation.【F:routes/api/v1/api.php†L80-L83】 |
| POST `/auth/verify-token` | Auth\PasswordResetController@verifyToken | OTP verification.【F:routes/api/v1/api.php†L80-L83】 |
| PUT `/auth/reset-password` | Auth\PasswordResetController@resetPasswordSubmit | Credential update.【F:routes/api/v1/api.php†L80-L83】 |
| PUT `/auth/firebase-reset-password` | Auth\PasswordResetController@firebase_auth_verify | Firebase-backed reset.【F:routes/api/v1/api.php†L80-L83】 |
| POST `/auth/guest/request` | Auth\CustomerAuthController@guest_request | Generates guest identity/token.【F:routes/api/v1/api.php†L85-L88】 |
| POST `/auth/firebase-verify-token` | Auth\CustomerAuthController@firebase_auth_verify | Firebase credential validation.【F:routes/api/v1/api.php†L85-L88】 |

### Delivery-Man Auth (`auth/delivery-man`, middleware `actch:deliveryman_app`)
Key endpoints: login/store/forgot/verify-token/firebase-verify/reset-password using DeliveryManLoginController & DMPasswordResetController.【F:routes/api/v1/api.php†L89-L96】

### Vendor Auth (`auth/vendor`, middleware `actch:restaurant_app`)
Key endpoints: login/register/forgot/verify/reset-password using VendorLoginController & VendorPasswordResetController.【F:routes/api/v1/api.php†L97-L103】

## Subscription (Vendor, `actch:restaurant_app`)
- GET `/vendor/package-view`, POST `/vendor/business_plan`, POST `/vendor/cancel-subscription`, GET `/vendor/check-product-limits` → Vendor\SubscriptionController handles pricing/limits.【F:routes/api/v1/api.php†L106-L112】

## Delivery-Man Panel (`prefix delivery-man`, middleware `actch:deliveryman_app`)
Highlights include profile, notifications, live location, order lifecycle, payment status updates, wallet/withdraw, OTP, shift, and messaging. All under DeliverymanController and ConversationController with `dm.api` for authenticated calls.【F:routes/api/v1/api.php†L114-L171】

## Vendor Panel (`prefix vendor`, middleware `vendor.api` + `actch:restaurant_app`)
- **Profile/Orders/Notifications:** VendorController endpoints for profile, campaign join/leave, bank/withdraw, FCM tokens, earnings, and order CRUD/statuses.【F:routes/api/v1/api.php†L174-L206】
- **Reports:** ReportController exposes expense/tax/transaction/order/campaign/food/disbursement reports and subscription transaction export.【F:routes/api/v1/api.php†L212-L223】
- **Withdraw Methods:** WithdrawMethodController CRUD plus defaults.【F:routes/api/v1/api.php†L225-L233】
- **Coupons & Ads:** CouponController CRUD/search; Vendor\AdvertisementController handles CRUD/status/copy for ads.【F:routes/api/v1/api.php†L235-L257】
- **Business Setup:** BusinessSettingsController updates restaurant info/setup, schedules, suggestions.【F:routes/api/v1/api.php†L259-L276】
- **Addons/Attributes/Products:** AttributeController list; AddOnController CRUD; FoodController CRUD/status/reviews/stock and reply updates.【F:routes/api/v1/api.php†L277-L314】
- **Delivery Men:** Vendor\DeliveryManController CRUD, search, assignment.【F:routes/api/v1/api.php†L316-L328】
- **POS:** POSController order placement/list and customer lookup.【F:routes/api/v1/api.php†L330-L334】
- **Messaging:** VendorConversationController list/search/details/send.【F:routes/api/v1/api.php†L336-L340】
- **OTP/Table:** send_order_otp and add_dine_in_table_number on VendorController.【F:routes/api/v1/api.php†L341-L342】

## Config & Catalog
- ConfigController exposes configuration payloads and map/analytics endpoints: `/config`, `/config/get-zone-id`, `/config/place-api-autocomplete`, `/config/distance-api`, `/config/place-api-details`, `/config/geocode-api`, `/config/get-analytic-scripts`, `/config/get-vehicles`, `/config/get-PaymentMethods`, `/config/offline_payment_method_list`, `/vehicle/extra_charge`, `/most-tips`.【F:routes/api/v1/api.php†L232-L270】
- ProductController covers discovery (latest/popular/recommended/most-reviewed/set-menu/search/related), details, rating/reviews submission (auth), allergy/nutrition name lists, and restaurant-popular endpoints.【F:routes/api/v1/api.php†L238-L309】
- RestaurantController surfaces listing by filter/latest/popular/dine-in/recommended/visit-again, details (logs visits), reviews, search, coupons.【F:routes/api/v1/api.php†L238-L269】
- CategoryController: listing, children, category products/all products, restaurants per category.【F:routes/api/v1/api.php†L270-L286】
- CuisineController: list cuisines and restaurants by cuisine.【F:routes/api/v1/api.php†L288-L291】
- BannerController: list banners.【F:routes/api/v1/api.php†L292-L294】

## Customer (middleware `auth:api` unless noted)
- Profile/notifications/zone update/interest/FCM token/guest removal via CustomerController; loyalty transfer/transactions; wallet transactions/add-fund/bonuses; address CRUD; messaging; wishlist CRUD; subscription schedule updates via OrderSubscriptionController.【F:routes/api/v1/api.php†L293-L335】
- Guest-friendly ordering (`apiGuestCheck`): order list/details/place/track/payment updates/refund, reorder, food list, order-again, cart CRUD (CartController).【F:routes/api/v1/api.php†L336-L374】
- Coupons (`auth:api`): list/apply plus restaurant-wise public lookup.【F:routes/api/v1/api.php†L358-L364】
- Cashback (`auth:api`): list and calculate cashback.【F:routes/api/v1/api.php†L366-L370】
- Newsletter subscribe and landing pages (including React landing/registration) via ConfigController/NewsletterController.【F:routes/api/v1/api.php†L372-L270】

## Campaigns & Promotions
- CampaignController basic/item campaign listings and details; Zone/category/restaurant/product promotions accessible without auth.【F:routes/api/v1/api.php†L344-L357】

## API V2
- POST `/api/v2/ls-lib-update` → `Api\V2\LsLibController@lib_update`, reserved for library updates.【F:routes/api/v2/api.php†L1-L7】

