<?php

namespace Omnipay\MigsHostedCheckout\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\MigsHostedCheckout\Helper;

/**
 * Class HostedRefundRequest
 * @package Omnipay\MigsHostedCheckout\Message
 */
class HostedRefundRequest extends AbstractHostedRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('order_id', 'amount');

        $data = array (
            //商户订单号
            'order_id'        => $this->getOrderId(),
            //交易金额，单位分
            'amount'         => $this->getAmount()
        );

        $data = Helper::filterData($data);

        return $data;
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

        $data = $this->httpRequest('back', $data);

        return $this->response = new HostedResponse($this, $data);
    }
}
