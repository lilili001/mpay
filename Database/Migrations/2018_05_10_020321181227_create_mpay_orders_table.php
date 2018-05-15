<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpayOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //订单基本表
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('order_id')-uniqid()->primary();
            $table->string('order_status',20);
            $table->decimal('amount',18);
            $table->integer('user_id');
            $table->string('transaction_id');
            $table->string('language');
            $table->string('payer_id');
            $table->timestamp('payment_time')->default(null);
            $table->timestamp('consign_time')->default(null); //交易完成时间
            $table->timestamp('end_time')->default(null); //交易关闭成时间
            $table->text('buyer_message');
            $table->string('buyer_name');
            $table->boolean('buyer_remark');//买家是否已评价
            $table->string('order_locale');
            $table->string('order_currency');
            $table->timestamps();
            $table->softDeletes();
        });
        //订单产品信息表
        Schema::create('order_item',function(Blueprint $table){
            $table->integer('item_id');
            $table->string('order_id');
            $table->integer('num');
            $table->string('title');
            $table->decimal('price');
            $table->string('pic_path');
        });

        //订单收货信息表
        Schema::create('order_address',function(Blueprint $table){
            $table->string('order_id');
            $table->string('name');
            $table->string('telephone');
            $table->string('mobile');
            $table->string('country');
            $table->string('province');
            $table->string('city');
            $table->string('street');
            $table->string('zipcode');
            $table->string('created_at');
            $table->string('updated_at');
        });

        //订单操作记录表 待付款 已付款 等待发货 已发货 已签收 交易成功 交易关闭 退货 退款中 退款成功
        Schema::create('order_operation',function(Blueprint $table){
            $table->string('order_id');

            $table->string('order_status');
            $table->string('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mpay__orders');
    }
}
