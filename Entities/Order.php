<?php

namespace Modules\Mpay\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    const COMPLETED = 1;
    const PENDING = 0;

    public $timestamps = true;
    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $guarded = [];
    //protected $fillable = ['transaction_id', 'order_id', 'amount', 'payment_status'];

    /**
     * @param Builder $query
     * @param string $transaction_id
     * @return mixed
     */
    public function scopeFindByTransactionId($query, $transaction_id)
    {
        return $query->where('transaction_id', $transaction_id);
    }

    /**
     * Payment completed.
     *
     * @return boolean
     */
    public function paid()
    {
        return in_array($this->payment_status, [self::COMPLETED]);
    }

    /**
     * Payment is still pending.
     *
     * @return boolean
     */
    public function unpaid()
    {
        return in_array($this->payment_status, [self::PENDING]);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class ,'order_id' ,'order_id' );
    }
    public function delivery()
    {
        return $this->hasOne(OrderDelivery::class ,'order_id' ,'order_id' );
    }
    public function product()
    {
        return $this->hasMany(OrderProduct::class ,'order_id' ,'order_id' );
    }
    public function supplier()
    {
        return $this->hasOne(OrderSupplier::class ,'order_id' ,'order_id' );
    }
}
