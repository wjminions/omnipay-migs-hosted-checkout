<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class HostedCompletePurchaseRequest
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedCompletePurchaseRequest extends AbstractHostedRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $request_params = $this->getRequestParams();

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

            'currency' => $this->getCurrency(),
            'resultIndicator' => isset($request_params['resultIndicator']) ? $request_params['resultIndicator'] : false,
            'sessionVersion' => isset($request_params['sessionVersion']) ? $request_params['sessionVersion'] : false,
            'amount' => $request_params['amount'],
            'return_url' => $this->getReturnUrl()
        );

        return $data;
    }


    public function setRequestParams($value)
    {
        $this->setParameter('request_params', $value);
    }


    public function getRequestParams()
    {
        return $this->getParameter('request_params');
    }


    public function getRequestParam($key)
    {
        $params = $this->getRequestParams();
        if (isset($params[$key])) {
            return $params[$key];
        } else {
            return null;
        }
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

        if ($data["resultIndicator"] && $_SESSION['successIndicator']) {
            if (strcmp($data["resultIndicator"], $_SESSION['successIndicator']) == 0) {
                $orderID = $_SESSION['orderID'];

                $request_assoc_array = array(
                    "apiOperation"=>"CAPTURE",
                    "order.id"=>$orderID,
                    "transaction.id" => $orderID . '_capture',
                    "transaction.amount" => $data['amount'],
                    "transaction.currency" => $data['currency']
                );

                $request = Helper::ParseRequest($data, $request_assoc_array);
                $response = Helper::SendTransaction($data['gatewayUrl'], $data, $request);

                $request_assoc_array = array(
                    "apiOperation"=>"RETRIEVE_ORDER",
                    "order.id"=>$orderID
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

                $data = array_merge($data, $parsed_array);
            }
        }

        unset($_SESSION['orderID']);
        unset($_SESSION['successIndicator']);
        unset($_SESSION['sessionId']);

        return $this->response = new HostedCompletePurchaseResponse($this, $data);
    }
}
