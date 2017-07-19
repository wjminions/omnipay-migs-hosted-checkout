<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class HostedVoidRequest
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedVoidRequest extends AbstractHostedRequest
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
            'refund_order'        => $this->getRefundOrder(),
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
            'return_url',
            'refund_order'
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
            "apiOperation"=>"VOID",
            "order.id"=>$data['order_id'],
            "transaction.id" => $data['refund_order'],
            "transaction.targetTransactionId" => $data['order_id'] . '_capture'
        );

        $request = Helper::ParseRequest($data, $request_assoc_array);
        $response = Helper::SendTransaction($data['gatewayUrl'], $data, $request);

        $parsed_array = Helper::parse_from_nvp($response);

        if ($parsed_array['result'] === "SUCCESS" && isset($parsed_array['authorizationResponse.responseCode']) && $parsed_array['authorizationResponse.responseCode'] === "00") {
            unset($data);
            $data['is_paid'] = true;
        }

        $data = array_merge($data, $parsed_array);

        return $this->response = new HostedRefundResponse($this, $data);
    }
}
