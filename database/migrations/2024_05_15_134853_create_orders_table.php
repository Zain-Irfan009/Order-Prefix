<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shopify_id')->unsigned()->nullable();
            $table->string('email')->nullable();


            $table->string('contact_email')->nullable();
            $table->string('current_subtotal_price')->nullable();
            $table->longText('current_subtotal_price_set')->nullable();
            $table->longText('current_total_additional_fees_set')->nullable();
            $table->string('current_total_discounts')->nullable();
            $table->longText('current_total_discounts_set')->nullable();
            $table->longText('current_total_duties_set')->nullable();
            $table->string('current_total_price')->nullable();
            $table->longText('current_total_price_set')->nullable();
            $table->string('current_total_tax')->nullable();
            $table->longText('current_total_tax_set')->nullable();
            $table->string('customer_locale')->nullable();
            $table->string('device_id')->nullable();
            $table->longText('discount_codes')->nullable();
            $table->longText('estimated_taxes')->nullable();
            $table->longText('payment_gateway_names')->nullable();
            $table->longText('total_discounts_set')->nullable();
            $table->longText('shipping_address')->nullable();
            $table->longText('billing_address')->nullable();
            $table->longText('shipping_lines')->nullable();
            $table->longText('discount_applications')->nullable();
            $table->longText('line_items')->nullable();

            $table->longText('order_number')->nullable();
            $table->string('shipping_name')->nullable();
            $table->string('name')->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->longText('tags')->nullable();
            $table->longText('company')->nullable();
            $table->longText('customer')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->text('phone')->nullable();
            $table->string('country')->nullable();
            $table->text('province')->nullable();
            $table->text('financial_status')->nullable();
            $table->text('fulfillment_status')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->dateTime('shopify_created_at')->nullable();
            $table->dateTime('shopify_updated_at')->nullable();
            $table->string('total_price')->nullable();
            $table->longText('note')->nullable();
            $table->string('subtotal_price')->nullable();
            $table->string('total_weight')->nullable();
            $table->string('taxes_included')->nullable();
            $table->string('total_tax')->nullable();
            $table->string('currency')->nullable();
            $table->string('total_discounts')->nullable();
            $table->bigInteger('shop_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
