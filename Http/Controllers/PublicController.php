<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/4/28
 * Time: 16:24
 */

namespace Modules\Mpay\Http\Controllers;


use Modules\Core\Http\Controllers\BasePublicController;

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
}