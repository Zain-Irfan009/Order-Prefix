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
    Route::get('orders-filter', [App\Http\Controllers\OrderController::class, 'OrdersFilter'])->name('orders.filter');
    Route::get('sync-order', [App\Http\Controllers\OrderController::class, 'shopifyOrders'])->name('sync.orders');


    Route::get('companies', [App\Http\Controllers\CompanyController::class, 'Companies'])->name('companies');
    Route::post('add-company', [App\Http\Controllers\CompanyController::class, 'AddCompany'])->name('add.company');
    Route::post('edit-company/{id}', [App\Http\Controllers\CompanyController::class, 'EditCompany'])->name('edit.company');
    Route::get('delete-company/{id}', [App\Http\Controllers\CompanyController::class, 'DeleteCompany'])->name('delete.company');




    Route::get('settings', [App\Http\Controllers\SettingController::class, 'Settings'])->name('settings');
    Route::post('save-settings', [App\Http\Controllers\SettingController::class, 'SettingsSave'])->name('settings.save');



});




//Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sync_products', [ProductController::class,'sync_products'])->name('sync-products');

Route::get('draft-order', function (Request $request) {

    $line_items = [];


        array_push($line_items, [
            "variant_id" => 41067054366899,
            "quantity" => 1,
        ]);

    $shop = \App\Models\User::first();
    $draft_order = $shop->api()->rest('put', '/admin/api/2021-10/draft_orders/969117565107.json', [
        "draft_order" => [
            "line_items" => $line_items,
            "name" => '#WO-Test',
            "note" => 'Yay33yy',

        ]
    ]);
    dd($draft_order);
});
Route::get('webhooks', function (Request $request) {

    $shop = \App\Models\User::first();
    $delete = $shop->api()->rest('post', '/admin/orders/5379528425651/close.json');
dd($delete);
//    $orders = $shop->api()->rest('POST', '/admin/webhooks.json', [
//
//        "webhook" => array(
//            "topic" => "orders/create",
//            "format" => "json",
//            "address" => env('APP_URL').'/webhook/order-create'
//        )
//    ]);
//    dd($orders);





    $webhooks = $shop->api()->rest('get', '/admin/api/2022-07/webhooks');
    dd($webhooks);
    return response()->json($webhooks);
})->name('webhook');



Route::get('/create/webhook', function () {
    $user = \App\Models\User::first();
    $data = [
        "webhook" => [
            "topic" => "orders/create",
            "address" => env('APP_URL').'/webhook/order-create',
            "format" => "json",
        ]
    ];

    $response1 = $user->api()->rest('POST', '/admin/webhooks.json', $data, [], true);
dd($response1);

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


