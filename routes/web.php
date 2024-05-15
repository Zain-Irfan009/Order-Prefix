<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CouponCampaignController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\widgetController;
use App\Models\Customer;
use App\Models\ErrorMessage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['verify.shopify']], function () {
    Route::get('/', [App\Http\Controllers\OrderController::class, 'allOrders'])->name('home');

    Route::any('/responses', [ResponseController::class, 'index'])->name('responses');
    Route::get('/customer_responses/{id}', [ResponseController::class, 'customer_responses'])->name('customer_responses');

});




//Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sync_products', [ProductController::class,'sync_products'])->name('sync-products');

Route::get('webhooks', function (Request $request) {

    return view('welcome');
    $shop = \App\Models\User::first();



    $webhooks = $shop->api()->rest('get', '/admin/api/2022-07/webhooks');
    dd($webhooks);
    return response()->json($webhooks);
})->name('webhook');
Route::get('/create/webhook', function () {
    $user = \App\Models\User::first();
    $data = [
        "webhook" => [
            "topic" => "customers/create",
            "address" => env("APP_URL") . "/api/webhooks/customers-create",
            "format" => "json",
        ]
    ];
    $response1 = $user->api()->rest('POST', '/admin/webhooks.json', $data, [], true);

    $data = [
        "webhook" => [
            "topic" => "customers/update",
            "address" => env("APP_URL") . "/api/webhooks/customers-update",
            "format" => "json",
        ]
    ];
    $response2 = $user->api()->rest('POST', '/admin/webhooks.json', $data, [], true);

    $data = [
        "webhook" => [
            "topic" => "customers/delete",
            "address" => env("APP_URL") . "/api/webhooks/customers-delete",
            "format" => "json",
        ]
    ];
    $response3 = $user->api()->rest('POST', '/admin/webhooks.json', $data, [], true);

    $data = [
        "webhook" => [
            "topic" => "products/delete",
            "address" => env("APP_URL") . "/api/webhooks/products-delete",
            "format" => "json",
        ]
    ];
    $response4 = $user->api()->rest('POST', '/admin/webhooks.json', $data, [], true);
    dd($response1, $response2, $response3, $response3);
});


