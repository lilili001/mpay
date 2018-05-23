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
            $table->string('order_id')->uniqid();
            $table->string('transaction_id')->nullable();
            $table->string('order_status',20);
            $table->decimal('amount',18);
            $table->decimal('amount_current_currency',18);
            $table->string('currency');
            $table->integer('user_id');
            $table->string('payment_gateway');
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

            $table->primary('order_id');
        });

        //订单供应商信息表
        Schema::create('order_supplier',function(Blueprint $table){
            $table->string('order_id')->unique();
            $table->integer('item_id');
            $table->string('supplier');
            $table->string('supplier_item_id');
            $table->string('supplier_item_slug');
            $table->decimal('supplier_unit_price');
            $table->decimal('supplier_subtotal');
            $table->timestamps();
            $table->primary('order_id');
        });

        //发货单
        Schema::create('order_shipping',function(Blueprint $table){
            $table->string('order_id')->unique();
            $table->string('delivery');//发货方式
            $table->string('invoice_number');//发货单号
            $table->timestamps();
            $table->primary('order_id');
        });

        //订单产品信息表
        Schema::create('order_item',function(Blueprint $table){
            $table->string('order_id')->unique();
            $table->integer('item_id');
            $table->integer('quantity');
            $table->string('title');
            $table->text('options');
            $table->decimal('unit_price',18);
            $table->decimal('unit_price_current_currency',18);
            $table->decimal('subtotal');
            $table->string('pic_path');
            $table->string('slug');
            $table->timestamps();
            $table->primary('order_id');
        });

        //订单收货信息表
        Schema::create('order_address',function(Blueprint $table){
            $table->string('order_id')->unique();
            $table->string('name');
            $table->string('telephone');
            $table->string('mobile');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('street');
            $table->string('zipcode');
            $table->timestamps();
            $table->primary('order_id');
        });

        //订单操作记录表 待付款 已付款 等待发货 已发货 已签收 交易成功 交易关闭 退货 退款中 退款成功
        Schema::create('order_operation',function(Blueprint $table){
            $table->string('order_id')->unique();
            $table->string('order_status');
            $table->string('order_status_label');
            $table->timestamps();
            $table->primary('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders'); // 下单时创建
        Schema::dropIfExists('order_supplier'); //下单时创建
        Schema::dropIfExists('order_shipping'); //发货时
        Schema::dropIfExists('order_item'); //下单时创建
        Schema::dropIfExists('order_address');//下单时创建
        Schema::dropIfExists('order_operation');//下单时 更新订单时
    }
}
