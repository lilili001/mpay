<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/4/28
 * Time: 16:24
 */

namespace Modules\Mpay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Core\Http\Controllers\BasePublicController;
use Omnipay\Omnipay;

/**
 * Class AlipayController
 * @package Modules\Mpay\Http\Controllers
 */
class AlipayController extends BasePublicController
{
    /**
     * @var \Omnipay\Common\GatewayInterface
     */
    protected $gateway;

    /**
     * AlipayController constructor.
     */
    public function __construct()
    {
        $gateway = Omnipay::create('Alipay_AopPage');
        $this->gateway = $gateway;
        $this->gateway->sandbox();
        $this->gateway->setSignType('RSA2'); // RSA/RSA2/MD5
        $this->gateway->setAppId(env('ALIPAY_APP_ID'));
        $this->gateway->setPrivateKey(env('PRIVATE_KEY'));
        $this->gateway->setAlipayPublicKey(env('ALIPAY_PUBLIC_KEY'));
        $this->gateway->setReturnUrl(env('RETURN_URL'));
        $this->gateway->setNotifyUrl(env('NOTIFY_URL'));
    }
    //付款

    /**
     * @param $orderId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkout($orderId)
    {
        //具体信息可以从订单表查询拿到
        /**
         * @var AopTradePagePayResponse $response
         */
        $response = $this->gateway->purchase()->setBizContent([
            'subject'      => 'test',
            'out_trade_no' => $orderId,
            'total_amount' => '0.01',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ])->send();

        $url = $response->getRedirectUrl();

        return redirect($url);
    }

    /**
     * @return string
     */
    public function return()
    {
        return 'return';
    }

    /**
     *
     */
    public function notify()
    {
        $request = $this->gateway->completePurchase();
        $request->setParams(array_merge($_POST, $_GET)); //Don't use $_REQUEST for may contain $_COOKIE
        /**
         * @var AopCompletePurchaseResponse $response
         */
        try {
            $response = $request->send();
            if($response->isPaid()){
                /**
                 * Payment is successful
                 */
                info('success');
                die('success'); //The notify response should be 'success' only
            }else{
                /**
                 * Payment is not successful
                 */
                info('fail');
                die('fail'); //The notify response
            }
        } catch (Exception $e) {
            /**
             * Payment is not successful
             */
            info('fail');
            die('fail'); //The notify response
        }
    }
}