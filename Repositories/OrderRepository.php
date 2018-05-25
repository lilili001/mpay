<?php
/**
 * Created by PhpStorm.
 * User: yixin
 * Date: 2018/5/22
 * Time: 16:19
 */

namespace Modules\Mpay\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Currency\Repositories\CurrencyRepository;
use Modules\Mpay\Entities\Order;
use Modules\Product\Repositories\ShoppingCartRepository;
use Cart;
use PayPal\Api\Amount;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;

/**
 * Class OrderRepository
 * @package Modules\Mpay\Repositories
 */
class OrderRepository
{
    protected $shopCart;
    protected $currency;
    public function __construct( ShoppingCartRepository $shopCart , CurrencyRepository $currency )
    {
        $this->shopCart = $shopCart;
        $this->currency = $currency;
    }
    /**
     *生成订单
     */
    public function save($data)
    {
        $productItems = $this->shopCart->getCurrentUserCart(true);
        info($productItems);
        try{
            $order_id = $this->shopCart->StrOrderOne();
            $exception = DB::transaction(function() use ( $data, $productItems, $order_id ) {

               $timestamp = Carbon::now();
               $rateList = $this->currency->getRateList();
                /****************创建 订单 （基础信息）**********************/
                DB::table('orders')->insert([
                    'order_id' => $order_id,
                    'transaction_id' => '',
                    'amount'   => $this->shopCart->getSelectedAmount(),
                    'amount_current_currency'  =>  $rateList[getCurrentCurrency()]['rate'] *  $this->shopCart->getSelectedAmount()  ,
                    'payment_gateway' => $data['order_payment_method'],
                    'user_id'   =>  user()->id,
                    'currency'  =>  getCurrentCurrency(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
                 
                /****************** 更新订单 产品表 ****************************/
                $pdcToInsert = [];
                foreach( $productItems as $key => $product ){
                    $pdcToInsert[] = [
                        'item_id' => $product['id'],
                        'order_id' => $order_id,
                        'quantity' => $product['qty'],
                        'unit_price' => $product['price'],
                        'unit_price_current_currency' => $rateList[getCurrentCurrency()]['rate'] * $product['price'] ,
                        'subtotal' => $product['subtotal'],
                        'subtotal_current_currency' => $rateList[getCurrentCurrency()]['rate'] * $product['subtotal'],
                        'title' => $product['name'],
                        'options' =>  json_encode( $product['options'] ),
                        'pic_path' => $product['options']['image'],
                        'slug' => $product['options']['slug'],
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }

                DB::table('order_item')->insert($pdcToInsert);

                /****************** 更新订单 地址表 ****************************/

                $address = $data['order_address'];
                DB::table('order_address')->insert([
                    'order_id' => $order_id,
                    'name'     => $address['first_name'] . ' ' .$address['last_name'],
                    'telephone'=> $address['telephone'],
                    'country'  => $address['country_label'],
                    'state'    => $address['state_label'],
                    'city'     => $address['city_label'],
                    'street'   => $address['street'] ,
                    'zipcode'  => $address['zip'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                /****************** 订单操作信息表 ****************************/
                DB::table('order_operation')->insert([
                    'order_id' => $order_id,
                    'order_status' => 1, //订单状态
                    'order_status_label' => config('order.status')[1], //订单状态
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                /****************** supplier 供应商信息表 ****************************/

                /****************** 购物车删除已下单产品 ****************************/
                foreach( $productItems as $item ){
                    //$this->shopCart->remove( $item['rowId'] );
                }
            });
            return is_null($exception) ? $order_id : $exception;
        }catch (Exception $e){
            info( 'Encounter en error for order placing ad below:'. $e->getMessage());
            return false;
        }
    }

    public function refund($saleId,$params)
    {
        //refund amount
        $amt = new Amount();
        $amt->setCurrency($params['currency'])
            ->setTotal($params['amount']);

        //refund Object
        $refundRequest = new RefundRequest();
        $refundRequest->setAmount($amt);

        //sale
        $sale = new Sale();
        $sale->setId($saleId);
        try {
            $refundedSale = $sale->refundSale($refundRequest, $this->paypalApiContext);
        } catch (Exception $ex) {
            return false;
            return AjaxResponse::fail('',$ex->getMessage());
        }
        return $refundedSale;
    }
}