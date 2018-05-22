<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpayPayPalIPNsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_paypal_ipn_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable();
            $table->string('verified');
            $table->string('transaction_id');
            $table->string('payment_status');
            $table->string('request_method')->nullable();
            $table->string('request_url')->nullable();
            $table->longText('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_alipay_ipn_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable();
            $table->string('trade_no');
            $table->text('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_paypal_ipn_records');
        Schema::dropIfExists('order_alipay_ipn_records');
    }
}
