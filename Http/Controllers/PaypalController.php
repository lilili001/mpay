<?php

namespace Modules\Mpay\Http\Controllers;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Mpay\Entities\Order;

use Illuminate\Http\Request;
use Modules\Mpay\Entities\PayPalIPN;
use Modules\Mpay\PayPal;
use Modules\Mpay\Repositories\IPNRepository;
use Modules\Product\Entities\ShoppingCart;
use PayPal\Api\Amount;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\IPN\Event\IPNInvalid;
use PayPal\IPN\Event\IPNVerificationFailure;
use PayPal\IPN\Event\IPNVerified;
use PayPal\IPN\Listener\Http\ArrayListener;
use PayPal\Rest\ApiContext;
use AjaxResponse;
use Cart;
/**
 * Class PayPalController
 * @package App\Http\Controllers
 */
class PayPalController extends BasePublicController
{
    /**
     * @var IPNRepository
     */
    protected $repository;
    /**
     * @var ApiContext
     */
    protected $paypalApiContext;

    /**
     * PayPalController constructor.
     * @param IPNRepository $repository
     */
    public function __construct(IPNRepository $repository)
    {
        $this->repository = $repository;
        $this->paypalApiContext = new ApiContext(
            new OAuthTokenCredential(
                'ATP2EfifofYX1bFTKgFNOPbWky9sX74sr5REi_GOxjaY26J-ItcbqXa3AZmsm_SAtgmeMYOs9HnRDHml',
                'EK_0M5hIQmW2gYQ7k8zIfpVsLqLYNz-Kqdz-XwX4g0rMxNFjDBWUGzWZHBvJmQTMli8bDJFYrgxVer-8'
            )
        );
    }

    /**
     * @param Request $request
     */
    public function form(Request $request, $order_id = null)
    {
        $order_id = $order_id ? $order_id : encrypt(1);
        $order = Order::where([ 'order_id' => decrypt($order_id)] )->get()->first()  ;
        return view('mpay::form', compact('order'));
    }

    /**
     * @param $order_id
     * @param Request $request
     */
    public function checkout($order_id, Request $request)
    {
        $order = Order::where([ 'order_id' => decrypt($order_id)] )->get()->first() ;

        $paypal = new PayPal;

        $response = $paypal->purchase([
            'amount' => $paypal->formatAmount($order->amount),
            'transactionId' => $order->order_id,
            'currency' => 'USD',
            'cancelUrl' => $paypal->getCancelUrl($order),
            'returnUrl' => $paypal->getReturnUrl($order),
        ]);

        if ($response->isRedirect()) {
            $response->redirect();
        }

        return redirect()->back()->with([
            'message' => $response->getMessage(),
        ]);
    }

    /**
     * @param $order_id
     * @param Request $request
     * @return mixed
     */
    public function completed($order_id, Request $request)
    {
        $order = Order::where([ 'order_id' =>  ($order_id)] )->get()->first() ;
        $paypal = new PayPal;

        $response = $paypal->complete([
            'amount' => $paypal->formatAmount($order->amount),
            'transactionId' => $order->order_id,
            'currency' => 'USD',
            'cancelUrl' => $paypal->getCancelUrl($order),
            'returnUrl' => $paypal->getReturnUrl($order),
            'notifyUrl' => $paypal->getNotifyUrl($order),
        ]);

        if ($response->isSuccessful()) {
            $order->update([
                'transaction_id' => $response->getTransactionReference()
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
                    ] );
                }
            }

            return redirect()->route('app.home', encrypt($order_id))->with([
                'message' => 'You recent payment is sucessful with reference code ' . $response->getTransactionReference(),
            ]);
        }

        return redirect()->back()->with([
            'message' => $response->getMessage(),
        ]);
    }
    /**
     * @param $order_id
     */
    public function cancelled($order_id)
    {
        $order = Order::where([ 'order_id' =>  ($order_id)] )->get()->first() ;
        return redirect()->route('app.home', encrypt($order_id))->with([
            'message' => 'You have cancelled your recent PayPal payment !',
        ]);
    }

    /**
     * @param $order_id
     * @param $env
     */
    /**
     * @param $order_id
     * @param $env
     * @param Request $request
     */
    public function webhook($order_id, $env, Request $request)
    {
        $listener = new ArrayListener;
        if ($env == 'sandbox') {
            $listener->useSandbox();
        }
        $listener->setData($request->all());
        $listener = $listener->run();
        $listener->onInvalid(function (IPNInvalid $event) use ($order_id) {
            $this->repository->handle($event, PayPalIPN::IPN_INVALID, $order_id);
        });
        $listener->onVerified(function (IPNVerified $event) use ($order_id) {
            $this->repository->handle($event, PayPalIPN::IPN_VERIFIED, $order_id);
        });
        $listener->onVerificationFailure(function (IPNVerificationFailure $event) use ($order_id) {
            $this->repository->handle($event, PayPalIPN::IPN_FAILURE, $order_id);
        });
        $listener->listen();
    }

    /**
     * @param $transactionId
     * @return mixed
     * 可用于发货时 核对paypal付款信息 备用
     */
    public function sale_detail($transactionId)
    {
        try{
            $sale = Sale::get($transactionId,$this->paypalApiContext);
        }catch (Exception $e){
            return $e->getMessage() ;
        }
        return $sale;
    }
    
    /**
     * refund
     */
    public function refund($saleId)
    {
        $params = request('all');
        //refund amount
        $amt = new Amount();
        $amt->setCurrency($params['currency'])
            ->setTotal($params['amount']);

        //refund Object
        $refundRequest = new RefundRequest();
        $refundRequest->setAmount($amt);

        //sale
        $sale = new Sale();
        $sale->setId($saleId);
        try {
            $refundedSale = $sale->refundSale($refundRequest, $this->paypalApiContext);
        } catch (Exception $ex) {
           return AjaxResponse::fail('',$ex->getMessage());
        }
        return $refundedSale;
    }

   /*************************************helper funcs**************************************************/
    //如果没有session 就从数据库里取 并赋给session
    protected function compareSessionVsDb(){
        $dataFromDb = $this->getCartFromDb();
        if( !session()->has('cart.cart') && isset( $dataFromDb )   ){
            Cart::instance('cart')->add( $dataFromDb->toArray()  );
        }
    }
    //获取数据库cart对象
    protected function getCartFromDb(){
        if( ShoppingCart::count() == 0 ) return null;
        $cartInstance = ShoppingCart::where([
            'identifier' => user()->id,
            'instance'   => 'cart'
        ])->first()->content;
        $cartInstance = unserialize( $cartInstance );
        return $cartInstance;
    }
    //从当前cart session 中获取选中的总金额
    public function getSelectedTotal(){
        $total = 0;
        $instance = Cart::instance('cart')->content();
        foreach($instance as $key=>$item){
            if($item->options['selected']){
                $total += (float) ($item->price) * ($item->qty) ;
            }
        }
        return $total;
    }


}