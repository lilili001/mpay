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
use AjaxResponse;
class OrderController extends BasePublicController
{
    protected $order;
    public function __construct(OrderRepository $order)
    {
        $this->order = $order;
    }

    public function save(Request $request)
    {
         $orderId = $this->order->save( $request->all() );
         $paymentMethod = request('order_payment_method');

         $url = null;
         switch ($paymentMethod){
             case 'alipay':
                 $url = '/alipay/checkout/'.encrypt($orderId);
                 break;
             case 'paypal':
                 $url = '/paypal/checkout/'.encrypt($orderId) ;
         }

         if( $orderId != false ){
            return AjaxResponse::success('' , $url );
         }else{
             return AjaxResponse::fail('');
         }
       //根据payment_method 跳转不同的付款通道

//        if( $paymentMethod == 'alipay' ){
//            return redirect()->route('alipay.checkout',['order'=> encrypt($order->order_id) ] );
//        }else{
//            return redirect()->route('checkout.payment.paypal',['order'=> encrypt($order->order_id)]);
//        }
    }
}