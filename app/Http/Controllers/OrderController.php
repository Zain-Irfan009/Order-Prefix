<?php

namespace App\Http\Controllers;

use App\Models\Lineitem;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{

    public function allOrders(){

        $total_orders=Order::count();
        $pushed_orders=Order::where('status',1)->count();
        $pending_orders=Order::where('tryvengo_status','Pending')->count();
        $delivered_orders=Order::where('tryvengo_status','Delivered')->count();

        $orders=Order::orderBy('order_number','desc')->paginate(30);
        return view('orders.index',compact('orders','total_orders','pushed_orders','pending_orders','delivered_orders'));
    }


    public function shopifyOrders($next = null){

        $shop=Auth::user();
        $orders = $shop->api()->rest('GET', '/admin/api/orders.json', [
            'limit' => 250,
            'page_info' => $next
        ]);
        if ($orders['errors'] == false) {
            if (count($orders['body']->container['orders']) > 0) {
                foreach ($orders['body']->container['orders'] as $order) {
                    $order = json_decode(json_encode($order));
                    $this->singleOrder($order,$shop);
                }
            }
            if (isset($orders['link']['next'])) {

                $this->shopifyOrders($orders['link']['next']);
            }
        }
        return Redirect::tokenRedirect('home', ['notice' => 'Orders Sync Successfully']);


    }
    public function singleOrder($order, $shop)
    {

        if($order->financial_status!='refunded' && $order->cancelled_at==null  ) {

                $newOrder = Order::where('shopify_id', $order->id)->where('shop_id', $shop->id)->first();
                if ($newOrder == null) {
                    $newOrder = new Order();
                }
                $newOrder->shopify_id = $order->id;
                $newOrder->email = $order->email;
                $newOrder->order_number = $order->name;
                $newOrder->contact_email = $order->contact_email;
                $newOrder->current_subtotal_price = $order->current_subtotal_price;
                $newOrder->current_subtotal_price_set = $order->current_subtotal_price_set;
                $newOrder->current_total_additional_fees_set = $order->current_total_additional_fees_set;
                $newOrder->current_total_discounts = $order->current_total_discounts;
                $newOrder->current_total_discounts_set = $order->current_total_discounts_set;
                $newOrder->current_total_duties_set = $order->current_total_duties_set;
                $newOrder->current_total_price = $order->current_total_price;
                $newOrder->current_total_price_set = $order->current_total_price_set;
                $newOrder->current_total_tax = $order->current_total_tax;
                $newOrder->current_total_tax_set = $order->current_total_tax_set;
                $newOrder->customer_locale = $order->customer_locale;
                $newOrder->device_id = $order->device_id;
                $newOrder->discount_codes = $order->discount_codes;
                $newOrder->estimated_taxes = $order->estimated_taxes;
                $newOrder->payment_gateway_names = $order->payment_gateway_names;
                $newOrder->total_discounts_set = $order->total_discounts_set;
                $newOrder->shipping_address = $order->shipping_address;
                $newOrder->shipping_lines = $order->shipping_lines;
                $newOrder->discount_applications = $order->discount_applications;
                $newOrder->company=json_encode($order->company);

                if (isset($order->shipping_address)) {
                    $newOrder->shipping_name = $order->shipping_address->name;
                    $newOrder->address1 = $order->shipping_address->address1;
                    $newOrder->address2 = $order->shipping_address->address2;
                    $newOrder->phone = $order->shipping_address->phone;
                    $newOrder->city = $order->shipping_address->city;
                    $newOrder->zip = $order->shipping_address->zip;
                    $newOrder->province = $order->shipping_address->province;
                    $newOrder->country = $order->shipping_address->country;
                }
                $newOrder->financial_status = $order->financial_status;
                $newOrder->fulfillment_status = $order->fulfillment_status;
                if (isset($order->customer)) {
                    $newOrder->first_name = $order->customer->first_name;
                    $newOrder->last_name = $order->customer->last_name;
                    $newOrder->customer_phone = $order->customer->phone;
                    $newOrder->customer_email = $order->customer->email;
                    $newOrder->customer_id = $order->customer->id;
                }
                $newOrder->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
                $newOrder->shopify_updated_at = date_create($order->updated_at)->format('Y-m-d h:i:s');
                $newOrder->tags = $order->tags;
                $newOrder->note = $order->note;
                $newOrder->total_price = $order->total_price;
                $newOrder->currency = $order->currency;

                $newOrder->subtotal_price = $order->subtotal_price;
                $newOrder->total_weight = $order->total_weight;
                $newOrder->taxes_included = $order->taxes_included;
                $newOrder->total_tax = $order->total_tax;
                $newOrder->currency = $order->currency;
                $newOrder->total_discounts = $order->total_discounts;
                $newOrder->shop_id = $shop->id;
                $newOrder->save();
                foreach ($order->line_items as $item) {
                    $new_line = Lineitem::where('shopify_id', $item->id)->where('order_id', $newOrder->id)->where('shop_id', $shop->id)->first();
                    if ($new_line == null) {
                        $new_line = new Lineitem();
                    }
                    $new_line->shopify_id = $item->id;
                    $new_line->shopify_product_id = $item->product_id;
                    $new_line->shopify_variant_id = $item->variant_id;
                    $new_line->title = $item->title;
                    $new_line->quantity = $item->quantity;
                    $new_line->sku = $item->sku;
                    $new_line->variant_title = $item->variant_title;
                    $new_line->title = $item->title;
                    $new_line->vendor = $item->vendor;
                    $new_line->price = $item->price;
                    $new_line->requires_shipping = $item->requires_shipping;
                    $new_line->taxable = $item->taxable;
                    $new_line->name = $item->name;
                    $new_line->properties = json_encode($item->properties, true);
                    $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                    $new_line->fulfillment_status = $item->fulfillment_status;
                    $new_line->order_id = $newOrder->id;
                    $new_line->shop_id = $shop->id;
                    $new_line->shopify_order_id = $order->id;
                    $new_line->save();
                }

                $this->fulfillmentOrders($newOrder);










        }
    }
    public function updateOrder($order, $shop)
    {

        if($order->financial_status!='refunded' && $order->cancelled_at==null  ) {
            if ($order->cart_token) {
                $newOrder = Order::where('shopify_id', $order->id)->where('shop_id', $shop->id)->first();
                if ($newOrder == null) {
                    $newOrder = new Order();
                }
                $newOrder->shopify_id = $order->id;
                $newOrder->email = $order->email;
                $newOrder->order_number = $order->name;
                $newOrder->company=json_encode($order->company);

                if (isset($order->shipping_address)) {
                    $newOrder->shipping_name = $order->shipping_address->name;
                    $newOrder->address1 = $order->shipping_address->address1;
                    $newOrder->address2 = $order->shipping_address->address2;
                    $newOrder->phone = $order->shipping_address->phone;
                    $newOrder->city = $order->shipping_address->city;
                    $newOrder->zip = $order->shipping_address->zip;
                    $newOrder->province = $order->shipping_address->province;
                    $newOrder->country = $order->shipping_address->country;
                }
                $newOrder->financial_status = $order->financial_status;
                $newOrder->fulfillment_status = $order->fulfillment_status;
                if (isset($order->customer)) {
                    $newOrder->first_name = $order->customer->first_name;
                    $newOrder->last_name = $order->customer->last_name;
                    $newOrder->customer_phone = $order->customer->phone;
                    $newOrder->customer_email = $order->customer->email;
                    $newOrder->customer_id = $order->customer->id;
                }
                $newOrder->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
                $newOrder->shopify_updated_at = date_create($order->updated_at)->format('Y-m-d h:i:s');
                $newOrder->tags = $order->tags;
                $newOrder->note = $order->note;
                $newOrder->total_price = $order->total_price;
                $newOrder->currency = $order->currency;

                $newOrder->subtotal_price = $order->subtotal_price;
                $newOrder->total_weight = $order->total_weight;
                $newOrder->taxes_included = $order->taxes_included;
                $newOrder->total_tax = $order->total_tax;
                $newOrder->currency = $order->currency;
                $newOrder->total_discounts = $order->total_discounts;
                $newOrder->shop_id = $shop->id;
                $newOrder->save();
                foreach ($order->line_items as $item) {
                    $new_line = Lineitem::where('shopify_id', $item->id)->where('order_id', $newOrder->id)->where('shop_id', $shop->id)->first();
                    if ($new_line == null) {
                        $new_line = new Lineitem();
                    }
                    $new_line->shopify_id = $item->id;
                    $new_line->shopify_product_id = $item->product_id;
                    $new_line->shopify_variant_id = $item->variant_id;
                    $new_line->title = $item->title;
                    $new_line->quantity = $item->quantity;
                    $new_line->sku = $item->sku;
                    $new_line->variant_title = $item->variant_title;
                    $new_line->title = $item->title;
                    $new_line->vendor = $item->vendor;
                    $new_line->price = $item->price;
                    $new_line->requires_shipping = $item->requires_shipping;
                    $new_line->taxable = $item->taxable;
                    $new_line->name = $item->name;
                    $new_line->properties = json_encode($item->properties, true);
                    $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                    $new_line->fulfillment_status = $item->fulfillment_status;
                    $new_line->order_id = $newOrder->id;
                    $new_line->shop_id = $shop->id;
                    $new_line->shopify_order_id = $order->id;
                    $new_line->save();
                }


            }

            $this->fulfillmentOrders($newOrder);
        }
    }





    public function OrdersFilter(Request $request){


        $shop=Auth::user();
        $orders=Order::query();

        if($request->orders_filter!=null) {
            $orders = $orders->where('order_number', 'like', '%' . $request->orders_filter . '%')->orWhere('shipping_name', 'like', '%' . $request->orders_filter . '%');
        }

        if($request->tryvengo_status!=null) {
            $orders = $orders->where('tryvengo_status', $request->tryvengo_status );
        }

        if($request->order_status!=null) {
            $orders = $orders->where('status', $request->order_status );
        }

        if ($request->date_filter != null) {
            $orders = $orders->whereDate('created_at', $request->date_filter);
        }


        $order_ids=$orders->pluck('id')->toArray();



        $total_orders=Order::whereIn('id',$order_ids)->where('shop_id',$shop->id)->count();
        $pushed_orders=Order::whereIn('id',$order_ids)->where('status',1)->count();
        $pending_orders=Order::whereIn('id',$order_ids)->where('tryvengo_status','Pending')->count();
        $delivered_orders=Order::whereIn('id',$order_ids)->where('tryvengo_status','Delivered')->count();



        $orders=$orders->orderBy('id', 'DESC')->paginate(30);

        return view('orders.index',compact('orders','request','shop','total_orders','pushed_orders','pending_orders','delivered_orders'));
    }




    public function fulfillmentOrders($order){
        $shop = User::where('name', env('SHOP_NAME'))->first();
        $get_fulfillment_orders= $shop->api()->rest('get', '/admin/api/2023-01/orders/' . $order->shopify_id . '/fulfillment_orders.json');

        if ($get_fulfillment_orders['errors'] == false) {
            $get_fulfillment_orders = json_decode(json_encode($get_fulfillment_orders));
            foreach ($get_fulfillment_orders->body->fulfillment_orders as $fulfillment) {
                $order->shopify_fulfillment_order_id = $fulfillment->id;
                $order->save();

                foreach ($fulfillment->line_items as $line_item) {
                    $db_line_item = LineItem::where('shopify_id', $line_item->line_item_id)->first();
                    if (isset($db_line_item)) {
                        $db_line_item->shopify_fulfillment_order_id = $line_item->id;
                        $db_line_item->shopify_fulfillment_real_order_id = $line_item->fulfillment_order_id;
                        $db_line_item->assigned_location_id = $fulfillment->assigned_location_id;
                        $db_line_item->save();
                    }
                }
            }
        }
    }
}
