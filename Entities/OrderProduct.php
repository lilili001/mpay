<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/5/23
 * Time: 14:45
 */

namespace Modules\Mpay\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_item';
    protected $guarded = [];
}