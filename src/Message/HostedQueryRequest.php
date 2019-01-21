<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class HostedQueryRequest
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedQueryRequest extends AbstractHostedRequest
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
        $data['is_paid'] = false;

        $request_assoc_array = array(
            "apiOperation"=>"RETRIEVE_ORDER",
            "order.id"=>$data['order_id']
        );

        $request = Helper::ParseRequest($data, $request_assoc_array);
        $response = Helper::SendTransaction($data['gatewayUrl'], $data, $request);

        $parsed_array = Helper::parse_from_nvp($response);

        if ($parsed_array['result'] === "SUCCESS"
            && $parsed_array['status'] === "CAPTURED"
            && $parsed_array['totalCapturedAmount'] === $data['amount']
            && $parsed_array['totalRefundedAmount'] === "0.00") {
            $data['is_paid'] = true;
        }

        unset($_SESSION['orderID']);
        unset($_SESSION['successIndicator']);
        unset($_SESSION['sessionId']);

        $data = array_merge($data, $parsed_array);

        return $data;
    }
}
