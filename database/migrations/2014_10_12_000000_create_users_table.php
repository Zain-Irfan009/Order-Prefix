<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('app_status')->default(1)->nullable();
            $table->bigInteger('script_tag_id')->nullable();
            $table->bigInteger('meta_field_id')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('product_count')->nullable();
            $table->string('widget_status')->nullable()->default(0);
            $table->text('currency')->nullable();
            $table->text('origin')->nullable();
            $table->text('currency_code')->nullable();
            $table->text('contact_email' )->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
