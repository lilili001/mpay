<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/alipay'], function (Router $router) {
// append
    $router->get('return', [
        'uses' => 'PublicController@return'
    ]);
    $router->post('notify', [
        'uses' => 'PublicController@notify'
    ]);

    $router->get('/',[
       'uses' => 'PublicController@alipay'
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

Route::get('/order/{order?}', [
    'name' => 'PayPal Express Checkout',
    'as' => 'app.home',
    'uses' => 'PayPalController@form',
]);

$router->post('/checkout/payment/{order}/paypal', [
    'name' => 'PayPal Express Checkout',
    'as' => 'checkout.payment.paypal',
    'uses' => 'PayPalController@checkout',
]);

Route::get('/paypal/checkout/{order}/completed', [
    'name' => 'PayPal Express Checkout',
    'as' => 'paypal.checkout.completed',
    'uses' => 'PayPalController@completed',
]);

Route::get('/paypal/checkout/{order}/cancelled', [
    'name' => 'PayPal Express Checkout',
    'as' => 'paypal.checkout.cancelled',
    'uses' => 'PayPalController@cancelled',
]);

Route::post('/webhook/paypal/{order?}/{env?}', [
    'name' => 'PayPal Express IPN',
    'as' => 'webhook.paypal.ipn',
    'uses' => 'PayPalController@webhook',
]);