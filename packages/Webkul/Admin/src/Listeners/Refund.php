<?php

namespace Webkul\Admin\Listeners;

use Webkul\Admin\Mail\Order\RefundedNotification;
use Webkul\PagBank\Payment\SmartButton as PagBankSmartButton;
use Webkul\Paypal\Payment\SmartButton as PayPalSmartButton;

class Refund extends Base
{
    /**
     * After order is created
     *
     * @param  \Webkul\Sales\Contracts\Refund  $refund
     * @return void
     */
    public function afterCreated($refund)
    {
        $this->refundOrder($refund);

        try {
            if (! core()->getConfigData('emails.general.notifications.emails.general.notifications.new_refund')) {
                return;
            }

            $this->prepareMail($refund, new RefundedNotification($refund));
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * After Refund is created
     *
     * @param  \Webkul\Sales\Contracts\Refund  $refund
     * @return void
     */
    public function refundOrder($refund)
    {
        $order = $refund->order;
        $smartButton = $this->getSmartButton($order->payment->method);

        if (isset($smartButton)) {
            /* getting oder id */
            $orderID = $order->payment->additional['orderID'];

            /* getting capture id by order id */
            $captureID = $smartButton->getCaptureId($orderID);

            /* now refunding order on the basis of capture id and refund data */
            $smartButton->refundOrder($captureID, [
                'amount' => [
                    'value'         => round($refund->grand_total, 2),
                    'currency_code' => $refund->order_currency_code,
                ],
            ]);
        }
    }

    /**
     * Getting smart button instance
     *
     * @param  \Webkul\Sales\Contracts\Refund  $method
     * @return null|PayPalSmartButton|PagBankSmartButton
     */
    private function getSmartButton($method)
    {
        $smartButton = null;

        if ($method === 'paypal_smart_button') {
            $smartButton = new PayPalSmartButton;
        } else if ($method === 'pagbank_smart_button') {
            $smartButton = new PagBankSmartButton;
        }

        return $smartButton;
    }
}
