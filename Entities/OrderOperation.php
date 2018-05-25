<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/5/24
 * Time: 14:24
 */

namespace Modules\Mpay\Entities;


use Illuminate\Database\Eloquent\Model;

class OrderOperation extends Model
{
    protected $table = 'order_operation';
    protected $guarded = [];
}