<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/4/28
 * Time: 16:24
 */

namespace Modules\Mpay\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Mpay\Entities\Order;
use Modules\Mpay\Repositories\OrderRepository;
use Modules\Product\Entities\ShoppingCart;

use Cart;

class OrderController extends BasePublicController
{
    protected $order;
    public function __construct(OrderRepository $order)
    {
        $this->order = $order;
    }

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
    public function save(Request $request)
    {

         $this->order->save( $request->all() );

       //根据payment_method 跳转不同的付款通道

//        if( $paymentMethod == 'alipay' ){
//            return redirect()->route('alipay.checkout',['order'=> encrypt($order->order_id) ] );
//        }else{
//            return redirect()->route('checkout.payment.paypal',['order'=> encrypt($order->order_id)]);
//        }
    }
}