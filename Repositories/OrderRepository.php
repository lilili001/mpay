<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/5/22
 * Time: 16:19
 */

namespace Modules\Mpay\Repositories;
use Illuminate\Support\Facades\DB;
use Modules\Product\Repositories\ShoppingCartRepository;
use Cart;

/**
 * Class OrderRepository
 * @package Modules\Mpay\Repositories
 */
class OrderRepository
{
    protected $shopCart;
    public function __construct( ShoppingCartRepository $shopCart )
    {
        $this->shopCart = $shopCart;
    }
    /**
     *生成订单
     */
    public function save($data)
    {
        info(  config('order.status')[1] );exit();
        $productItems = $this->shopCart->getCurrentUserCart(true);

        DB::transaction(function() use ($data,$productItems) {
            /****************创建 订单 （基础信息）**********************/
           $order = DB::table('orders')->insert([
                'order_id' => $this->StrOrderOne(),
                'amount'   => $this->getSelectedAmount(),
                'payment_gateway' => $data['order_payment_method'],
                'user_id'   => user()->id
            ]);

            /****************** 更新订单 产品表 ****************************/
            $pdcToInsert = [];
            foreach( $productItems as $key => $product ){
                $pdcToInsert[] = [
                    'item_id' => $product['id'],
                    'order_id' => $order->id,
                    'quantity' => $product['qty'],
                    'unit_price' => $product['unit_price'],
                    'subtotal' => $product['subtotal'],
                    'title' => $product['name'],
                    'options' =>  json_encode( $product['options'] ),
                    'pic_path' => $product['pic_path'],
                    'slug' => $product['slug'],
                ];
            }

            DB::table('order_item')->insert($pdcToInsert);

            /****************** 更新订单 地址表 ****************************/
            /*
             *  'id' => 12,
                'first_name' => 'carla',
                'last_name' => 'john',
                'email' => 'carlar@qq.com',
                'telephone' => '13472740661',
                'street' => 'address for cala john',
                'country' => 8,
                'country_label' => 'Albania',
                'city' => NULL,
                'city_label' => 'sdf',
                'state' => NULL,
                'state_label' => 'sdf',
                'zip' => 'sdf',
                'user_id' => 1,
                'is_default' => 1,
                'created_at' => '2018-05-22 07:12:30',
                'updated_at' => '2018-05-22 07:12:30',
             * */
            $address = $data['order_address'];
            DB::table('order_address')->insert([
                'order_id' => $order->id,
                'name'     => $address['first_name'] . ' ' .$address['last_name'],
                'telephone'=> $address['telephone'],
                'country'  => $address['country_label'],
                'state'    => $address['state_label'],
                'city'     => $address['city_label'],
                'street'   => $address['street'] ,
                'zipcode'  => $address['zipcode']
            ]);

            /****************** 订单操作信息表 ****************************/
            DB::table('order_operation')->insert([
                'order_id' => $order->id,
                'order_status' => config('order.status')[1] //订单状态
            ]);

            /****************** 购物车删除已下单产品 ****************************/
            foreach( $productItems as $item ){
                $this->shopCart->remove( $item['rowId'] );
            }
        });
    }
}