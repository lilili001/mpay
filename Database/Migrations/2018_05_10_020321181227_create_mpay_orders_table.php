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
            $table->boolean('is_ordered_with_supplier')->default(0);//是否已和供应商订货
            $table->decimal('amount',18);
            $table->decimal('amount_current_currency',18);
            $table->string('currency');
            $table->integer('user_id');
            $table->string('payment_gateway');
            $table->string('language');
            $table->string('payer_id');
            $table->timestamp('payment_time')->default(null);
            $table->timestamp('consign_time')->default(null); //交易收货时间
            $table->timestamp('end_time')->default(null); //交易关闭成时间
            $table->timestamp('order_with_supplier_at')->default(null);//和供应商订货时间
            $table->text('buyer_message');
            $table->string('buyer_name');
            $table->boolean('buyer_remark');//买家是否已评价
            $table->string('order_locale');
            $table->string('order_currency');
            $table->boolean('is_paid')->default(0); //是否付款
            $table->boolean('is_shipped')->default(0); //是否发货
            $table->timestamps();
            $table->softDeletes();

            $table->primary('order_id');
        });

        //订单供应商信息表
        Schema::create('order_supplier',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id');
            $table->integer('item_id');
            $table->string('supplier');
            $table->string('supplier_item_id');
            $table->string('supplier_item_slug');
            $table->decimal('supplier_unit_price');
            $table->decimal('supplier_subtotal');
            $table->timestamps();
        });

        //发货单
        Schema::create('order_shipping',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id');
            $table->string('delivery');//发货方式
            $table->string('tracking_number');//追踪单号
            $table->string('invoice_number');//发货单号
            $table->timestamps();
        });

        //订单产品信息表
        Schema::create('order_item',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id');
            $table->integer('item_id');
            $table->integer('quantity');
            $table->string('title');
            $table->text('options');
            $table->decimal('unit_price',18);
            $table->decimal('unit_price_current_currency',18);
            $table->decimal('subtotal');
            $table->decimal('subtotal_current_currency',18);
            $table->string('pic_path');
            $table->string('slug');
            $table->timestamps();
        });

        //订单收货信息表
        Schema::create('order_address',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id');
            $table->string('name');
            $table->string('telephone');
            $table->string('mobile');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('street');
            $table->string('zipcode');
            $table->timestamps();
        });

        //订单操作记录表 待付款 已付款 等待发货 已发货 已签收 交易成功 交易关闭 退货 退款中 退款成功
        Schema::create('order_operation',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id') ;
            $table->string('order_status');
            $table->string('order_status_label');
            $table->timestamps();
        });

        //订单退款记录表
        Schema::create('order_refund',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id')->unique() ;
            $table->string('payerId');
            $table->string('is_order_shipped'); //是否已发货
            $table->string('is_order_received'); //是否已收到货
            $table->string('need_return_goods'); //是否退货
            $table->decimal('amount');//退款金额
            $table->string('approve_status')->default(0);//是否审批
            $table->string('refund_status');//退款状态 0 1
            $table->integer('user_id');
            $table->timestamps();
        });

        //订单退货记录表 买家填写退货物流后更新该表
        Schema::create('order_return',function(Blueprint $table){
            $table->increments('id');
            $table->string('order_id')->unique();
            $table->integer('user_id');
            $table->string('name');
            $table->string('telephone');
            $table->string('mobile');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('street');
            $table->string('zipcode');

            $table->timestamp('pickup_time');//收货时间
            $table->string('approve_status')->default(0);
            $table->string('return_status');// 0 1
            $table->timestamps();
        });

        Schema::create('order_return_items',function(Blueprint $table){
            $table->increments('id');
            $table->integer('return_id');
            $table->string('order_id');
            $table->integer('item_id');
            $table->integer('quantity');
            $table->text('options');
        });

        Schema::create('comments',function(Blueprint $table){
            $table->increments('id');
            $table->text('body'); // 评论或咨询或留言
            $table->text('img_url');//留言的图片
            $table->integer('user_id');//用户id
            $table->integer('pid');
            $table->integer('commentable_id');//对应模型id
            $table->string('commentable_type');//对应模型
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
        Schema::dropIfExists('orders'); // 下单时创建
        Schema::dropIfExists('order_supplier'); //订单供应商
        Schema::dropIfExists('order_shipping'); //发货单
        Schema::dropIfExists('order_item'); //订单产品
        Schema::dropIfExists('order_address');//订单收货地址
        Schema::dropIfExists('order_operation');//订单状态记录表
        Schema::dropIfExists('order_refund');//退款记录表
        Schema::dropIfExists('order_return');//退货记录表
        Schema::dropIfExists('order_return_items');//退货的产品
        Schema::dropIfExists('comments');//评论咨询或退款退货留言
    }
}
