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

use Cart;

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
    public function save($paymentMethod)
    {
       $order =  Order::create([
            'order_id' => $this->StrOrderOne(),
            'amount'   => $this->getSelectedAmount(),
            //'payment_gateway' => $paymentMethod,
            'user_id'   => user()->id
        ]);

        //清除购物车
        $this->compareSessionVsDb();
        foreach (Cart::instance('cart')->content() as $key => $item) {
            if ($item->options['userId'] == user()->id  &&  !!($item->options['selected']) ) {
                //删除
                Cart::instance('cart')->remove($item->rawId);
                //删除数据库购物车
                ShoppingCart::where([
                    'identifier' => user()->id,
                    'instance'   => 'cart'
                ])->update( [
                    'content' => serialize( Cart::instance('cart')->content() ),
                    'selected_total' => $this->getSelectedTotal()
                ]);
            }
        }

       //根据payment_method 跳转不同的付款通道

        if( $paymentMethod == 'alipay' ){
            return redirect()->route('alipay.checkout',['order'=> encrypt($order->order_id) ] );
        }else{
            return redirect()->route('checkout.payment.paypal',['order'=> encrypt($order->order_id)]);
        }
    }
}