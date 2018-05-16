@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="gateway--info">
            <div class="gateway--desc">
               跳转中...
            </div>
            <div class="gateway--paypal">
                <form method="POST" action="{{ route('checkout.payment.paypal', ['order' =>  encrypt($order->order_id)   ]) }}">
                    {{ csrf_field() }}
                    <button class="btn btn-pay">
                        <i class="fa fa-paypal" aria-hidden="true"></i> Pay with PayPal
                    </button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
     if( document.referrer.indexOf('checkout') !== -1 ){
         $('form').submit();
     }
</script>
 @stop