<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class HostedPurchaseRequest
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedPurchaseRequest extends AbstractHostedRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validateData();

        $data = array (
            'certificateVerifyPeer' => false,
            'certificateVerifyHost' => 0,
            'gatewayUrl' => $this->getEndpoint('pay'),
            'merchantId' => $this->getMerchantId(),
            'apiUsername' => $this->getApiUsername(),
            'password' => $this->getPassword(),
            'debug' => false,
            'version' => $this->getVersion(),
            'proxyServer' => '',
            'proxyAuth' => '',
            'proxyCurlOption' => '',
            'proxyCurlValue' => '',
            'certificatePath' => '',

            //商户订单号
            'order_id'        => $this->getOrderId(),
            //交易金额，单位分
            'amount'         => $this->getAmount(),
            'checkout_method' => $this->getCheckoutMethod(),
            'currency' => $this->getCurrency(),
            'return_url' => $this->getReturnUrl()
        );

        return $data;
    }


    private function validateData()
    {
        $this->validate(
            'merchantId',
            'apiUsername',
            'password',
            'version',
            'order_id',
            'amount',
            'currency',
            'checkout_method',
            'return_url'
        );
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     *
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->response = new HostedPurchaseResponse($this, $data);
    }
}
