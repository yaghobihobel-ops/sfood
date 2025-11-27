<?php

namespace App\Http\Controllers\Admin;

use App\Models\Food;
use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Restaurant;
use App\Models\DeliveryMan;
use App\Models\OrderDetail;
use App\Models\Translation;
use App\Exports\OrderExport;
use App\Mail\RefundRejected;
use App\Models\ItemCampaign;
use App\Models\OrderPayment;
use App\Models\RefundReason;
use App\Traits\PlaceNewOrder;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Scopes\RestaurantScope;
use App\CentralLogics\OrderLogic;
use App\Models\OrderCancelReason;
use App\Exports\OrderRefundExport;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\CustomerLogic;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RestaurantOrderlistExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use MatanYadaev\EloquentSpatial\Objects\Point;

class OrderController extends Controller
{
    use PlaceNewOrder;
    public function list($status, Request $request)
    {
        $key = explode(' ', $request['search']);

        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if (session()->has('order_filter') && $status != 'dine_in') {
            $request = json_decode(session('order_filter'));
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'restaurant'])
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('restaurant', function ($q) use ($request) {
                    return $q->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->whereRaw('created_at <> schedule_at');
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'pending', function ($query) {
                return $query->Pending();
            })
            ->when($status == 'accepted', function ($query) {
                return $query->AccepteByDeliveryman();
            })
            ->when($status == 'processing', function ($query) {
                return $query->Preparing();
            })
            ->when($status == 'food_on_the_way', function ($query) {
                return $query->FoodOnTheWay();
            })
            ->when($status == 'delivered', function ($query) {
                return $query->Delivered();
            })
            ->when($status == 'canceled', function ($query) {
                return $query->Canceled();
            })
            ->when($status == 'failed', function ($query) {
                return $query->failed();
            })
            ->when($status == 'requested', function ($query) {
                return $query->Refund_requested();
            })
            ->when($status == 'rejected', function ($query) {
                return $query->Refund_request_canceled();
            })
            ->when($status == 'refunded', function ($query) {
                return $query->Refunded();
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->Scheduled();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when($status == 'dine_in', function ($query) {
                return $query->where('order_type', 'dine_in');
            })
            ->when(!in_array($status, ['all', 'scheduled', 'canceled', 'requested', 'refunded', 'delivered', 'failed', 'dine_in']), function ($query) {
                return $query->OrderScheduledIn(30);
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('restaurant', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('order_status', $request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                return $query->scheduled();
            })
            ->when(isset($request->order_type), function ($query) use ($request) {
                return $query->where('order_type', $request->order_type);
            })
            ->when($request?->from_date != null && $request?->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [Helpers::to_gregorian($request->from_date), Helpers::to_gregorian($request->to_date)]);
            })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })
            ->Notpos()
            ->hasSubscriptionToday()
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = $request?->orderStatus ?? [];
        $scheduled =  $request?->scheduled ?? 0;
        $vendor_ids =  $request?->vendor ?? [];
        $zone_ids = $request?->zone ?? [];
        $from_date =  $request?->from_date ?? null;
        $to_date = $request?->to_date ?? null;
        $order_type =  $request?->order_type ?? null;
        $total = $orders->total();

        return view('admin-views.order.list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'order_type'));
    }

    public function export_orders($status, $type, Request $request)
    {

        try {
            $key = explode(' ', $request['search']);

            if (session()->has('zone_filter') == false) {
                session()->put('zone_filter', 0);
            }

            if (session()->has('order_filter')) {
                $request = json_decode(session('order_filter'));
            }

            $orders = Order::with(['customer', 'restaurant', 'refund'])
                ->when(isset($request->zone), function ($query) use ($request) {
                    return $query->whereHas('restaurant', function ($q) use ($request) {
                        return $q->whereIn('zone_id', $request->zone);
                    });
                })
                ->when($status == 'scheduled', function ($query) {
                    return $query->whereRaw('created_at <> schedule_at');
                })
                ->when($status == 'searching_for_deliverymen', function ($query) {
                    return $query->SearchingForDeliveryman();
                })
                ->when($status == 'pending', function ($query) {
                    return $query->Pending();
                })
                ->when($status == 'accepted', function ($query) {
                    return $query->AccepteByDeliveryman();
                })
                ->when($status == 'processing', function ($query) {
                    return $query->Preparing();
                })
                ->when($status == 'food_on_the_way', function ($query) {
                    return $query->FoodOnTheWay();
                })
                ->when($status == 'delivered', function ($query) {
                    return $query->Delivered();
                })
                ->when($status == 'canceled', function ($query) {
                    return $query->Canceled();
                })
                ->when($status == 'failed', function ($query) {
                    return $query->failed();
                })
                ->when($status == 'requested', function ($query) {
                    return $query->Refund_requested();
                })
                ->when($status == 'rejected', function ($query) {
                    return $query->Refund_request_canceled();
                })
                ->when($status == 'refunded', function ($query) {
                    return $query->Refunded();
                })
                ->when($status == 'rejected', function ($query) {
                    return $query->Refund_request_canceled();
                })
                ->when($status == 'scheduled', function ($query) {
                    return $query->Scheduled();
                })
                ->when($status == 'on_going', function ($query) {
                    return $query->Ongoing();
                })
                ->when($status == 'dine_in', function ($query) {
                    return $query->where('order_type', 'dine_in');
                })
                ->when(!in_array($status, ['all', 'scheduled', 'canceled', 'requested', 'refunded', 'delivered', 'failed', 'dine_in']), function ($query) {
                    return $query->OrderScheduledIn(30);
                })
                ->when(isset($request->vendor), function ($query) use ($request) {
                    return $query->whereHas('restaurant', function ($query) use ($request) {
                        return $query->whereIn('id', $request->vendor);
                    });
                })
                ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                    return $query->whereIn('order_status', $request->orderStatus);
                })
                ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                    return $query->scheduled();
                })
                ->when(isset($request->order_type), function ($query) use ($request) {
                    return $query->where('order_type', $request->order_type);
                })
                ->when($request?->from_date != null && $request?->to_date != null, function ($query) use ($request) {
                     return $query->whereBetween('created_at', [Helpers::to_gregorian($request->from_date), Helpers::to_gregorian($request->to_date)]);
                })
                ->when(isset($key), function ($query) use ($key) {
                    return $query->where(function ($q) use ($key) {
                        foreach ($key as $k => $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                            $search[$k] = $value;
                        }
                    });
                })
                ->Notpos()
                ->orderBy('schedule_at', 'desc')
                ->hasSubscriptionToday()


                ->when($status == 'offline_payments' && $request?->payment_status == 'pending', function ($query) {
                    return $query->whereHas('offline_payments', function ($query) {
                        return $query->where('status', 'pending');
                    });
                })
                ->when($status == 'offline_payments' && $request?->payment_status == 'all', function ($query) {
                    return $query->has('offline_payments');
                })
                ->when($status == 'offline_payments' && $request?->payment_status == 'denied', function ($query) {
                    return $query->whereHas('offline_payments', function ($query) {
                        return $query->where('status', 'denied');
                    });
                })
                ->when($status == 'offline_payments' && $request?->payment_status == 'verified', function ($query) {
                    return $query->whereHas('offline_payments', function ($query) {
                        return $query->where('status', 'verified');
                    });
                })
                ->get();

            if (in_array($status, ['requested', 'rejected', 'refunded'])) {
                $data = [
                    'orders' => $orders,
                    'type' => $request->order_type ?? translate('messages.all'),
                    'status' => $status,
                    'order_status' => isset($request->orderStatus) ? implode(', ', $request->orderStatus) : null,
                    'search' => $request->search ?? $key[0] ?? null,
                    'from' => $request->from_date ?? null,
                    'to' => $request->to_date ?? null,
                    'zones' => isset($request->zone) ? Helpers::get_zones_name($request->zone) : null,
                    'restaurant' => isset($request->vendor) ? Helpers::get_restaurant_name($request->vendor) : null,
                ];

                if ($type == 'excel') {
                    return Excel::download(new OrderRefundExport($data), 'RefundOrders.xlsx');
                } else if ($type == 'csv') {
                    return Excel::download(new OrderRefundExport($data), 'RefundOrders.csv');
                }
            }


            $data = [
                'orders' => $orders,
                'type' => $request->order_type ?? translate('messages.all'),
                'status' => $status,
                'order_status' => isset($request->orderStatus) ? implode(', ', $request->orderStatus) : null,
                'search' => $request->search ?? $key[0] ?? null,
                'from' => $request->from_date ?? null,
                'to' => $request->to_date ?? null,
                'zones' => isset($request->zone) ? Helpers::get_zones_name($request->zone) : null,
                'restaurant' => isset($request->vendor) ? Helpers::get_restaurant_name($request->vendor) : null,
            ];

            if ($type == 'excel') {
                return Excel::download(new OrderExport($data), 'Orders.xlsx');
            } else if ($type == 'csv') {
                return Excel::download(new OrderExport($data), 'Orders.csv');
            }
        } catch (\Exception $e) {
            dd($e);
            Toastr::error("line___{$e->getLine()}", $e->getMessage());
            info(["line___{$e->getLine()}", $e->getMessage()]);
            return back();
        }
    }

    public function dispatch_list($status, Request $request)
    {

        $key = explode(' ', $request?->search);
        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
            $zone_ids =  $request?->zone ?? 0;
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'restaurant'])
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('restaurant', function ($query) use ($request) {
                    return $query->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('restaurant', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($key), function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })
            ->when($request?->from_date != null && $request?->to_date != null, function ($query) use ($request) {
                 return $query->whereBetween('created_at', [Helpers::to_gregorian($request->from_date), Helpers::to_gregorian($request->to_date)]);
            })

            ->Notpos()
            ->OrderScheduledIn(30)
            ->hasSubscriptionToday()
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = $request?->orderStatus ?? [];
        $scheduled =   $request?->scheduled ?? 0;
        $vendor_ids =  $request?->vendor ?? [];
        $zone_ids =   $request?->zone ?? [];
        $from_date =   $request?->from_date ?? null;
        $to_date =   $request?->to_date ?? null;
        $total = $orders->total();

        return view('admin-views.order.distaptch_list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'));
    }
 // ... The rest of the file remains the same
