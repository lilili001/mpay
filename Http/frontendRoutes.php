<?php

use Illuminate\Routing\Router;
/** @var Router $router */


$router->group(['prefix' =>'/mpay'], function (Router $router) {

// append
    $router->get('return', [
        'uses' => 'AlipayController@return'
    ]);
    $router->post('notify', [
        'uses' => 'AlipayController@notify'
    ]);

    $router->get('/',[
       'uses' => 'AlipayController@alipay'
    ]);
});

$router->group(['prefix' =>'/order'], function (Router $router) {
// append
    $router->get('save', [
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

Route::get('/orderdetail/{order?}', [
    'name' => 'PayPal Express Checkout',
    'as' => 'app.home',
    'uses' => 'PaypalController@form',
]);

Route::get('/orderdetail/jumping/{order?}', [
    'name' => 'PayPal Express Checkout',
    'as' => 'app.paypal.jump',
    'uses' => 'PaypalController@formjump',
]);

$router->post('/checkout/payment/{order}/paypal', [
    'name' => 'PayPal Express Checkout',
    'as' => 'checkout.payment.paypal',
    'uses' => 'PaypalController@checkout',
]);

Route::get('/paypal/checkout/{order}/completed', [
    'name' => 'PayPal Express Checkout',
    'as' => 'paypal.checkout.completed',
    'uses' => 'PaypalController@completed',
]);

Route::get('/paypal/checkout/{order}/cancelled', [
    'name' => 'PayPal Express Checkout',
    'as' => 'paypal.checkout.cancelled',
    'uses' => 'PaypalController@cancelled',
]);

Route::post('/webhook/paypal/{order?}/{env?}', [
    'name' => 'PayPal Express IPN',
    'as' => 'webhook.paypal.ipn',
    'uses' => 'PaypalController@webhook',
]);

