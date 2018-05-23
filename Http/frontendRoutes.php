<?php

use Illuminate\Routing\Router;
/** @var Router $router */

//Order routes
$router->group(['prefix' =>'/order'], function (Router $router) {
    $router->post('save', [
        'uses' => 'OrderController@save',
        'as' => 'order.create'
    ]);
    $router->post('update', [
        'uses' => 'OrderController@update'
    ]);
    $router->get('delete',[
        'uses' => 'OrderController@update'
    ]);
});

//alipay routes
$router->group(['prefix' =>'/alipay'], function (Router $router) {
    $router->get('/checkout/{order}',[
        'uses' => 'AlipayController@checkout',
        'as' => 'alipay.checkout'
    ]);
    $router->get('return', [
        'uses' => 'AlipayController@return',
        'as' => 'alipay.return'
    ]);
    $router->post('notify', [
        'uses' => 'AlipayController@notify',
        'as' => 'alipay.notify'
    ]);
});
//paypal routes
Route::get('/orderdetail/{order?}', [
    'name' => 'PayPal Express Checkout',
    'as' => 'app.home',
    'uses' => 'PaypalController@form',
]);
Route::get('/paypal/checkout/{order}', [
    'name' => 'PayPal Express Checkout',
    'as' => 'checkout.payment.paypal',
    'uses' => 'PaypalController@checkout',
]);
Route::get('/paypal/{order}/completed', [
    'name' => 'PayPal Express Checkout',
    'as' => 'paypal.checkout.completed',
    'uses' => 'PaypalController@completed',
]);
Route::get('/paypal/{order}/cancelled', [
    'name' => 'PayPal Express Checkout',
    'as' => 'paypal.checkout.cancelled',
    'uses' => 'PaypalController@cancelled',
]);
Route::post('/paypal/webhook/{order?}/{env?}', [
    'name' => 'PayPal Express IPN',
    'as' => 'webhook.paypal.ipn',
    'uses' => 'PaypalController@webhook',
]);
//查询 paypal transaction
Route::get('/paypal/sale_detail/{transactionId}', [
    'name' => 'PayPal Express sale_detail',
    'as' => 'paypal.sale_detail',
    'uses' => 'PaypalController@sale_detail',
]);
//退款 refund
Route::get('/paypal/refund/{transactionId}',[
    'name' => 'PayPal Express refund',
    'as' => 'paypal.refund',
    'uses' => 'PaypalController@refund'
]);
