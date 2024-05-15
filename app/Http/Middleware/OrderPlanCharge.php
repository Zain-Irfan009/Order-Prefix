<?php

namespace App\Http\Middleware;


use App\Http\Controllers\AdminController;
use App\Models\BillingStatus;
use App\Models\Order;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Osiset\ShopifyApp\Storage\Models\Charge;
use Osiset\ShopifyApp\Storage\Models\Plan;

class OrderPlanCharge
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (isset($user) && isset($user->plan_id)) {

            $response = $user->api()->rest('get', '/admin/api/2022-04/recurring_application_charges.json');
            $all_recurring_charges = $response;

            if($all_recurring_charges['errors'] == false){
                $all_recurring_charges = $all_recurring_charges['body']['recurring_application_charges']['container'];
                if(isset($all_recurring_charges[0]) && (strtoupper($all_recurring_charges[0]['status']) == 'ACTIVE') ){

                }else{
                    //                    first status is not active create update the charge table
                    $this->CreateUpdateChargeTable($user);
                }
//            }

                $charge = Charge::where('user_id', $user->id)->where('plan_id', $user->plan_id)->OrderBy('created_at', 'desc')->first();
                if (isset($charge)) {
                    if((strtoupper($charge->status) == "ACTIVE")?true:false){
                        if (isset($charge->billing_on)) {
                            $now = time(); // or your date as well
                            $your_date = strtotime($charge->billing_on);
                            $date_diff = $now - $your_date;
                            $billing_days_diff = round($date_diff / (60 * 60 * 24));

                            if ($billing_days_diff > 30) {
                                $this->CreateUpdateChargeTable($user);
//                                $this->checkUpsellStatus($charge,$user);
                            }else{
                                $this->billing_status($user,1);
//                                $this->checkUpsellStatus($charge,$user);
                            }
                        }else{
                            $this->billing_status($user,0);
//                            $this->checkUpsellStatus($charge,$user);
                        }
                    }else{
//                        $this->checkUpsellStatus($charge,$user);
                    }
                }else{
                    $this->billing_status($user,0);
                }
            }else{
                $this->billing_status($user,0);
            }

        }else{
            $this->billing_status($user,0);
        }

        $billing_status = BillingStatus::where('user_id',$user->id)->first();
        if(isset($billing_status) && $billing_status->billing_status == 0){
            $user->plan_id = null;
            $user->save();
        }
//        }
        $widget_controller = new AdminController();
        $widget_controller->createUpdateMetaFields($user);

        return $next($request);

    }

    public function checkUpsellStatus(Charge $charge,User $user){

        $upsell_order_sales = Order::where('user_id',$user->id)->sum('total_price');
        $plan = Plan::find($user->plan_id);

        if($upsell_order_sales <= 1000) {
            if($plan->id == 1  || $plan->id == 3 || $plan->id == 4){
                $this->billing_status($user,1);
            }else{
                $this->billing_status($user,0);
            }
        }elseif($upsell_order_sales <= 2000){
            if($plan->id == 3 || $plan->id == 4){
                $this->billing_status($user,1);
            }else{
                $this->billing_status($user,0);
            }
        }elseif($upsell_order_sales >= 5000){
            if($plan->id == 4){
                $this->billing_status($user,1);
            }else{
                $this->billing_status($user,0);
            }
        }else{
            $this->billing_status($user,0);
        }
    }

    public function billing_status(User $user,$status_id){
        $billing_status = BillingStatus::where('user_id',$user->id)->first();
        if($billing_status == null){
            $billing_status = new BillingStatus();
        }
        $billing_status->user_id = $user->id;
        $billing_status->billing_status = $status_id;
        $billing_status->save();
    }

    public function CreateUpdateChargeTable($user){
        $response = $user->api()->rest('get', '/admin/api/2022-04/recurring_application_charges.json');

        if ($response['errors'] == false) {

            $all_recurring_charges = $response['body']['recurring_application_charges']['container'];
//            we reverse the charges response, because last record of the charge will be the lastest status of the plan charge.
            $all_recurring_charges = array_reverse($all_recurring_charges);

            foreach ($all_recurring_charges as $all_recurring_charge) {
//                $plan = Plan::where('name',$all_recurring_charge->name)->first();
                $all_recurring_charge = json_decode(json_encode($all_recurring_charge),false);
                $plan = Plan::first();
                $charge = Charge::where('user_id', $user->id)->where('charge_id', $all_recurring_charge->id)->first();
                if ($charge == null) {
                    $charge = new Charge();
                }
                $charge->user_id = $user->id;
                $charge->charge_id = $all_recurring_charge->id;
                $charge->plan_id = isset($plan->id)?$plan->id:1;
                $charge->terms = isset($plan->terms)?$plan->terms:null;
                $charge->type = isset($plan->type)?$plan->type:'RECURRING';
                $charge->price = $all_recurring_charge->price;
                $charge->status = strtoupper($all_recurring_charge->status);
                $charge->name = $all_recurring_charge->name;
                $charge->billing_on = $all_recurring_charge->billing_on;
                $charge->created_at = $all_recurring_charge->created_at;
                $charge->updated_at = $all_recurring_charge->updated_at;
                $charge->activated_on = $all_recurring_charge->activated_on;
                $charge->cancelled_on = $all_recurring_charge->cancelled_on;
                $charge->trial_days = $all_recurring_charge->trial_days;
                $charge->capped_amount = isset($all_recurring_charge->capped_amount)?$all_recurring_charge->capped_amount:0;
                $charge->trial_ends_on = $all_recurring_charge->trial_ends_on;
                $charge->test = (isset($all_recurring_charge->test) && $all_recurring_charge->test == true) ? 1 : 0;
                $charge->save();
            }
            $active_charge = Charge::where('user_id',$user->id)->where('plan_id',$user->plan_id)->OrderBy('created_at', 'desc')->first();

            if((isset($active_charge) && strtoupper($active_charge->status) == "ACTIVE")?true:false){ #if plan is active
                $this->billing_status($user,1);
            }else{
                $this->billing_status($user,0);
            }

        }else{
            $this->billing_status($user,0);
        }
    }
}
