<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/mpay'], function (Router $router) {
// append
    $router->get('return', [
        'uses' => 'PublicController@return'
    ]);
    $router->post('notify', [
        'uses' => 'PublicController@notify'
    ]);

    $router->get('alipay',[
       'uses' => 'PublicController@alipay'
    ]);

});
