<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\widgetController;
use App\Models\Customer;
use App\Models\ErrorMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::any('/submit-quiz', [\App\Http\Controllers\ResponseController::class, 'submit_quiz'])->name('submit-quiz');
Route::any('/test-submit-quiz', [\App\Http\Controllers\ResponseController::class, 'test_submit_quiz'])->name('test-submit-quiz');

Route::post('/webhooks/customers-create', function (Request $request) {
    try {

//        $logs = new ErrorMessage();
//        $logs->message = 'customer create res: '.json_encode($request->getContent());
//        $logs->save();
        $customer = $request->getContent();

        $shop = $request->header('x-shopify-shop-domain');
        $shop = User::where('name', $shop)->first();
        $customer = json_decode($customer);
        $sync_controller = new SyncController();
        $sync_controller->createUpdateCustomer($customer,$shop);

    } catch (\Exception $e) {

        $error_log = new ErrorMessage();
        $error_log->message = 'customer Create catch: '.($e->getMessage());
        $error_log->save();
    }
});

Route::post('/webhooks/customers-update', function (Request $request) {
    try {

//        $logs = new ErrorMessage();
//        $logs->message = 'customer update res: '.json_encode($request->getContent());
//        $logs->save();
        $data = $request->getContent();

        $shop = $request->header('x-shopify-shop-domain');
        $shop = User::where('name', $shop)->first();
        $data = json_decode($data);
        $sync_controller = new SyncController();
        $sync_controller->createUpdateCustomer($data,$shop);

    } catch (\Exception $e) {

//        $error_log = new ErrorMessage();
//        $error_log->message = 'customer update catch' . json_encode($e->getMessage());
//        $error_log->save();
    }
});

Route::post('/webhooks/customers-delete', function (Request $request) {
    try {

//        $logs = new ErrorMessage();
//        $logs->message = 'customer delete res: '.json_encode($request->getContent());
//        $logs->save();
        $data = $request->getContent();

        $shop = $request->header('x-shopify-shop-domain');
        $shop = User::where('name', $shop)->first();
        $data = json_decode($data);

        $db_customer = Customer::where('shopify_customer_id',$data->id)->first();
        if(isset($db_customer)){
            DB::table('customers')->where('id',$db_customer->id)->delete();
        }

    } catch (\Exception $e) {

//        $error_log = new ErrorMessage();
//        $error_log->message = 'customer Create catch' . json_encode($e->getMessage());
//        $error_log->save();
    }
});

Route::post('/webhooks/products-delete', function (Request $request) {
    try {

//        $logs = new ErrorMessage();
//        $logs->message = 'product delete res: '.json_encode($request->getContent());
//        $logs->save();
        $data = $request->getContent();

        $shop = $request->header('x-shopify-shop-domain');
        $shop = User::where('name', $shop)->first();
        $data = json_decode($data);

        $product_controller = new ProductController();
        $product_controller->ProductDelete($data, $shop->name);

    } catch (\Exception $e) {

//        $error_log = new ErrorMessage();
//        $error_log->message = 'customer Create catch' . json_encode($e->getMessage());
//        $error_log->save();
    }
});
