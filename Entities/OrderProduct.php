<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/5/23
 * Time: 14:45
 */

namespace Modules\Mpay\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Sale\Entities\OrderRefund;
use Modules\Sale\Entities\OrderReturn;

class OrderProduct extends Model
{
    protected $table = 'order_item';
    protected $guarded = [];

    //订单产品退款
    public function refund()
    {
        return $this->hasOne(OrderRefund::class,'item_id','id');
    }

    //订单产品退货
    public function goods_return()
    {
        return $this->hasOne(OrderReturn::class,'goods_id','id');
    }
}