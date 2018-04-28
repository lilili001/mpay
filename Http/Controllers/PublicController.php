<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/4/28
 * Time: 16:24
 */

namespace Modules\Mpay\Http\Controllers;

use Modules\Core\Http\Controllers\BasePublicController;
use Omnipay;

class PublicController extends BasePublicController
{
    public function return()
    {
        return 'return';
    }

    public function notify()
    {
        return 'notify';
    }

    public function alipay()
    {
        $gateway = Omnipay\Omnipay::create('Alipay_AopPage');
        $gateway->setSignType('RSA2'); // RSA/RSA2/MD5
        $gateway->setAppId(env('ALIPAY_APP_ID'));
        $gateway->setPrivateKey(env('PRIVATE_KEY'));
        $gateway->setAlipayPublicKey(env('ALIPAY_PUBLIC_KEY'));
        $gateway->setReturnUrl(env('RETURN_URL'));
        $gateway->setNotifyUrl(env('NOTIFY_URL'));

        /**
         * @var AopTradePagePayResponse $response
         */
        $response = $gateway->purchase()->setBizContent([
            'subject'      => 'test',
            'out_trade_no' => date('YmdHis') . mt_rand(1000, 9999),
            'total_amount' => '0.01',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ])->send();

        $url = $response->getRedirectUrl();

        return redirect($url);
    }
}