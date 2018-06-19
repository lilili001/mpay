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
                <div>
                    <h4>Items:</h4>
                    @foreach( $order->product as $product )
                        <div class="media">
                              <div class="media-left">
                                    <a href="#">
                                      <img class="media-object" src="{{  $product->pic_path  }}" alt="...">
                                    </a>
                              </div>
                              <div class="media-body">
                                    <h4 class="media-heading">{{ $product->title }}</h4>
                                    <div>
                                        <?php
                                            $options = json_decode( $product->options )
                                        ?>
                                        @foreach(  $options->selectedItemLocale as  $key=>$option )
                                            <span>{{ $key .':'. $option  }}</span>
                                        @endforeach
                                    </div>
                              </div>
                        </div>
                    @endforeach
                </div>

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