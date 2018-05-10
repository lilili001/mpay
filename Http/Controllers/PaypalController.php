<?php

namespace Modules\Mpay\Http\Controllers;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Mpay\Entities\Order;

use Illuminate\Http\Request;
use Modules\Mpay\Entities\PayPalIPN;
use Modules\Mpay\PayPal;
use Modules\Mpay\Repositories\IPNRepository;
use PayPal\IPN\Event\IPNInvalid;
use PayPal\IPN\Event\IPNVerified;
use PayPal\IPN\Listener\Http\ArrayListener;

/**
 * Class PayPalController
 * @package App\Http\Controllers
 */
class PayPalController extends BasePublicController
{
    protected $repository;
    public function __construct(IPNRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     */
    public function form(Request $request, $order_id = null)
    {
        $order_id = $order_id ? $order_id : encrypt(1);

        $order = Order::find(decrypt($order_id));

        return view('mpay::form', compact('order'));
    }

    /**
     * @param $order_id
     * @param Request $request
     */
    public function checkout($order_id, Request $request)
    {
        $order = Order::findOrFail(decrypt($order_id));

        $paypal = new PayPal;

        $response = $paypal->purchase([
            'amount' => $paypal->formatAmount(3),
            'transactionId' => $order->first()->id,
            'currency' => 'USD',
            'cancelUrl' => $paypal->getCancelUrl($order),
            'returnUrl' => $paypal->getReturnUrl($order),
        ]);

        if ($response->isRedirect()) {
            $response->redirect();
        }

        return redirect()->back()->with([
            'message' => $response->getMessage(),
        ]);
    }

    /**
     * @param $order_id
     * @param Request $request
     * @return mixed
     */
    public function completed($order_id, Request $request)
    {
        $order = Order::findOrFail($order_id);
        $paypal = new PayPal;

        $response = $paypal->complete([
            'amount' => $paypal->formatAmount(3),
            'transactionId' => $order->id,
            'currency' => 'USD',
            'cancelUrl' => $paypal->getCancelUrl($order),
            'returnUrl' => $paypal->getReturnUrl($order),
            'notifyUrl' => $paypal->getNotifyUrl($order),
        ]);

        if ($response->isSuccessful()) {

            $order->update([
                'transaction_id' => $response->getTransactionReference()
            ]);

            return redirect()->route('app.home', encrypt($order_id))->with([
                'message' => 'You recent payment is sucessful with reference code ' . $response->getTransactionReference(),
            ]);
        }

        return redirect()->back()->with([
            'message' => $response->getMessage(),
        ]);
    }

    /**
     * @param $order_id
     */
    public function cancelled($order_id)
    {
        $order = Order::findOrFail($order_id);

        return redirect()->route('app.home', encrypt($order_id))->with([
            'message' => 'You have cancelled your recent PayPal payment !',
        ]);
    }

    /**
     * @param $order_id
     * @param $env
     */
    /**
     * @param $order_id
     * @param $env
     * @param Request $request
     */
    public function webhook($order_id, $env, Request $request)
    {
        $listener = new ArrayListener;

        if ($env == 'sandbox') {
            $listener->useSandbox();
        }

        $listener->setData($request->all());

        $listener = $listener->run();

        $listener->onInvalid(function (IPNInvalid $event) use ($order_id) {
            $this->repository->handle($event, PayPalIPN::IPN_INVALID, $order_id);
        });

        $listener->onVerified(function (IPNVerified $event) use ($order_id) {
            $this->repository->handle($event, PayPalIPN::IPN_VERIFIED, $order_id);
        });

        $listener->onVerificationFailure(function (IPNVerificationFailure $event) use ($order_id) {
            $this->repository->handle($event, PayPalIPN::IPN_FAILURE, $order_id);
        });

        $listener->listen();
    }
}