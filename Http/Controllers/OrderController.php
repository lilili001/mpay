<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/4/28
 * Time: 16:24
 */

namespace Modules\Mpay\Http\Controllers;

use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Mpay\Entities\Order;
use Modules\Product\Entities\ShoppingCart;
use Omnipay\Omnipay;

class OrderController extends BasePublicController
{

    protected  function StrOrderOne(){
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    protected function getSelectedAmount(){
        return ShoppingCart::where([
            'identifier' => user()->id,
            'instance' => 'cart'
        ])->first()->selected_total;
    }
    public function save()
    {
       $order =  Order::create([
            'order_id' => $this->StrOrderOne(),
            'amount'   => $this->getSelectedAmount()
        ]);
        return redirect()->route('app.home',['order'=> encrypt($order->id)]);
    }
}