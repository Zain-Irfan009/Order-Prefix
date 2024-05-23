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


        $orders=Order::orderBy('order_number','desc')->paginate(30);
        return view('orders.index',compact('orders'));
    }


    public function shopifyOrders($next = null){

        $shop=Auth::user();
        $orders = $shop->api()->rest('GET', '/admin/api/orders.json', [
            'limit' => 250,
            'page_info' => $next
        ]);
//dd($orders);
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
//dd(2);

        $setting=Setting::first();
        if($setting->update_prefix==1) {
            if ($order->financial_status != 'refunded' && $order->cancelled_at == null) {

                if ($order->company) {
                    $flag = 0;


                    $newOrder = Order::where('shopify_id', $order->id)->where('shop_id', $shop->id)->first();
                    if ($newOrder == null) {
                        $newOrder = new Order();
                        $flag = 1;
                    }

                    if ($flag == 1) {
//                $newOrder->b2b_order=1;
                        $newOrder->shopify_id = $order->id;
                        $newOrder->email = $order->email;
                        $newOrder->order_number = $order->name;
                        $newOrder->contact_email = $order->contact_email;
                        $newOrder->current_subtotal_price = $order->current_subtotal_price;
                        $newOrder->current_subtotal_price_set = json_encode($order->current_subtotal_price_set);
                        $newOrder->current_total_additional_fees_set = json_encode($order->current_total_additional_fees_set);
                        $newOrder->current_total_discounts = $order->current_total_discounts;
                        $newOrder->current_total_discounts_set = json_encode($order->current_total_discounts_set);
                        $newOrder->current_total_duties_set = json_encode($order->current_total_duties_set);
                        $newOrder->current_total_price = $order->current_total_price;
                        $newOrder->current_total_price_set = json_encode($order->current_total_price_set);
                        $newOrder->current_total_tax = $order->current_total_tax;
                        $newOrder->current_total_tax_set = json_encode($order->current_total_tax_set);
                        $newOrder->customer_locale = $order->customer_locale;
                        $newOrder->device_id = $order->device_id;
                        $newOrder->discount_codes = json_encode($order->discount_codes);
                        $newOrder->estimated_taxes = $order->estimated_taxes;
                        $newOrder->payment_gateway_names = json_encode($order->payment_gateway_names);
                        $newOrder->total_discounts_set = json_encode($order->total_discounts_set);
                        $newOrder->shipping_address = json_encode($order->shipping_address);
                        $newOrder->billing_address = json_encode($order->billing_address);
                        $newOrder->shipping_lines = json_encode($order->shipping_lines);
                        $newOrder->customer = json_encode($order->customer);
                        $newOrder->discount_applications = json_encode($order->discount_applications);
                        if ($order->company) {
                            $newOrder->company = json_encode($order->company);
                        }
                        $newOrder->line_items = json_encode($order->line_items);

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
                        $newOrder->name = $order->name;
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


                        $cancel = $shop->api()->rest('post', '/admin/orders/' . $order->id . '/cancel.json', [
                            'order' => [
                            ]
                        ]);
                        $delete = $shop->api()->rest('delete', '/admin/orders/' . $order->id . '.json');


                        $line_items = json_decode($newOrder->line_items);

//                    dd($line_items);
// Remove 'id' and 'admin_graphql_api_id' fields from each item in the array
                        foreach ($line_items as &$item) {

                            unset($item->id);
                            unset($item->admin_graphql_api_id);
                        }

                        $shipping_lines = json_decode($newOrder->shipping_lines);

// Remove 'id' and 'admin_graphql_api_id' fields from each item in the array
                        foreach ($shipping_lines as &$shipping_line) {
                            unset($shipping_line->id);
                            unset($shipping_line->carrier_identifier);
                        }


                        $discounts = json_decode($newOrder->discount_applications);
                        $discount_array = array();
                        foreach ($discounts as $discount) {

                            $data['code'] = $discount->code;
                            $data['amount'] = $discount->value;
                            $data['type'] = $discount->value_type;
                            array_push($discount_array, $data);
                        }

            $new_name= ltrim($newOrder->name, '#');
                        if (!empty($newOrder->tags)) {
                            $get = $shop->api()->rest('post', '/admin/orders.json', [
                                "order" => [
                                    "name" => '#WO-' . $new_name,
                                    "note" => $newOrder->note,
                                    "tags" => $newOrder->tags,
                                    "line_items" => $line_items,
                                    'total_weight' => $newOrder->total_weight,
                                    'company' => json_decode($newOrder->company),
                                    "customer" => json_decode($newOrder->customer),
                                    'shipping_address' => json_decode($newOrder->shipping_address),
                                    'billing_address' => json_decode($newOrder->billing_address),
                                    'shipping_lines' => $shipping_lines,
                                    'discount_codes' => $discount_array
                                ]
                            ]);
                        } else {
                            $get = $shop->api()->rest('post', '/admin/orders.json', [
                                "order" => [
                                    "name" => '#WO-' . $new_name,
                                    "note" => $newOrder->note,
                                    "line_items" => $line_items,
                                    'total_weight' => $newOrder->total_weight,
                                    'company' => json_decode($newOrder->company),
                                    "customer" => json_decode($newOrder->customer),
                                    'shipping_address' => json_decode($newOrder->shipping_address),
                                    'billing_address' => json_decode($newOrder->billing_address),
                                    'shipping_lines' => $shipping_lines,
                                    'discount_codes' => $discount_array
                                ]
                            ]);
                        }

                        if ($get['errors'] == false) {

                            $u_order = $get['body']['container']['order'];

                            $newOrder->shopify_id = $u_order['id'];
                            $newOrder->name = $u_order['name'];
                            $newOrder->save();

                        }
                    }
                }
//            }
            }
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
            $orders = $orders->where('name', 'like', '%' . $request->orders_filter . '%')->orWhere('shipping_name', 'like', '%' . $request->orders_filter . '%');
        }






        $orders=$orders->orderBy('id', 'DESC')->paginate(30);

        return view('orders.index',compact('orders','request','shop'));
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
