@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="gateway--info">
            <div class="gateway--desc">
                @if(session()->has('message'))
                    <p class="message">
                        {{ session('message') }}
                    </p>
                @endif
                <p><strong>Order Overview !</strong></p>
                <hr>
                <p>Item : Yearly Subscription cost !</p>
                <p>Amount :   {{ $order->currency . $order->amount_current_currency }}</p>
                <hr>
            </div>
            <div class="gateway--paypal">
                {{--<form method="POST" action="{{ route('checkout.payment.paypal', ['order' =>  encrypt($order->order_id)   ]) }}">--}}
                    {{--{{ csrf_field() }}--}}
                    {{--<button class="btn btn-pay">--}}
                        {{--<i class="fa fa-paypal" aria-hidden="true"></i> Pay with PayPal--}}
                    {{--</button>--}}
                {{--</form>--}}
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
 var session = "{{ session('message')  }}";
 if( !session ) location.href="/"
</script>
 @stop