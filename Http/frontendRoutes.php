<?php

use Illuminate\Routing\Router;
/** @var Router $router */


$router->group(['prefix' =>'/mpay'], function (Router $router) {

    $router->get('/checkout/{order}',[
        'uses' => 'AlipayController@checkout',
        'as' => 'alipay.checkout'
    ]);

// append
    $router->get('return', [
        'uses' => 'AlipayController@return',
        'as' => 'alipay.return'
    ]);

    $router->post('notify', [
        'uses' => 'AlipayController@notify',
        'as' => 'alipay.notify'
    ]);
});

$router->group(['prefix' =>'/order'], function (Router $router) {
// append
    $router->get('save/{payment_method}', [
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

$router->get('/checkout/payment/{order}/paypal', [
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

Route::get('/transaction_search', [
    'name' => 'PayPal Express transaction_search',
    'as' => 'paypal.checkout.transaction_search',
    'uses' => 'PaypalController@transaction_search',
]);