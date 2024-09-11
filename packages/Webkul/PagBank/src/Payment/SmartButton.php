<?php

namespace Webkul\PagBank\Payment;

use PagBankCheckoutSdk\Core\PagBankHttpClient;
use PagBankCheckoutSdk\Core\ProductionEnvironment;
use PagBankCheckoutSdk\Core\SandboxEnvironment;
use PagBankCheckoutSdk\Orders\OrdersCaptureRequest;
use PagBankCheckoutSdk\Orders\OrdersCreateRequest;
use PagBankCheckoutSdk\Orders\OrdersGetRequest;
use PagBankCheckoutSdk\Payments\CapturesRefundRequest;

class SmartButton extends PagBank
{
    /**
     * Client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * Client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'pagbank_smart_button';

    /**
     * PagBank partner attribution id.
     *
     * @var string
     */
    protected $pagbankPartnerAttributionId = 'Bagisto_Cart';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Returns PagBank HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke PagBank APIs, provided the
     * credentials have access.
     *
     * @return PagBankCheckoutSdk\Core\PagBankHttpClient
     */
    public function client()
    {
        return new PagBankHttpClient($this->environment());
    }

    /**
     * Create order for approval of client.
     *
     * @param  array  $body
     * @return HttpResponse
     */
    public function createOrder($body)
    {
        $request = new OrdersCreateRequest;
        $request->headers['PagBank-Partner-Attribution-Id'] = $this->pagbankPartnerAttributionId;
        $request->prefer('return=representation');
        $request->body = $body;

        return $this->client()->execute($request);
    }

    /**
     * Capture order after approval.
     *
     * @param  string  $orderId
     * @return HttpResponse
     */
    public function captureOrder($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);

        $request->headers['PagBank-Partner-Attribution-Id'] = $this->pagbankPartnerAttributionId;
        $request->prefer('return=representation');

        $this->client()->execute($request);
    }

    /**
     * Get order details.
     *
     * @param  string  $orderId
     * @return HttpResponse
     */
    public function getOrder($orderId)
    {
        return $this->client()->execute(new OrdersGetRequest($orderId));
    }

    /**
     * Get capture id.
     *
     * @param  string  $orderId
     * @return string
     */
    public function getCaptureId($orderId)
    {
        $pagbankOrderDetails = $this->getOrder($orderId);

        return $pagbankOrderDetails->result->purchase_units[0]->payments->captures[0]->id;
    }

    /**
     * Refund order.
     *
     * @return HttpResponse
     */
    public function refundOrder($captureId, $body = [])
    {
        $request = new CapturesRefundRequest($captureId);

        $request->headers['PagBank-Partner-Attribution-Id'] = $this->pagbankPartnerAttributionId;
        $request->body = $body;

        return $this->client()->execute($request);
    }

    /**
     * Return pagbank redirect url
     *
     * @return string
     */
    public function getRedirectUrl() {}

    /**
     * Set up and return PagBank PHP SDK environment with PagBank access credentials.
     * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
     *
     * @return PagBankCheckoutSdk\Core\SandboxEnvironment|PagBankCheckoutSdk\Core\ProductionEnvironment
     */
    protected function environment()
    {
        $isSandbox = $this->getConfigData('sandbox') ?: false;

        if ($isSandbox) {
            return new SandboxEnvironment($this->clientId, $this->clientSecret);
        }

        return new ProductionEnvironment($this->clientId, $this->clientSecret);
    }

    /**
     * Initialize properties.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->clientId = $this->getConfigData('client_id') ?: '';

        $this->clientSecret = $this->getConfigData('client_secret') ?: '';
    }
}
