<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/5/23
 * Time: 14:45
 */

namespace Modules\Mpay\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    protected $table = 'order_shipping';
    protected $guarded = [];

    public function tracking()
    {
        return $this->hasOne(OrderDeliveryTracking::class,'tracking_number','tracking_number');
    }
}